<?php
defined('ABSPATH') || exit;

final class SND_Game {
    const MAX_GUESSES = 6;
    const RULESET = 2;

    public static function date() { return (new DateTimeImmutable('now', new DateTimeZone('Europe/Belgrade')))->format('Y-m-d'); }

    public static function ordered($items, $seed) {
        $items = array_values($items);
        usort($items, function ($a, $b) use ($seed) {
            $a = is_array($a) ? ($a['id'] ?? wp_json_encode($a)) : (string) $a;
            $b = is_array($b) ? ($b['id'] ?? wp_json_encode($b)) : (string) $b;
            return strcmp(hash('sha256', $seed . '|' . $a), hash('sha256', $seed . '|' . $b));
        });
        return $items;
    }

    public static function distance($a, $b) {
        $la = deg2rad($a[0]); $lb = deg2rad($b[0]);
        $v = pow(sin(($lb - $la) / 2), 2) + cos($la) * cos($lb) * pow(sin(deg2rad($b[1] - $a[1]) / 2), 2);
        return 6371.0088 * 2 * atan2(sqrt(min(1, $v)), sqrt(max(0, 1 - $v)));
    }

    public static function direction($from, $to) {
        $la = deg2rad($from[0]); $lb = deg2rad($to[0]); $dl = deg2rad($to[1] - $from[1]);
        $angle = fmod(rad2deg(atan2(sin($dl) * cos($lb), cos($la) * sin($lb) - sin($la) * cos($lb) * cos($dl))) + 360, 360);
        $i = ((int) floor(($angle + 22.5) / 45)) % 8;
        $names = array('sever', 'severoistok', 'istok', 'jugoistok', 'jug', 'jugozapad', 'zapad', 'severozapad');
        $arrows = array('↑', '↗', '→', '↘', '↓', '↙', '←', '↖');
        return array('name' => $names[$i], 'arrow' => $arrows[$i]);
    }

    private static function choice_question($type, $title, $correct, $other, $seed, $explanation, $source = '') {
        $other = array_values(array_unique(array_filter($other, function ($x) use ($correct) { return $x !== $correct && $x !== ''; })));
        $values = array_merge(array($correct), array_slice(self::ordered($other, $seed . ':decoys'), 0, 3));
        $options = array();
        foreach ($values as $value) { $options[] = array('id' => substr(hash('sha256', $seed . ':' . $value), 0, 16), 'label' => $value); }
        return array('type' => $type, 'title' => $title, 'options' => self::ordered($options, $seed . ':order'), 'correct' => $options[0]['id'], 'explanation' => $explanation, 'source' => $source);
    }

    public static function questions($target, $pool, $districts, $seed) {
        $q = array();
        if (!empty($target['population'])) {
            $round = function ($n) { return (int) (round($n / ($n >= 10000 ? 1000 : 100)) * ($n >= 10000 ? 1000 : 100)); };
            $correct = $round($target['population']); $decoys = array();
            foreach ($pool as $t) {
                if (!empty($t['population']) && abs($t['population'] / $target['population'] - 1) >= .25) {
                    $decoys[] = 'Oko ' . number_format($round($t['population']), 0, ',', '.');
                }
            }
            if (count(array_unique($decoys)) < 3) {
                foreach (array(.3, .5, 1.6, 2.5, 4) as $factor) { $decoys[] = 'Oko ' . number_format($round($target['population'] * $factor), 0, ',', '.'); }
            }
            $q[] = self::choice_question('population', 'Koliko približno stanovnika ima cela teritorija?', 'Oko ' . number_format($correct, 0, ',', '.'), $decoys, $seed . ':population', 'Prema Popisu ' . $target['year'] . ', cela teritorija ima ' . number_format($target['population'], 0, ',', '.') . ' stanovnika. Podatak se ne odnosi samo na naselje.', $target['population_source']);
            $q[0]['subtitle'] = 'Popis ' . $target['year'] . ' · stanovništvo cele teritorije';
        } else {
            $anchors = array_filter($pool, function ($t) use ($target) { return $t['id'] !== $target['id'] && self::distance($t['center'], $target['center']) > 10; });
            if (!$anchors) { $anchors = array_filter($pool, function ($t) use ($target) { return $t['id'] !== $target['id']; }); }
            $anchors = self::ordered($anchors, $seed . ':anchor'); $anchor = $anchors[0];
            $direction = self::direction($target['center'], $anchor['center']);
            $reason = $target['region'] === 'Kosovo i Metohija' ? 'Popis RZS 2022. nije sproveden na teritoriji Kosova i Metohije; zato je ovde geografsko pitanje.' : 'Za ovu teritoriju u bazi nije naveden uporediv podatak o stanovništvu, pa je postavljeno geografsko pitanje.';
            $q[] = self::choice_question('orientation', 'U kom smeru je centar teritorije „' . $anchor['name'] . '“ u odnosu na ovu teritoriju?', ucfirst($direction['name']), array('Sever', 'Severoistok', 'Istok', 'Jugoistok', 'Jug', 'Jugozapad', 'Zapad', 'Severozapad'), $seed . ':orientation', 'Od centra teritorije ' . $target['name'] . ' ka centru teritorije ' . $anchor['name'] . ' kreće se u smeru: ' . $direction['name'] . '. ' . $reason, $target['region'] === 'Kosovo i Metohija' ? 'https://kro.mre.gov.rs/arcgis/rest/services/IMIS/MINING_PUBLIC/MapServer' : 'https://www.geoboundaries.org/');
        }
        if ($target['kind'] === 'district' && in_array($target['id'], array('rs-25', 'rs-26', 'rs-27', 'rs-28', 'rs-29'), true)) {
            $municipalities = SND_Data::pool('municipality'); $inside = array(); $outside = array();
            foreach ($municipalities as $m) { if ($m['district_id'] === $target['id']) { $inside[] = $m['name']; } else { $outside[] = $m['name']; } }
            $inside = self::ordered($inside, $seed . ':member');
            $q[] = self::choice_question('membership', 'Koja od ovih opština pripada ovom okrugu?', $inside[0], $outside, $seed . ':membership', 'Opština ' . $inside[0] . ' pripada celini: ' . $target['name'] . ', prema podeli na pet upravnih okruga na Kosovu i Metohiji.', 'https://kro.mre.gov.rs/arcgis/rest/services/IMIS/MINING_PUBLIC/MapServer');
        } elseif ($target['kind'] === 'district' && $target['id'] !== 'rs-00') {
            $q[] = self::choice_question('seat', 'Koje je sedište ovog okruga?', $target['seat'], array_column($districts, 'seat'), $seed . ':seat', 'Sedište okruga je ' . $target['seat'] . '.', 'https://www.srbija.gov.rs/sekcija/329/okruzi.php');
        } elseif ($target['kind'] === 'municipality' && $target['district_id'] !== 'rs-00') {
            $parent = $districts[$target['district_id']];
            $q[] = self::choice_question('district', 'Kojem okrugu pripada ova teritorija?', $parent['name'], array_column($districts, 'name'), $seed . ':district', $target['name'] . ' pripada administrativnoj celini: ' . $parent['name'] . '.', 'https://www.srbija.gov.rs/sekcija/329/okruzi.php');
        } else {
            $q[] = self::choice_question('river', 'Na ušću kojih reka se nalazi Beograd?', 'Save i Dunava', array('Tise i Dunava', 'Drine i Save', 'Južne i Zapadne Morave'), $seed . ':river', 'Beograd se nalazi na ušću Save u Dunav.', 'https://www.tob.rs/');
        }
        $landmarks = array();
        foreach (array_merge(array_values($districts), array_values($pool)) as $t) { if (!empty($t['landmark']['name'])) { $landmarks[] = $t['landmark']['name']; } }
        if (!empty($target['landmark']['image']) && count(array_unique($landmarks)) >= 4) {
            $l = $target['landmark'];
            $last = self::choice_question('landmark', 'Koja je znamenitost na fotografiji?', $l['name'], $landmarks, $seed . ':landmark', $l['fact'], $l['source_url']);
            $last['image'] = SND_Data::image_url($l['image']);
            $last['image_source'] = $l['source_url'];
            $last['credit'] = $l['credit']; $last['license'] = $l['license'];
            $q[] = $last;
        } else {
            $others = array_filter($pool, function ($t) use ($target) { return $t['id'] !== $target['id']; });
            uasort($others, function ($a, $b) use ($target) { return self::distance($target['center'], $a['center']) <=> self::distance($target['center'], $b['center']); });
            $near = reset($others); $decoys = array();
            foreach ($others as $t) { if (self::distance($target['center'], $t['center']) > self::distance($target['center'], $near['center']) + 1) { $decoys[] = $t['name']; } }
            $q[] = self::choice_question('nearby', 'Koja od ovih teritorija ima centar najbliži centru tražene teritorije?', $near['name'], $decoys, $seed . ':nearby', 'Od ponuđenih odgovora, najbliža je teritorija ' . $near['name'] . ': približno ' . round(self::distance($target['center'], $near['center'])) . ' km vazdušnom linijom između geometrijskih centara.', 'https://www.geoboundaries.org/');
        }
        $flag = SND_Flags::question($target, $seed);
        if (!$flag) {
            $flag = self::choice_question('region', 'U kom širem delu Srbije se nalazi ova teritorija?', $target['region'], array('Vojvodina','Beograd','Zapadna Srbija','Šumadija i Pomoravlje','Istočna Srbija','Južna Srbija','Kosovo i Metohija'), $seed . ':flag-replacement', 'Širi položaj teritorije: ' . $target['region'] . '. Zastava za ovo mesto još nije potvrđena u bazi, pa je postavljeno geografsko pitanje.');
            $flag['subtitle'] = 'Zamensko pitanje: zastava još nije potvrđena u bazi.';
        }
        array_splice($q, 2, 0, array($flag));
        return $q;
    }

    public static function challenge($kind, $mode) {
        $data = SND_Data::get(); $pool = SND_Data::pool($kind); $districts = SND_Data::pool('district');
        if (count($pool) < 4 || count($districts) < 4) { return new WP_Error('snd_empty', 'Nema dovoljno aktivnih teritorija. Obratite se administratoru.', array('status' => 503)); }
        $date = self::date(); $key = 'snd_day_v2_' . str_replace('-', '', $date) . '_' . $kind;
        if ($mode === 'daily') { $saved = get_option($key); if ($saved) { return $saved; } }
        $lock=$mode==='daily'?'snd_challenge_lock_'.$key:'';$lease=$lock?SND_Lock::acquire($lock):true;
        if(!$lease){return new WP_Error('snd_busy','Dnevni izazov se priprema. Pokušaj ponovo.',array('status'=>409));}
        try {
        if($mode==='daily'){$saved=get_option($key);if($saved){return $saved;}}
        $days = (int) (new DateTimeImmutable('2026-01-01'))->diff(new DateTimeImmutable($date))->format('%r%a');
        $cycle = (int) floor($days / count($pool));
        $order = self::ordered($pool, $kind . ':' . $cycle);
        $target = $mode === 'daily' ? $order[(($days % count($pool)) + count($pool)) % count($pool)] : $order[random_int(0, count($pool) - 1)];
        $seed = $mode === 'daily' ? $date . ':' . $kind : bin2hex(random_bytes(16));
        $catalog = array();
        foreach ($pool as $t) { $catalog[$t['id']] = array_intersect_key($t, array_flip(array('id', 'kind', 'name', 'center', 'aliases', 'map_path'))); }
        $challenge = array('date' => $date, 'kind' => $kind, 'mode' => $mode, 'ruleset' => self::RULESET, 'storage_key' => $key, 'number' => max(1, $days + 1), 'target' => $target, 'catalog' => $catalog, 'revision' => $data['revision'], 'questions' => self::questions($target, $pool, $districts, $seed));
        if ($mode === 'daily') { add_option($key, $challenge, '', false); return get_option($key); }
        return $challenge;
        } finally {if($lock){SND_Lock::release($lock,$lease);}}
    }

    public static function create($kind, $mode) {
        $c = self::challenge($kind, $mode); if (is_wp_error($c)) { return $c; }
        $token = bin2hex(random_bytes(24));
        $state = array('challenge' => $c, 'stage' => 'guess', 'guesses' => array(), 'hint' => false, 'solved' => false, 'answers' => array(), 'question_index' => 0, 'revision' => 0, 'owner' => get_current_user_id(), 'game_id' => bin2hex(random_bytes(16)), 'recorded' => false);
        self::persist($token, $state);
        SND_Progress::remember(get_current_user_id(), $kind, $mode, $token);
        return array('token' => $token, 'state' => self::public_state($state));
    }

    public static function persist($token, $state) {
        if ($state['challenge']['mode'] === 'daily') {
            $state['challenge_key'] = $state['challenge']['storage_key'] ?? ('snd_day_' . str_replace('-', '', $state['challenge']['date']) . '_' . $state['challenge']['kind']);
            unset($state['challenge']);
        }
        set_transient('snd_session_' . $token, $state, (!empty($state['owner']) ? 30 : 3) * DAY_IN_SECONDS);
    }

    public static function load($token) {
        if (!is_string($token) || !preg_match('/^[a-f0-9]{48}$/D', $token)) { return new WP_Error('snd_token', 'Partija nije pronađena. Pokreni novu.', array('status' => 404)); }
        $s = get_transient('snd_session_' . $token);
        if (!$s) { return new WP_Error('snd_expired', 'Sačuvana partija je istekla. Pokreni novu.', array('status' => 410)); }
        if (!empty($s['owner']) && (int) $s['owner'] !== get_current_user_id()) { return new WP_Error('snd_owner', 'Ova partija pripada drugom nalogu.', array('status' => 403)); }
        if (!isset($s['challenge']) && isset($s['challenge_key'])) { $s['challenge'] = get_option($s['challenge_key']); }
        if (empty($s['challenge'])) { return new WP_Error('snd_expired', 'Podaci ove partije više nisu dostupni. Pokreni novu.', array('status' => 410)); }
        if ($s['challenge']['mode'] === 'daily' && $s['challenge']['date'] !== self::date()) { return new WP_Error('snd_new_day', 'Stigao je novi dnevni izazov. Pokreni današnju partiju.', array('status' => 410)); }
        return $s;
    }

    public static function act(&$s, $action, $value) {
        $c = $s['challenge'];
        if ($action === 'guess' && $s['stage'] === 'guess') {
            if (!is_string($value) || !isset($c['catalog'][$value])) { return new WP_Error('snd_guess', 'Izaberi teritoriju iz ponuđene liste.', array('status' => 400)); }
            foreach ($s['guesses'] as $guess) { if ($guess['id'] === $value) { return new WP_Error('snd_duplicate', 'Ovu teritoriju si već pokušao/la.', array('status' => 400)); } }
            $from = $c['catalog'][$value]; $correct = $value === $c['target']['id'];
            $s['guesses'][] = array('id' => $value, 'name' => $from['name'], 'km' => (int) round(self::distance($from['center'], $c['target']['center'])), 'direction' => self::direction($from['center'], $c['target']['center']), 'correct' => $correct);
            if ($correct || count($s['guesses']) >= self::MAX_GUESSES) { $s['stage'] = 'reveal'; $s['solved'] = $correct; }
        } elseif ($action === 'hint' && $s['stage'] === 'guess' && count($s['guesses']) >= 3 && !$s['hint']) {
            $s['hint'] = true;
        } elseif ($action === 'giveup' && $s['stage'] === 'guess') {
            $s['stage'] = 'reveal';
        } elseif ($action === 'next' && $s['stage'] === 'reveal') {
            $s['stage'] = 'quiz';
        } elseif (($action === 'answer' || $action === 'skip') && $s['stage'] === 'quiz' && !isset($s['answers'][$s['question_index']])) {
            $q = $c['questions'][$s['question_index']];
            if ($action === 'answer' && !in_array($value, array_column($q['options'], 'id'), true)) { return new WP_Error('snd_choice', 'Izaberi jedan ponuđeni odgovor.', array('status' => 400)); }
            $s['answers'][$s['question_index']] = array('choice' => $action === 'skip' ? null : $value, 'correct' => $action === 'answer' && $value === $q['correct'], 'skipped' => $action === 'skip');
        } elseif ($action === 'next' && $s['stage'] === 'quiz' && isset($s['answers'][$s['question_index']])) {
            $s['question_index']++;
            if ($s['question_index'] >= count($c['questions'])) { $s['stage'] = 'complete'; }
        } elseif ($action === 'finish' && in_array($s['stage'], array('reveal', 'quiz'), true)) {
            foreach ($c['questions'] as $i => $q) { if (!isset($s['answers'][$i])) { $s['answers'][$i] = array('choice' => null, 'correct' => false, 'skipped' => true); } }
            $s['stage'] = 'complete';
        } else { return new WP_Error('snd_action', 'Ovaj potez sada nije dostupan. Osveži prikaz partije.', array('status' => 409)); }
        $s['revision']++;
        return true;
    }

    public static function public_state($s) {
        $c = $s['challenge'];
        $out = array_intersect_key($s, array_flip(array('stage', 'guesses', 'hint', 'solved', 'question_index', 'revision')));
        $out += array('date' => $c['date'], 'number' => $c['number'], 'kind' => $c['kind'], 'mode' => $c['mode'], 'shape' => $c['target']['path'], 'max_guesses' => self::MAX_GUESSES, 'question_count' => count($c['questions']));
        $out['catalog'] = array_map(function ($t) { return array_intersect_key($t, array_flip(array('id', 'name', 'aliases'))); }, array_values($c['catalog']));
        if ($s['hint']) { $out['hint_text'] = 'Širi položaj: ' . $c['target']['region'] . '.'; }
        if ($s['stage'] !== 'guess') {
            $out['territory'] = array_intersect_key($c['target'], array_flip(array('id', 'name', 'region', 'map_path')));
            $out['map'] = array_map(function ($t) { return array_intersect_key($t, array_flip(array('id', 'name', 'map_path'))); }, array_values($c['catalog']));
        }
        if ($s['stage'] === 'quiz') {
            $i = $s['question_index']; $q = $c['questions'][$i];
            if (!isset($s['answers'][$i])) { unset($q['correct'], $q['explanation'], $q['source'], $q['credits']); }
            else { $q['answer'] = $s['answers'][$i]; }
            $out['question'] = $q;
        }
        $out['score'] = count(array_filter($s['answers'], function ($a) { return $a['correct']; }));
        if ($s['stage'] === 'complete') {
            $out['review'] = array();
            foreach ($c['questions'] as $i => $q) {
                $label = ''; foreach ($q['options'] as $option) { if ($option['id'] === $q['correct']) { $label = $option['label']; } }
                $out['review'][] = array('title' => $q['title'], 'correct_label' => $label, 'explanation' => $q['explanation'], 'source' => $q['source'], 'answer' => $s['answers'][$i] ?? array('correct' => false, 'skipped' => true));
            }
        }
        return $out;
    }

    public static function cleanup() {
        global $wpdb;
        $threshold = (new DateTimeImmutable(self::date()))->modify('-14 days')->format('Ymd');
        $names = $wpdb->get_col($wpdb->prepare("SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s", $wpdb->esc_like('snd_day_') . '%'));
        foreach ($names as $name) { if (preg_match('/^snd_day_(?:v2_)?([0-9]{8})_/', $name, $m) && $m[1] < $threshold) { delete_option($name); } }
    }
}
