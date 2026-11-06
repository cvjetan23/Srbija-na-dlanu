<?php
defined('ABSPATH') || exit;

final class SND_Data {
    private static $cache;

    public static function editable() {
        $data = get_option('snd_dataset', null);
        if (!is_array($data) || empty($data['territories'])) { $data = require SND_DIR . 'data/territories.php'; }
        $overrides = get_option('snd_overrides', array());
        foreach ($data['territories'] as &$item) { if (isset($overrides[$item['id']])) { $item = array_merge($item, $overrides[$item['id']]); } }
        unset($item);
        return $data;
    }

    public static function get() {
        if (self::$cache !== null) { return self::$cache; }
        $data = self::editable();
        $items = array();
        foreach ($data['territories'] as $item) {
            if (!empty($item['disabled'])) { continue; }
            $items[$item['id']] = $item;
        }
        $data['territories'] = $items;
        $data['revision'] = substr(hash('sha256', wp_json_encode($data)), 0, 16);
        self::$cache = $data;
        return $data;
    }

    public static function reset_cache() { self::$cache = null; }

    public static function pool($kind) {
        $data = self::get();
        return array_filter($data['territories'], function ($t) use ($kind) { return $t['kind'] === $kind; });
    }

    public static function image_url($path) {
        if (strpos($path, 'https://') === 0) { return esc_url_raw($path, array('https')); }
        return SND_URL . 'assets/' . ltrim($path, '/');
    }

    public static function validate($data) {
        if (!is_array($data) || !isset($data['schema']) || (int) $data['schema'] !== 1 || empty($data['territories']) || !is_array($data['territories'])) {
            return new WP_Error('snd_schema', 'Očekivan je JSON sa schema: 1 i nizom territories.');
        }
        if (count($data['territories']) > 400) { return new WP_Error('snd_size', 'Dozvoljeno je najviše 400 teritorija.'); }
        $out = array('schema' => 1, 'coverage' => sanitize_text_field($data['coverage'] ?? ''), 'sources' => array(), 'territories' => array());
        foreach (array_slice((array) ($data['sources'] ?? array()), 0, 30) as $s) {
            $out['sources'][] = array('label' => sanitize_text_field($s['label'] ?? ''), 'url' => esc_url_raw($s['url'] ?? '', array('https')), 'license' => sanitize_text_field($s['license'] ?? ''));
        }
        $ids = array();
        $counts = array('district' => 0, 'municipality' => 0);
        foreach ($data['territories'] as $t) {
            if (!is_array($t)) { return new WP_Error('snd_item', 'Svaka teritorija mora biti objekat.'); }
            $id = is_string($t['id'] ?? null) ? $t['id'] : '';
            $kind = $t['kind'] ?? '';
            if (!preg_match('/^[a-z0-9-]{1,70}$/D', $id) || isset($ids[$id]) || !is_string($kind) || !isset($counts[$kind]) || !is_string($t['name'] ?? null) || trim(sanitize_text_field($t['name'])) === '') {
                return new WP_Error('snd_identity', 'Teritorija nema jedinstven ispravan id, name ili kind (district/municipality).');
            }
            foreach (array('path', 'map_path') as $key) {
                if (!isset($t[$key]) || !is_string($t[$key]) || strlen($t[$key]) > 150000 || !preg_match('/^[MmLlHhVvCcSsQqTtAaZz0-9eE.,\s+\-]+$/D', $t[$key])) {
                    return new WP_Error('snd_shape', 'Neispravna SVG putanja za ' . $id . '. Dozvoljeni su samo podaci putanje.');
                }
            }
            $c = $t['center'] ?? null;
            if (!is_array($c) || count($c) !== 2 || !isset($c[0], $c[1]) || !is_numeric($c[0]) || !is_numeric($c[1]) || $c[0] < 40 || $c[0] > 48 || $c[1] < 17 || $c[1] > 25) {
                return new WP_Error('snd_center', 'Neispravan center [geografska širina, geografska dužina] za ' . $id . '.');
            }
            $row = array('id' => $id, 'kind' => $kind, 'name' => sanitize_text_field($t['name']), 'center' => array((float) $c[0], (float) $c[1]), 'path' => $t['path'], 'map_path' => $t['map_path']);
            foreach (array('region', 'seat', 'district_id') as $key) { $row[$key] = sanitize_text_field($t[$key] ?? ''); }
            $row['aliases'] = array_map('sanitize_text_field', array_slice((array) ($t['aliases'] ?? array()), 0, 15));
            $row['population'] = max(0, (int) ($t['population'] ?? 0));
            $row['year'] = (int) ($t['year'] ?? 0);
            $row['population_source'] = esc_url_raw($t['population_source'] ?? '', array('https'));
            if ($row['population'] && ($row['year'] < 1900 || $row['year'] > (int) gmdate('Y') || !$row['population_source'])) {
                return new WP_Error('snd_population', 'Za stanovništvo su obavezni ispravna godina i HTTPS izvor: ' . $id);
            }
            if ($kind === 'district' && empty($row['seat'])) { return new WP_Error('snd_seat', 'Nedostaje sedište: ' . $id); }
            $row['landmark'] = array();
            if (!empty($t['landmark']['name']) && !empty($t['landmark']['image'])) {
                $l = $t['landmark'];
                $image = (string) $l['image'];
                if (!(strpos($image, 'https://') === 0 && esc_url_raw($image, array('https'))) && !preg_match('~^photos/[a-zA-Z0-9_-]+\.(jpg|jpeg|png|webp)$~D', $image)) {
                    return new WP_Error('snd_image', 'Neispravna putanja fotografije za ' . $id);
                }
                if (empty($l['credit']) || empty($l['license']) || empty($l['source_url'])) { return new WP_Error('snd_credit', 'Fotografija zahteva autora, licencu i izvor: ' . $id); }
                foreach (array('name', 'credit', 'license', 'fact') as $key) { $row['landmark'][$key] = sanitize_text_field($l[$key] ?? ''); }
                $row['landmark']['image'] = $image;
                $row['landmark']['source_url'] = esc_url_raw($l['source_url'], array('https'));
            }
            $row['disabled'] = !empty($t['disabled']);
            $ids[$id] = true;
            if (!$row['disabled']) { $counts[$kind]++; }
            $out['territories'][] = $row;
        }
        if ($counts['district'] < 4 || $counts['municipality'] < 4) { return new WP_Error('snd_pool', 'Potrebne su najmanje četiri aktivne teritorije u svakoj igri.'); }
        foreach ($out['territories'] as $row) {
            if ($row['kind'] === 'municipality' && (!isset($ids[$row['district_id']]) || !self::find_district($out['territories'], $row['district_id']))) {
                return new WP_Error('snd_parent', 'Opština mora imati postojeći district_id okruga: ' . $row['id']);
            }
            if ($row['kind'] === 'district' && !$row['disabled'] && in_array($row['id'], array('rs-25', 'rs-26', 'rs-27', 'rs-28', 'rs-29'), true)) {
                $members = array_filter($out['territories'], function ($m) use ($row) { return $m['kind'] === 'municipality' && !$m['disabled'] && $m['district_id'] === $row['id']; });
                if (!$members) { return new WP_Error('snd_members', 'Okrug mora imati najmanje jednu aktivnu opštinu za pitanje o pripadnosti: ' . $row['id']); }
            }
        }
        return $out;
    }

    private static function find_district($rows, $id) {
        foreach ($rows as $row) { if ($row['id'] === $id) { return $row['kind'] === 'district' && !$row['disabled']; } }
        return false;
    }
}
