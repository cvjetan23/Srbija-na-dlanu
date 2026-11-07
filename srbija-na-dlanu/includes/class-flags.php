<?php
defined('ABSPATH') || exit;

final class SND_Flags {
    public static function all() {
        $file = SND_DIR . 'data/flags.php';
        $flags = is_file($file) ? require $file : array();
        foreach ((array) get_option('snd_flag_overrides', array()) as $key => $flag) {
            if (!empty($flag['image'])) { $flags[$key] = $flag; } else { unset($flags[$key]); }
        }
        return $flags;
    }
    public static function for_territory($territory) {
        $name = $territory['kind'] === 'district' ? $territory['seat'] : $territory['name'];
        $all = self::all();
        return $all[SND_Text::key($name)] ?? null;
    }
    public static function valid($flag) {
        if (!is_array($flag) || empty($flag['image'])) { return array(); }
        $image = (string) $flag['image'];
        if (!(strpos($image, 'https://') === 0 && esc_url_raw($image, array('https'))) && !preg_match('~^flags/[a-zA-Z0-9_-]+\.(png|jpg|webp)$~D', $image)) {
            return new WP_Error('snd_flag_image', 'Za zastavu izaberi HTTPS sliku iz biblioteke ili ugrađenu flags/ sliku.');
        }
        $out = array('image' => $image);
        foreach (array('name', 'credit', 'license') as $key) { $out[$key] = sanitize_text_field($flag[$key] ?? ''); }
        $out['source'] = esc_url_raw($flag['source'] ?? '', array('https'));
        if (!$out['name'] || !$out['credit'] || !$out['license'] || !$out['source']) { return new WP_Error('snd_flag_source', 'Zastava mora imati naziv mesta, autora, licencu i izvor.'); }
        return $out;
    }
    public static function question($target, $seed, $fallback=false) {
        $flag = self::for_territory($target);
        $subtitle=$target['kind']==='district'?'Zastava grada-sedišta ovog okruga.':'Zastava ove opštine ili grada.';
        if(!$flag && $fallback){
            $districts=SND_Data::pool('district');$district=$target['kind']==='district'?$target:($districts[$target['district_id']??'']??null);
            if($district){$flag=self::for_territory($district);}
            if($flag){$subtitle='Lokalna zastava nije potvrđena; pogodi zastavu sedišta ove administrativne celine.';}
            else{$available=SND_Game::ordered(self::all(),$seed.':verified-flag');$flag=$available?reset($available):null;$subtitle='Lokalna zastava nije potvrđena; ovo je dodatno pitanje o drugom mestu navedenom u naslovu.';}
        }
        if (!$flag) { return null; }
        $others = array_filter(self::all(), function ($other) use ($flag) { return $other['name'] !== $flag['name'] && $other['image'] !== $flag['image']; });
        $others = SND_Game::ordered($others, $seed . ':flag-decoys');
        if (count($others) < 3) { return null; }
        $chosen = array_merge(array($flag), array_slice($others, 0, 3));
        $chosen = SND_Game::ordered($chosen, $seed . ':flag-order');
        $options = array(); $correct = ''; $sources = array();
        foreach ($chosen as $i => $value) {
            $id = substr(hash('sha256', $seed . ':flag:' . $value['name']), 0, 16);
            $options[] = array('id' => $id, 'label' => 'Zastava ' . chr(65 + $i), 'image' => SND_Data::image_url($value['image']));
            $sources[] = array('label' => 'Zastava ' . chr(65 + $i), 'credit' => $value['credit'], 'license' => $value['license'], 'source' => $value['source']);
            if ($value['name'] === $flag['name']) { $correct = $id; }
        }
        return array('type' => 'flag', 'i18n_data'=>array('place'=>$flag['name']), 'correct_label'=>'Zastava mesta '.$flag['name'], 'title' => 'Koja je zastava mesta „' . $flag['name'] . '“?', 'subtitle' => $subtitle, 'options' => $options, 'correct' => $correct, 'explanation' => 'Prikazana je zastava mesta ' . $flag['name'] . '.', 'source' => $flag['source'], 'credits' => $sources);
    }
}
