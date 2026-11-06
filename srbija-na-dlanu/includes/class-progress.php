<?php
defined('ABSPATH') || exit;

final class SND_Progress {
    public static function get($user_id) {
        $p = get_user_meta($user_id, 'snd_progress_v2', true);
        if (!is_array($p)) { $p = array(); }
        foreach (array('district', 'municipality') as $kind) {
            $p[$kind] = array_merge(array('days' => array(), 'discovered' => array(), 'practice_count' => 0, 'completed_games' => array(), 'quiz_correct' => 0, 'quiz_total' => 0), isset($p[$kind]) && is_array($p[$kind]) ? $p[$kind] : array());
        }
        return $p;
    }
    public static function slot($kind, $mode) { return 'snd_active_v2_' . $kind . '_' . $mode; }
    public static function active($user_id, $kind, $mode) {
        $token = get_user_meta($user_id, self::slot($kind, $mode), true);
        if (!$token) { return null; }
        $s = SND_Game::load($token);
        if (is_wp_error($s) || (int) ($s['owner'] ?? 0) !== (int) $user_id) { return null; }
        return array('token' => $token, 'state' => $s);
    }
    public static function remember($user_id, $kind, $mode, $token) {
        if ($user_id) { update_user_meta($user_id, self::slot($kind, $mode), $token); }
    }
    public static function record(&$state) {
        $user_id = (int) ($state['owner'] ?? 0);
        if (!$user_id || $state['stage'] !== 'complete' || !empty($state['recorded'])) { return true; }
        $lock = 'snd_progress_lock_' . $user_id;
        $lease=SND_Lock::acquire($lock);
        if (!$lease) { return new WP_Error('snd_progress_busy', 'Rezultat se još čuva. Pokušaj ponovo.', array('status' => 409)); }
        try {
            $all = self::get($user_id); $c = $state['challenge']; $p = &$all[$c['kind']];
            $id = $state['game_id'] ?? hash('sha256', wp_json_encode(array($c['date'], $c['target']['id'], $state['guesses'], $state['answers'])));
            if (in_array($id, $p['completed_games'], true)) { $state['recorded'] = true; return true; }
            $correct = count(array_filter($state['answers'], function ($a) { return !empty($a['correct']); }));
            $result = array('date' => $c['date'], 'territory' => $c['target']['id'], 'name' => $c['target']['name'], 'solved' => $state['solved'], 'guesses' => count($state['guesses']), 'score' => $correct, 'question_count' => count($c['questions']), 'hint' => $state['hint'], 'ruleset' => $c['ruleset'] ?? 1);
            $count = true;
            if ($c['mode'] === 'daily') {
                if (isset($p['days'][$c['date']])) {
                    $count = false;
                    if (($p['days'][$c['date']]['ruleset'] ?? 1) < $result['ruleset']) {
                        $p['quiz_correct'] += $correct - $p['days'][$c['date']]['score'];
                        $p['quiz_total'] += count($c['questions']) - $p['days'][$c['date']]['question_count'];
                        $p['days'][$c['date']] = $result;
                    }
                } else { $p['days'][$c['date']] = $result; }
            } else { $p['practice_count']++; }
            if ($count) { $p['quiz_correct'] += $correct; $p['quiz_total'] += count($c['questions']); }
            if (!in_array($c['target']['id'], $p['discovered'], true)) { $p['discovered'][] = $c['target']['id']; }
            $p['completed_games'][] = $id; $p['completed_games'] = array_slice($p['completed_games'], -300);
            if(!update_user_meta($user_id,'snd_progress_v2',$all)){return new WP_Error('snd_progress_save','Rezultat nije mogao da se sačuva. Pokušaj ponovo.',array('status'=>503));}
            $state['recorded'] = true;
            return true;
        } finally { SND_Lock::release($lock,$lease); }
    }
    public static function summary($user_id) {
        $all = self::get($user_id); $summary = array();
        foreach ($all as $kind => $p) {
            $day = SND_Game::date(); $streak = 0;
            if (!isset($p['days'][$day])) { $day = (new DateTimeImmutable($day))->modify('-1 day')->format('Y-m-d'); }
            while (isset($p['days'][$day])) { $streak++; $day = (new DateTimeImmutable($day))->modify('-1 day')->format('Y-m-d'); }
            $summary[$kind] = array('days' => array_keys($p['days']), 'discovered' => $p['discovered'], 'daily_count' => count($p['days']), 'practice_count' => $p['practice_count'], 'quiz_correct' => $p['quiz_correct'], 'quiz_total' => $p['quiz_total'], 'streak' => $streak, 'recent' => array_values(array_slice(array_reverse($p['days']), 0, 5)));
        }
        return $summary;
    }
    public static function ajax_session() {
        nocache_headers();
        $id = get_current_user_id();
        $out = array('authenticated' => (bool) $id, 'user' => null, 'nonce' => '', 'progress' => null, 'active' => array());
        if ($id) {
            $u = wp_get_current_user(); $out['user'] = array('id' => $id, 'name' => $u->display_name);
            $out['nonce'] = wp_create_nonce('wp_rest'); $out['progress'] = self::summary($id);
            foreach (array('district', 'municipality') as $kind) { foreach (array('daily', 'practice') as $mode) {
                $active = self::active($id, $kind, $mode);
                if ($active) { $out['active'][$kind . ':' . $mode] = $active['token']; }
            } }
        }
        wp_send_json($out);
    }
}
