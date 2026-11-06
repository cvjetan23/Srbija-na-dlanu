<?php
defined('ABSPATH') || exit;

final class SND_Admin {
    public static function menu() { add_menu_page('Srbija na dlanu', 'Srbija na dlanu', 'manage_options', 'srbija-na-dlanu', array(__CLASS__, 'page'), 'dashicons-location-alt', 65); }
    public static function assets($hook) {
        if ($hook !== 'toplevel_page_srbija-na-dlanu') { return; }
        wp_enqueue_media();
        wp_enqueue_script('snd-admin', SND_URL . 'assets/admin.js', array('jquery'), SND_VERSION, true);
    }
    private static function authorize($action) {
        if (!current_user_can('manage_options')) { wp_die('Nedovoljne dozvole.', '', array('response' => 403)); }
        check_admin_referer($action);
    }
    private static function redirect($message, $error = false) {
        set_transient('snd_notice_' . get_current_user_id(), array('message' => $message, 'error' => $error), 60);
        wp_safe_redirect(admin_url('admin.php?page=srbija-na-dlanu')); exit;
    }
    private static function field($name, $label, $value, $type = 'text') {
        echo '<tr><th><label for="snd-' . esc_attr($name) . '">' . esc_html($label) . '</label></th><td><input class="regular-text" id="snd-' . esc_attr($name) . '" name="' . esc_attr($name) . '" type="' . esc_attr($type) . '" value="' . esc_attr($value) . '"></td></tr>';
    }
    public static function page() {
        if (!current_user_can('manage_options')) { return; }
        $data = SND_Data::editable();
        echo '<div class="wrap"><h1>Srbija na dlanu</h1>';
        $notice = get_transient('snd_notice_' . get_current_user_id());
        if ($notice) { echo '<div class="notice ' . ($notice['error'] ? 'notice-error' : 'notice-success') . '"><p>' . esc_html($notice['message']) . '</p></div>'; delete_transient('snd_notice_' . get_current_user_id()); }
        echo '<p><a class="button" href="'.esc_url(SND_Site::url()).'">Otvori zaseban sajt igre</a> Početna, Okruzi, Opštine, Baza znanja i Prijava imaju svoj meni i šablon. Ostale stranice sajta zadržavaju temu. Shortcode <code>[srbija_na_dlanu]</code> možeš koristiti i na drugim stranicama.</p><p><strong>Dnevni izazov se menja u ponoć po vremenu u Srbiji.</strong> Današnji zadatak je sačuvan; izmene sadržaja odmah važe za nove partije vežbanja i naredne dnevne izazove.</p><p>' . esc_html($data['coverage']) . '</p>';
        $edit = isset($_GET['edit']) ? sanitize_key(wp_unslash($_GET['edit'])) : '';
        $target = null;
        foreach ($data['territories'] as $row) { if ($row['id'] === $edit) { $target = $row; break; } }
        if ($target) {
            echo '<h2>Uredi: ' . esc_html($target['name']) . '</h2><form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
            wp_nonce_field('snd_save');
            echo '<input type="hidden" name="action" value="snd_save"><input type="hidden" name="id" value="' . esc_attr($target['id']) . '"><table class="form-table">';
            self::field('name', 'Naziv', $target['name']);
            self::field('aliases', 'Drugi nazivi (razdvojeni zarezom)', implode(', ', $target['aliases'] ?? array()));
            self::field('region', 'Širi položaj / pomoć', $target['region']);
            if ($target['kind'] === 'district') { self::field('seat', 'Sedište okruga', $target['seat']); }
            self::field('population', 'Stanovništvo CELE teritorije', $target['population'] ?? 0, 'number');
            self::field('year', 'Godina podatka', $target['year'] ?? 2022, 'number');
            self::field('population_source', 'Izvor stanovništva (HTTPS)', $target['population_source'] ?? '', 'url');
            $l = $target['landmark'] ?? array();
            self::field('landmark_name', 'Naziv znamenitosti', $l['name'] ?? '');
            self::field('landmark_image', 'URL fotografije (HTTPS) ili photos/ime.jpg', $l['image'] ?? '');
            echo '<tr><th>Fotografija iz biblioteke</th><td><button type="button" class="button" id="snd-select-image">Izaberi fotografiju</button><p class="description">Dodaj samo fotografiju za koju imaš pravo korišćenja. Upiši autora, licencu i stranicu izvora.</p></td></tr>';
            self::field('landmark_credit', 'Autor fotografije', $l['credit'] ?? '');
            self::field('landmark_license', 'Licenca fotografije', $l['license'] ?? '');
            self::field('landmark_source_url', 'Stranica izvora fotografije (HTTPS)', $l['source_url'] ?? '', 'url');
            self::field('landmark_fact', 'Kratka zanimljivost / objašnjenje', $l['fact'] ?? '');
            foreach(array('en'=>'English','de'=>'Deutsch','ru'=>'Русский') as $lang=>$label){
                self::field('landmark_'.$lang.'_name','Naziv znamenitosti — '.$label,$l['translations'][$lang]['name']??'');
                self::field('landmark_'.$lang.'_fact','Zanimljivost — '.$label,$l['translations'][$lang]['fact']??'');
            }
            echo '<tr><th>Prevodi sadržaja</th><td>Prazno polje koristi ugrađeni prevod. Kada promeniš izvornu zanimljivost, dodaj i njene prevode; bez njih se prikazuje izvorni tekst.</td></tr>';
            echo '<tr><th>Status</th><td><label><input type="checkbox" name="disabled" value="1" ' . checked(!empty($target['disabled']), true, false) . '> Isključi iz novih partija</label></td></tr></table>';
            submit_button('Sačuvaj teritoriju');
            echo '</form><p><a href="' . esc_url(admin_url('admin.php?page=srbija-na-dlanu')) . '">← Sve teritorije</a></p>';
            $flag=SND_Flags::for_territory($target);$place=$target['kind']==='district'?$target['seat']:$target['name'];
            echo '<h2>Zastava: '.esc_html($place).'</h2><p>Za okrug se koristi zastava njegovog sedišta. Ista zastava važi i za odgovarajući grad/opštinu. Dodaj samo potvrđeni simbol i proveri uslove korišćenja.</p><form method="post" action="'.esc_url(admin_url('admin-post.php')).'">';
            wp_nonce_field('snd_save_flag');echo '<input type="hidden" name="action" value="snd_save_flag"><input type="hidden" name="id" value="'.esc_attr($target['id']).'"><table class="form-table">';
            foreach(array('image'=>'URL slike zastave (HTTPS) ili flags/ime.png','credit'=>'Autor / pripisivanje','license'=>'Licenca','source'=>'Stranica izvora (HTTPS)') as $key=>$label){self::field('flag_'.$key,$label,$flag[$key]??'');}
            echo '</table><p>Bez slike igra koristi jasno označeno zamensko geografsko pitanje. Prazno polje slike uklanja zastavu za ovo mesto; ugrađenu zastavu možeš vratiti poništavanjem lokalne izmene.</p>';submit_button('Sačuvaj zastavu','secondary');
            echo '<button class="button" name="restore_flag" value="1">Vrati ugrađenu zastavu</button></form>';
        } else {
            $rows = $data['territories']; usort($rows, function ($a, $b) { return strcmp($a['kind'] . $a['name'], $b['kind'] . $b['name']); });
            echo '<h2>Sadržaj igre</h2><p>Fotografija znamenitosti je opciona. Bez nje poslednja runda automatski postaje prostorno pitanje. Za dodavanje novih granica koristi uvoz JSON baze.</p><label for="snd-filter">Pronađi teritoriju: </label><input id="snd-filter" type="search" placeholder="Naziv teritorije…"><table class="widefat striped" style="margin-top:12px"><thead><tr><th>Teritorija</th><th>Igra</th><th>Stanovništvo</th><th>Fotografija</th><th>Status</th></tr></thead><tbody id="snd-territories">';
            foreach ($rows as $row) {
                echo '<tr><td><a href="' . esc_url(add_query_arg(array('page' => 'srbija-na-dlanu', 'edit' => $row['id']), admin_url('admin.php'))) . '">' . esc_html($row['name']) . '</a></td><td>' . ($row['kind'] === 'district' ? 'Okruzi' : 'Opštine i gradovi') . '</td><td>' . esc_html(number_format((int) ($row['population'] ?? 0), 0, ',', '.')) . ' (' . esc_html($row['year'] ?? '—') . ')</td><td>' . (!empty($row['landmark']['image']) ? 'Da' : 'Prostorno pitanje') . '</td><td>' . (!empty($row['disabled']) ? 'Isključena' : 'Aktivna') . '</td></tr>';
            }
            echo '</tbody></table><h2>Rezervna kopija i uvoz</h2><p>Izvoz sadrži granice i sav trenutni sadržaj. Uvoz zamenjuje postojeću bazu i lokalne izmene, pa pre toga preuzmi rezervnu kopiju. Dnevne partije koje su već pripremljene ostaju iste.</p><p><a class="button" href="' . esc_url(wp_nonce_url(admin_url('admin-post.php?action=snd_export'), 'snd_export')) . '">Preuzmi JSON rezervnu kopiju</a></p><form method="post" enctype="multipart/form-data" action="' . esc_url(admin_url('admin-post.php')) . '">';
            wp_nonce_field('snd_import'); echo '<input type="hidden" name="action" value="snd_import"><input type="file" name="dataset" accept=".json,application/json" required>'; submit_button('Uvezi JSON bazu', 'secondary'); echo '</form>';
        }
        echo '<h2>Izvori i obuhvat</h2><ul>';
        foreach ($data['sources'] as $s) { echo '<li><a href="' . esc_url($s['url']) . '" target="_blank" rel="noopener noreferrer">' . esc_html($s['label']) . '</a> — ' . esc_html($s['license']) . '</li>'; }
        echo '</ul><h2>Nalozi igrača</h2><p>Prijavljeni igrači čuvaju partije i rezultate u WordPress korisničkim podacima. Gosti koriste pregledač. Novi nalozi igre imaju isključivo ulogu Pretplatnik.</p><form method="post" action="'.esc_url(admin_url('admin-post.php')).'">';wp_nonce_field('snd_settings');
        echo '<input type="hidden" name="action" value="snd_settings"><label><input type="checkbox" name="registration" value="1" '.checked(get_option('snd_registration_enabled',true),true,false).'> Omogući registraciju igrača u aplikaciji</label>';submit_button('Sačuvaj podešavanja','secondary');
        echo '</form><p>Podaci dodatka ostaju sačuvani posle deaktivacije. Izvoz baze obuhvata i trenutne zastave; medijske slike sa sopstvenog domena ostaju u WP biblioteci.</p></div>';
    }

    public static function settings(){self::authorize('snd_settings');update_option('snd_registration_enabled',!empty($_POST['registration']),false);self::redirect('Podešavanja su sačuvana.');}
    public static function save_flag(){
        self::authorize('snd_save_flag');$id=sanitize_key(wp_unslash($_POST['id']??''));$t=SND_Data::get()['territories'][$id]??null;
        if(!$t){self::redirect('Teritorija nije pronađena.',true);}
        $name=$t['kind']==='district'?$t['seat']:$t['name'];$key=SND_Text::key($name);$overrides=get_option('snd_flag_overrides',array());
        if(!empty($_POST['restore_flag'])){unset($overrides[$key]);}else{
            $flag=array('name'=>$name);foreach(array('image','credit','license','source') as $field){$flag[$field]=is_string($_POST['flag_'.$field]??null)?wp_unslash($_POST['flag_'.$field]):'';}
            $flag=SND_Flags::valid($flag);if(is_wp_error($flag)){self::redirect($flag->get_error_message(),true);}$overrides[$key]=$flag;
        }
        update_option('snd_flag_overrides',$overrides,false);self::redirect('Zastava je sačuvana. Već pripremljen dnevni izazov ostaje isti.');
    }

    public static function save() {
        self::authorize('snd_save'); $data = SND_Data::editable();
        $id = sanitize_key(wp_unslash($_POST['id'] ?? '')); $found = false;
        foreach ($data['territories'] as &$t) {
            if ($t['id'] !== $id) { continue; } $found = true;
            foreach (array('name', 'region', 'population_source') as $key) { $t[$key] = wp_unslash($_POST[$key] ?? ''); }
            if ($t['kind'] === 'district') { $t['seat'] = wp_unslash($_POST['seat'] ?? ''); }
            $t['population'] = (int) ($_POST['population'] ?? 0); $t['year'] = (int) ($_POST['year'] ?? 0);
            $t['aliases'] = array_filter(array_map('trim', explode(',', wp_unslash($_POST['aliases'] ?? ''))));
            $t['disabled'] = !empty($_POST['disabled']); $t['landmark'] = array();
            foreach (array('name', 'image', 'credit', 'license', 'source_url', 'fact') as $key) { $t['landmark'][$key] = wp_unslash($_POST['landmark_' . $key] ?? ''); }
            foreach(array('en','de','ru') as $lang){foreach(array('name','fact') as $field){$value=$_POST['landmark_'.$lang.'_'.$field]??'';if(is_string($value) && $value!==''){$t['landmark']['translations'][$lang][$field]=wp_unslash($value);}}}
        }
        unset($t);
        if (!$found) { self::redirect('Teritorija nije pronađena.', true); }
        $valid = SND_Data::validate($data); if (is_wp_error($valid)) { self::redirect($valid->get_error_message(), true); }
        $overrides = get_option('snd_overrides', array());
        foreach ($valid['territories'] as $t) { if ($t['id'] === $id) { $overrides[$id] = $t; } }
        update_option('snd_overrides', $overrides, false); self::redirect('Teritorija je sačuvana. Današnji izazov ostaje isti.');
    }

    public static function import() {
        self::authorize('snd_import');
        $f = $_FILES['dataset'] ?? array();
        if (empty($f['tmp_name']) || !is_uploaded_file($f['tmp_name']) || ($f['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || $f['size'] > 10 * 1024 * 1024) { self::redirect('Izaberi JSON datoteku do 10 MB.', true); }
        $data = json_decode(file_get_contents($f['tmp_name']), true);
        $valid = SND_Data::validate($data); if (is_wp_error($valid)) { self::redirect($valid->get_error_message(), true); }
        $flags=array();if(isset($data['flags'])){
            if(!is_array($data['flags'])){self::redirect('Neispravna baza zastava.',true);}
            foreach($data['flags'] as $key=>$flag){$f=SND_Flags::valid($flag);if(is_wp_error($f)){self::redirect($f->get_error_message(),true);}if($f){$flags[SND_Text::key($f['name'])]=$f;}}
        }
        update_option('snd_dataset', $valid, false); delete_option('snd_overrides');
        if(isset($data['flags'])){ $seed=require SND_DIR.'data/flags.php';foreach($seed as $key=>$unused){if(!isset($flags[$key])){$flags[$key]=array();}}update_option('snd_flag_overrides',$flags,false); }
        self::redirect('Nova baza je uvezena. Već pripremljeni dnevni izazovi su sačuvani.');
    }

    public static function export() {
        self::authorize('snd_export'); nocache_headers();
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="srbija-na-dlanu-baza-' . SND_Game::date() . '.json"');
        $data=SND_Data::editable();$data['flags']=SND_Flags::all();echo wp_json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); exit;
    }
}
