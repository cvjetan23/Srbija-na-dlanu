<?php
defined('ABSPATH') || exit;

final class SND_API {
    public static function register() {
        register_rest_route('srbija-na-dlanu/v1', '/bootstrap', array('methods' => 'GET', 'callback' => array(__CLASS__, 'bootstrap'), 'permission_callback' => '__return_true'));
        foreach (array('start', 'resume', 'action') as $route) {
            register_rest_route('srbija-na-dlanu/v1', '/' . $route, array('methods' => 'POST', 'callback' => array(__CLASS__, $route), 'permission_callback' => '__return_true'));
        }
    }

    private static function response($data) {
        $r = new WP_REST_Response($data);
        $r->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $r->header('X-Robots-Tag', 'noindex');
        return $r;
    }

    private static function limit($bucket, $maximum) {
        $ip = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : 'unknown';
        $key = 'snd_rate_' . substr(hash_hmac('sha256', $bucket . ':' . $ip . ':' . floor(time() / 600), wp_salt('nonce')), 0, 32);
        $n = (int) get_transient($key);
        if ($n >= $maximum) { return new WP_Error('snd_rate', 'Previše zahteva. Sačekaj nekoliko minuta pa pokušaj ponovo.', array('status' => 429)); }
        set_transient($key, $n + 1, 11 * MINUTE_IN_SECONDS);
        return true;
    }

    public static function bootstrap() {
        $data = SND_Data::get(); $date = SND_Game::date();
        $tomorrow = new DateTimeImmutable('tomorrow', new DateTimeZone('Europe/Belgrade'));
        $outline = array(); foreach (SND_Data::pool('district') as $t) { $outline[] = $t['map_path']; }
        return self::response(array('date' => $date, 'next_change' => $tomorrow->getTimestamp(), 'server_time' => time(), 'counts' => array('district' => count(SND_Data::pool('district')), 'municipality' => count(SND_Data::pool('municipality'))), 'coverage' => $data['coverage'], 'sources' => $data['sources'], 'outline' => $outline, 'version' => SND_VERSION));
    }

    public static function start($request) {
        $limit = self::limit('start', 60); if (is_wp_error($limit)) { return $limit; }
        $kind = $request->get_param('kind'); $mode = $request->get_param('mode');
        if (!in_array($kind, array('district', 'municipality'), true) || !in_array($mode, array('daily', 'practice'), true)) { return new WP_Error('snd_mode', 'Nepoznata igra ili režim.', array('status' => 400)); }
        $r = SND_Game::create($kind, $mode);
        return is_wp_error($r) ? $r : self::response($r);
    }

    public static function resume($request) {
        $limit = self::limit('resume', 240); if (is_wp_error($limit)) { return $limit; }
        $s = SND_Game::load($request->get_param('token')); if (is_wp_error($s)) { return $s; }
        return self::response(array('token' => $request->get_param('token'), 'state' => SND_Game::public_state($s)));
    }

    public static function action($request) {
        $limit = self::limit('action', 240); if (is_wp_error($limit)) { return $limit; }
        $token = $request->get_param('token');
        $s = SND_Game::load($token); if (is_wp_error($s)) { return $s; }
        $key = 'snd_lock_' . $token;
        // add_option has a unique DB key; a second request cannot consume a turn concurrently.
        $locked_at = (int) get_option($key, 0);
        if ($locked_at && $locked_at < time() - 30) { delete_option($key); }
        if (!add_option($key, time(), '', false)) { return new WP_Error('snd_busy', 'Prethodni potez se još obrađuje. Pokušaj ponovo.', array('status' => 409)); }
        try {
            $s = SND_Game::load($token); if (is_wp_error($s)) { return $s; }
            if (!is_numeric($request->get_param('revision')) || (int) $request->get_param('revision') !== $s['revision']) { return new WP_Error('snd_stale', 'Partija je promenjena u drugom prozoru. Prikaz će biti osvežen.', array('status' => 409)); }
            $r = SND_Game::act($s, $request->get_param('action'), $request->get_param('value'));
            if (is_wp_error($r)) { return $r; }
            SND_Game::persist($token, $s);
            return self::response(array('token' => $token, 'state' => SND_Game::public_state($s)));
        } finally { delete_option($key); }
    }
}
