<?php
defined('ABSPATH') || exit;

final class SND_API {
    public static function register() {
        register_rest_route('srbija-na-dlanu/v1','/activity',array('methods'=>'POST','callback'=>array('SND_Analytics','activity'),'permission_callback'=>'__return_true'));
        register_rest_route('srbija-na-dlanu/v1', '/bootstrap', array('methods' => 'GET', 'callback' => array(__CLASS__, 'bootstrap'), 'permission_callback' => '__return_true'));
        foreach (array('start', 'resume', 'action') as $route) {
            register_rest_route('srbija-na-dlanu/v1', '/' . $route, array('methods' => 'POST', 'callback' => array(__CLASS__, $route), 'permission_callback' => '__return_true'));
        }
        register_rest_route('srbija-na-dlanu/v1', '/claim', array('methods'=>'POST','callback'=>array(__CLASS__,'claim'),'permission_callback'=>function(){ return is_user_logged_in(); }));
        register_rest_route('srbija-na-dlanu/v1', '/admin/setup', array('methods'=>'POST','callback'=>function(){ $r=SND_Site::install(); return is_wp_error($r)?$r:SND_API::response(array('pages'=>$r)); },'permission_callback'=>function(){ return current_user_can('manage_options'); }));
    }

    public static function response($data) {
        $r = new WP_REST_Response($data);
        $r->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $r->header('X-Robots-Tag', 'noindex');
        return $r;
    }

    private static function limit($bucket, $maximum) {
        if(!SND_Rate::allow('game:'.$bucket,$maximum,600,SND_Rate::actor(),max(6000,$maximum*50))){
            return new WP_Error('snd_rate','Previše zahteva. Sačekaj nekoliko minuta pa pokušaj ponovo.',array('status'=>429));
        }
        return true;
    }

    public static function bootstrap() {
        $data = SND_Data::get(); $date = SND_Game::date();
        $tomorrow = new DateTimeImmutable('tomorrow', new DateTimeZone('Europe/Belgrade'));
        $outline = array(); foreach (SND_Data::pool('district') as $t) { $outline[] = $t['map_path']; }
        return self::response(array('date' => $date, 'next_change' => $tomorrow->getTimestamp(), 'server_time' => time(), 'counts' => array('district' => count(SND_Data::pool('district')), 'municipality' => count(SND_Data::pool('municipality'))), 'coverage' => $data['coverage'], 'sources' => $data['sources'], 'outline' => $outline, 'version' => SND_VERSION,'config'=>SND_Settings::public_config()));
    }

    public static function start($request) {
        $limit = self::limit('start', 60); if (is_wp_error($limit)) { return $limit; }
        $kind = $request->get_param('kind'); $mode = $request->get_param('mode');
        if (!in_array($kind, array('district', 'municipality'), true) || !in_array($mode, array('daily', 'practice'), true)) { return new WP_Error('snd_mode', 'Nepoznata igra ili režim.', array('status' => 400)); }
        if(!SND_Settings::get($kind.'_enabled') || !SND_Settings::get($mode.'_enabled')){return new WP_Error('snd_disabled','Nove partije ovog tipa trenutno nisu dostupne.',array('status'=>503));}
        $lock = is_user_logged_in() ? 'snd_start_lock_'.get_current_user_id().'_'.$kind.'_'.$mode : '';
        $lease=$lock?SND_Lock::acquire($lock):true;
        if (!$lease) { return new WP_Error('snd_busy','Partija se još priprema. Pokušaj ponovo.',array('status'=>409)); }
        try {
            if (is_user_logged_in() && ($mode === 'daily' || !$request->get_param('fresh'))) {
                $active = SND_Progress::active(get_current_user_id(), $kind, $mode);
                if ($active) { return self::game_response($active['token'], $active['state']); }
            }
            $r = SND_Game::create($kind, $mode);
            if (!is_wp_error($r) && is_user_logged_in()) { $r['progress'] = SND_Progress::summary(get_current_user_id()); }
            return is_wp_error($r) ? $r : self::response($r);
        } finally { if ($lock) { SND_Lock::release($lock,$lease); } }
    }

    public static function resume($request) {
        $limit = self::limit('resume', 240); if (is_wp_error($limit)) { return $limit; }
        $s = SND_Game::load($request->get_param('token')); if (is_wp_error($s)) { return $s; }
        // A read must not overwrite a simultaneous action with an older revision.
        if ($s['stage']==='complete' && !empty($s['owner']) && empty($s['recorded'])) {
            $key='snd_lock_'.$request->get_param('token');
            $lease=SND_Lock::acquire($key);
            if (!$lease) { return new WP_Error('snd_busy','Rezultat se još čuva. Pokušaj ponovo.',array('status'=>409)); }
            try {
                $s=SND_Game::load($request->get_param('token')); if(is_wp_error($s)){return $s;}
                $record=SND_Progress::record($s);if(is_wp_error($record)){return $record;}
                SND_Game::persist($request->get_param('token'),$s);
            } finally {SND_Lock::release($key,$lease);}
        }
        return self::game_response($request->get_param('token'), $s);
    }

    public static function action($request) {
        $limit = self::limit('action', 240); if (is_wp_error($limit)) { return $limit; }
        $token = $request->get_param('token');
        $s = SND_Game::load($token); if (is_wp_error($s)) { return $s; }
        $key = 'snd_lock_' . $token;
        $lease=SND_Lock::acquire($key);
        if (!$lease) { return new WP_Error('snd_busy', 'Prethodni potez se još obrađuje. Pokušaj ponovo.', array('status' => 409)); }
        try {
            $s = SND_Game::load($token); if (is_wp_error($s)) { return $s; }
            if (!is_numeric($request->get_param('revision')) || (int) $request->get_param('revision') !== $s['revision']) { return new WP_Error('snd_stale', 'Partija je promenjena u drugom prozoru. Prikaz će biti osvežen.', array('status' => 409)); }
            $r = SND_Game::act($s, $request->get_param('action'), $request->get_param('value'));
            if (is_wp_error($r)) { return $r; }
            $record = SND_Progress::record($s); if (is_wp_error($record)) { return $record; }
            SND_Game::persist($token, $s);
            return self::game_response($token, $s);
        } finally { SND_Lock::release($key,$lease); }
    }
    private static function game_response($token, $state) {
        SND_Analytics::capture($state);
        $out = array('token'=>$token,'state'=>SND_Game::public_state($state));
        if (get_current_user_id()) { $out['progress']=SND_Progress::summary(get_current_user_id()); }
        return self::response($out);
    }
    public static function claim($request) {
        $token = $request->get_param('token'); $s=SND_Game::load($token); if(is_wp_error($s)){return $s;}
        if (!empty($s['owner'])) { return self::game_response($token,$s); }
        $c=$s['challenge'];$slot_lock='snd_start_lock_'.get_current_user_id().'_'.$c['kind'].'_'.$c['mode'];$slot_lease=SND_Lock::acquire($slot_lock);
        if(!$slot_lease){return new WP_Error('snd_busy','Partija se još priprema. Pokušaj ponovo.',array('status'=>409));}
        try {
        $active=SND_Progress::active(get_current_user_id(),$c['kind'],$c['mode']);
        if($active){return self::game_response($active['token'],$active['state']);}
        $lock='snd_lock_'.$token;
        $lease=SND_Lock::acquire($lock);
        if(!$lease){return new WP_Error('snd_busy','Partija se još čuva. Pokušaj ponovo.',array('status'=>409));}
        try {
            $s=SND_Game::load($token);if(is_wp_error($s)){return $s;}
            $s['owner']=get_current_user_id();
            $s['privacy_epoch']=get_user_meta(get_current_user_id(),'snd_privacy_epoch',true);
            $record=SND_Progress::record($s);if(is_wp_error($record)){return $record;}
            SND_Game::persist($token,$s);SND_Progress::remember(get_current_user_id(),$c['kind'],$c['mode'],$token);
            return self::game_response($token,$s);
        } finally {SND_Lock::release($lock,$lease);}
        } finally {SND_Lock::release($slot_lock,$slot_lease);}
    }
}
