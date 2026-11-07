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
    public static function ajax_request($route,$payload,$nonce='') {
        if(!in_array($route,array('bootstrap','start','resume','action','claim'),true)){return new WP_REST_Response(array('code'=>'snd_route','message'=>'Nepoznata akcija.'),400);}
        if(is_user_logged_in() && !wp_verify_nonce($nonce,'wp_rest')){return new WP_REST_Response(array('code'=>'rest_cookie_invalid_nonce','message'=>SND_I18n::t('Prijava je istekla. Osveži stranicu i ponovo se prijavi.')),403);}
        if(!is_string($payload) || strlen($payload)>16384 || !is_array(json_decode($payload,true))){return new WP_REST_Response(array('code'=>'snd_payload','message'=>'Neispravan zahtev.'),400);}
        $request=new WP_REST_Request($route==='bootstrap'?'GET':'POST','/srbija-na-dlanu/v1/'.$route);
        $request->set_header('Content-Type','application/json');$request->set_body($payload);
        if($nonce){$request->set_header('X-WP-Nonce',$nonce);}
        $response=rest_ensure_response(rest_do_request($request));
        return apply_filters('rest_post_dispatch',$response,rest_get_server(),$request);
    }
    public static function ajax() {
        nocache_headers();$origin=$_SERVER['HTTP_ORIGIN']??'';
        if($origin && wp_parse_url($origin,PHP_URL_HOST)!==wp_parse_url(home_url(),PHP_URL_HOST)){wp_send_json(array('code'=>'snd_origin','message'=>'Neispravan zahtev.'),403);}
        $route=isset($_POST['route']) && is_string($_POST['route'])?sanitize_key(wp_unslash($_POST['route'])):'';
        $payload=isset($_POST['payload']) && is_string($_POST['payload'])?wp_unslash($_POST['payload']):'';
        $nonce=isset($_POST['_wpnonce']) && is_string($_POST['_wpnonce'])?wp_unslash($_POST['_wpnonce']):'';
        $response=self::ajax_request($route,$payload,$nonce);
        header('Content-Language: '.SND_I18n::html_lang());wp_send_json($response->get_data(),$response->get_status());
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
        $id=$request->get_param('request_id');
        if(!is_string($id) || !preg_match('/^[a-f0-9]{32}$/D',$id)){return self::start_game($request);}
        $key='snd_start_'.hash('sha256',wp_json_encode(array(get_current_user_id(),$id,$request->get_param('kind'),$request->get_param('mode'))));
        $lease=SND_Lock::acquire($key.'_lock');if(!$lease){return new WP_Error('snd_busy','Partija se još priprema. Pokušaj ponovo.',array('status'=>409));}
        try{
            $token=get_transient($key);if($token){$state=SND_Game::load($token);if(!is_wp_error($state) && $state['challenge']['kind']===$request->get_param('kind') && $state['challenge']['mode']===$request->get_param('mode')){return self::game_response($token,$state);}}
            $result=self::start_game($request);if($result instanceof WP_REST_Response){$data=$result->get_data();if(!empty($data['token'])){set_transient($key,$data['token'],10*MINUTE_IN_SECONDS);}}return $result;
        }finally{SND_Lock::release($key.'_lock',$lease);}
    }
    private static function start_game($request) {
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
