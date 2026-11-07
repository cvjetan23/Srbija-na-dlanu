<?php
defined('ABSPATH') || exit;

final class SND_Control {
    private static function guard($action='') {
        if(!current_user_can('manage_options')){wp_die('Nedovoljne dozvole.','',array('response'=>403));}
        if($action){check_admin_referer($action);}
    }
    public static function menu() {
        foreach(array('analytics'=>'Analitika','players'=>'Igrači','games'=>'Partije','settings'=>'Podešavanja','translations'=>'Prevodi','ranking'=>'Rang lista') as $key=>$label){
            add_submenu_page('srbija-na-dlanu',$label,$label,'manage_options','snd-'.$key,array(__CLASS__,$key));
        }
    }
    public static function assets($hook) {
        if(strpos($hook,'snd-')!==false){wp_enqueue_style('snd-control',SND_URL.'assets/control.css',array(),SND_VERSION);}
    }
    private static function url($page,$args=array()) {return add_query_arg(array_merge(array('page'=>'snd-'.$page),$args),admin_url('admin.php'));}
    private static function begin($title) {
        self::guard();echo '<div class="wrap snd-control"><h1>'.esc_html($title).'</h1><nav class="snd-admin-nav">';
        foreach(array('analytics'=>'Analitika','players'=>'Igrači','games'=>'Partije','settings'=>'Podešavanja','translations'=>'Prevodi','ranking'=>'Rang lista') as $key=>$label){echo '<a class="button" href="'.esc_url(self::url($key)).'">'.esc_html($label).'</a> ';}
        echo '<a class="button" href="'.esc_url(admin_url('admin.php?page=srbija-na-dlanu')).'">Teritorije i slike</a></nav>';
        if(isset($_GET['saved'])){echo '<div class="notice notice-success"><p>Izmene su sačuvane.</p></div>';}
    }
    private static function form($action) {echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'">';wp_nonce_field($action);echo '<input type="hidden" name="action" value="'.esc_attr($action).'">';}
    private static function checkbox($key,$label,$value) {echo '<label class="snd-check"><input type="checkbox" name="settings['.esc_attr($key).']" value="1" '.checked($value,true,false).'> '.esc_html($label).'</label>';}
    public static function settings() {
        self::begin('Podešavanja igre');$s=SND_Settings::all();self::form('snd_control_save');
        echo '<h2>Izgled i jezici</h2><table class="form-table"><tr><th><label for="snd-name">Naziv aplikacije</label></th><td><input id="snd-name" class="regular-text" name="settings[app_name]" value="'.esc_attr($s['app_name']).'" maxlength="100"></td></tr><tr><th>Dostupni jezici</th><td>';
        foreach(SND_I18n::languages() as $key=>$name){echo '<label class="snd-check"><input type="checkbox" name="settings[languages][]" value="'.esc_attr($key).'" '.checked(in_array($key,$s['languages'],true),true,false).'> '.esc_html($name).'</label>';}
        echo '</td></tr><tr><th>Podrazumevani jezik</th><td><select name="settings[default_language]">';foreach(SND_I18n::languages() as $key=>$name){echo '<option value="'.esc_attr($key).'" '.selected($key,$s['default_language'],false).'>'.esc_html($name).'</option>';}
        echo '</select><p class="description">Jezik iz linka ili izbor posetioca ima prednost. Ako podrazumevani jezik isključiš, koristi se prvi dostupan.</p></td></tr><tr><th>Podrazumevana tema</th><td><select name="settings[default_theme]">';
        foreach(array('auto'=>'Prema uređaju','light'=>'Svetla','dark'=>'Tamna') as $key=>$name){echo '<option value="'.$key.'" '.selected($key,$s['default_theme'],false).'>'.esc_html($name).'</option>';}
        echo '</select>';self::checkbox('theme_switch','Dozvoli igraču da promeni temu',$s['theme_switch']);echo '</td></tr>';
        foreach(array('primary_color'=>'Glavna boja (svetla tema)','dark_accent'=>'Naglašena zelena (tamna tema)','dark_button_color'=>'Pozadina dugmadi (tamna tema)','highlight_color'=>'Akcent detalji') as $key=>$label){echo '<tr><th>'.esc_html($label).'</th><td><input type="color" name="settings['.$key.']" value="'.esc_attr($s[$key]).'"></td></tr>';}
        echo '<tr><th>Pozadina reka</th><td>';self::checkbox('river_background','Diskretna SVG pozadina sa rekama Srbije',$s['river_background']);echo '</td></tr>';
        echo '<tr><th>Veličina teksta</th><td><input type="number" name="settings[font_scale]" min="90" max="140" value="'.(int)$s['font_scale'].'"> % (90–140)</td></tr></table><h2>Pravila i sadržaj</h2><p>Promene pravila važe za nove partije vežbanja i buduće dnevne izazove. Već započete partije zadržavaju svoja pravila.</p><fieldset>';
        foreach(array('district_enabled'=>'Igra za okruge','municipality_enabled'=>'Igra za opštine i gradove','daily_enabled'=>'Dnevni izazov','practice_enabled'=>'Vežbanje') as $key=>$label){self::checkbox($key,$label,$s[$key]);}
        echo '</fieldset><table class="form-table"><tr><th>Broj pokušaja</th><td><input type="number" name="settings[max_guesses]" min="2" max="12" value="'.(int)$s['max_guesses'].'"></td></tr><tr><th>Pomoć pri pogađanju</th><td>';self::checkbox('hint_enabled','Omogući pomoć',$s['hint_enabled']);
        echo '<label>Posle <input type="number" name="settings[hint_after]" min="1" max="11" value="'.(int)$s['hint_after'].'"> promašaja</label></td></tr><tr><th>Dodatne runde</th><td>';
        foreach(array('population'=>'Stanovništvo / položaj','administration'=>'Administrativna geografija','flag'=>'Zastava / geografska zamena','landmark'=>'Znamenitost / geografska zamena') as $key=>$label){echo '<label class="snd-check"><input type="checkbox" name="settings[rounds][]" value="'.$key.'" '.checked(in_array($key,$s['rounds'],true),true,false).'> '.esc_html($label).'</label>';}
        echo '<p class="description">Bez izabranih dodatnih rundi igra se samo pogađanje teritorije.</p></td></tr><tr><th>Slike i izvori</th><td>';
        foreach(array('source_help_enabled'=>'Izvor fotografije kao označena pomoć pre odgovora','media_fallback'=>'Zamensko pitanje kada slika nije učitana','show_sources'=>'Dugme „Obuhvat i izvori“','flag_fallback'=>'Kada nema lokalne zastave, ponudi potvrđenu zastavu sedišta ili drugog jasno imenovanog mesta') as $key=>$label){self::checkbox($key,$label,$s[$key]);}
        echo '<p class="description">Autor, licenca i izvori uz odgovor ostaju prikazani zbog pripisivanja i provere podataka.</p></td></tr><tr><th>Nalozi</th><td>';self::checkbox('registration_enabled','Dozvoli registraciju novih igrača',SND_Settings::get('registration_enabled'));
        echo '<p class="description">Novi igrači dobijaju WP ulogu Pretplatnik. Postojeći nalozi mogu da se prijave i kada je registracija isključena.</p></td></tr></table>';self::ranking_settings($s);echo '<h2>Analitika i čuvanje podataka</h2>';
        foreach(array('analytics_enabled'=>'Beleži analitiku i detaljnu istoriju partija','guest_analytics'=>'Beleži i anonimne goste','erase_deleted_users'=>'Obriši analitiku igrača prilikom brisanja njegovog WP naloga') as $key=>$label){self::checkbox($key,$label,$s[$key]);}
        echo '<p><label>Čuvaj detaljnu analitiku <input type="number" name="settings[retention_days]" min="0" max="3650" value="'.(int)$s['retention_days'].'"> dana (0 = bez automatskog brisanja, inače 7–3650).</label></p><p class="description">Čišćenje radi dnevno kroz WP-Cron. Progres na nalogu se čuva nezavisno od roka za analitiku. Kada brisanje pri uklanjanju naloga isključiš, istorija se anonimizuje. Ne čuvaju se sirove IP adrese, lozinke, kolačići ili sadržaj URL upita; za preporuke se čuva samo domen.</p>';
        submit_button('Sačuvaj podešavanja');echo '</form><hr><h2>Izvoz i brisanje analitike</h2><p>Izvoz je dostupan i uz filtere na stranici Analitika. Brisanje ovde uklanja samo tabele analitike, a progres i WP nalozi ostaju sačuvani.</p>';self::form('snd_analytics_clear');
        echo '<label><input type="checkbox" name="confirm" value="1" required> Potvrđujem brisanje celokupne detaljne analitike.</label>';submit_button('Obriši analitiku','delete');echo '</form></div>';
    }
    private static function ranking_settings($s) {
        echo '<h2>Bodovanje i rang lista</h2>';self::checkbox('ranking_enabled','Omogući rang liste i upis bodova za nove partije',$s['ranking_enabled']);
        echo '<p>Dve odvojene liste: Dnevni izazovi i Opšta rang lista (izazovi + vežbanje). Svaka ima dnevni, mesečni i ukupni pregled, zajedno i po igrama. Rezultati se čuvaju nezavisno od analitike. Promene važe za nove partije i buduće dnevne izazove.</p><table class="form-table">';
        foreach(array('points_territory'=>'Maksimum za teritoriju (ostatak do 100 deli se na pitanja)','points_step'=>'Umanjenje za svaki dodatni pokušaj','points_hint'=>'Umanjenje za pomoć pri pogađanju') as $key=>$label){echo '<tr><th>'.esc_html($label).'</th><td><input type="number" min="0" max="100" name="settings['.$key.']" value="'.(int)$s[$key].'"></td></tr>';}
        echo '</table><p>Bez dodatnih pitanja teritorija vredi 100. Izvor kao pomoć isključuje poene samo za to pitanje. Zbir je ograničen na 0–100. Ranije partije ne dobijaju naknadno izmišljene bodove. Javno se prikazuje nadimak, a igrač može sakriti svoj plasman na nalogu.</p>';
    }
    public static function ranking() {
        self::begin('Rang lista');$f=SND_Ranking::filters();echo '<form method="get" class="snd-filters"><input type="hidden" name="page" value="snd-ranking">';
        foreach(array('board'=>array('daily'=>'Dnevni izazovi','general'=>'Opšta rang lista'),'period'=>array('day'=>'Dnevna','month'=>'Mesečna','all'=>'Večna'),'game'=>array(''=>'Sve igre','district'=>'Okruzi','municipality'=>'Opštine i gradovi')) as $key=>$values){echo '<select name="'.$key.'">';foreach($values as $v=>$label){echo '<option value="'.esc_attr($v).'" '.selected($v,$f[$key],false).'>'.esc_html($label).'</option>';}echo '</select>';}
        echo '<input type="date" name="rank_date" value="'.esc_attr($f['rank_date']).'"><input type="month" name="rank_month" value="'.esc_attr($f['rank_month']).'"><button class="button">Prikaži</button></form><p>Administratorski pregled uključuje i skrivene igrače. Javna lista ih ne prikazuje. Nezavršene partije nisu na listi.</p>';$r=SND_Ranking::results($f,true);
        echo '<table class="widefat striped"><thead><tr><th>Mesto</th><th>Nalog</th><th>Javni nadimak</th><th>Poeni</th><th>Partije</th><th>Dnevni izazovi</th><th>Prosek / 100</th></tr></thead><tbody>';
        foreach($r['rows'] as $row){echo '<tr><td>'.(int)$row['rank'].'</td><td>'.esc_html(self::user_label($row['user_id'])).'</td><td>'.esc_html($row['alias']).'</td><td>'.(int)$row['total'].'</td><td>'.(int)$row['games'].'</td><td>'.(int)$row['daily_games'].'</td><td>'.esc_html(round($row['average'],1)).'</td></tr>';}
        if(!$r['rows']){echo '<tr><td colspan="7">Nema rezultata za ovaj izbor.</td></tr>';}echo '</tbody></table><p>Stranica '.$r['page'].' / '.$r['pages'].'</p>';foreach(array(-1=>'Prethodna',1=>'Sledeća') as $delta=>$label){$page=$r['page']+$delta;if($page>0 && $page<=$r['pages']){echo '<a class="button" href="'.esc_url(self::url('ranking',array_merge($f,array('rank_page'=>$page)))).'">'.$label.'</a> ';}}
        $url=wp_nonce_url(add_query_arg(array_merge($f,array('action'=>'snd_ranking_export')),admin_url('admin-post.php')),'snd_ranking_export');echo '<p><a class="button" href="'.esc_url($url).'">Izvezi bodovane partije (CSV)</a></p></div>';
    }
    public static function save() {
        self::guard('snd_control_save');$input=wp_unslash($_POST['settings']??array());$s=SND_Settings::validate($input);
        update_option('snd_settings',$s,false);update_option('snd_registration_enabled',!empty($input['registration_enabled']),false);SND_Settings::reset();
        wp_safe_redirect(self::url('settings',array('saved'=>1)));exit;
    }
    private static function filter_form($page,$f) {
        echo '<form method="get" class="snd-filters"><input type="hidden" name="page" value="snd-'.esc_attr($page).'">';
        foreach(array('from'=>'Od','to'=>'Do') as $key=>$label){echo '<label>'.esc_html($label).' <input type="date" name="'.$key.'" value="'.esc_attr($f[$key]).'"></label>';}
        foreach(array('kind'=>array(''=>'Sve igre','district'=>'Okruzi','municipality'=>'Opštine i gradovi'),'mode'=>array(''=>'Svi režimi','daily'=>'Dnevni','practice'=>'Vežbanje'),'stage'=>array(''=>'Sve faze','guess'=>'Pogađanje','reveal'=>'Otkriveno','quiz'=>'Kviz','complete'=>'Završeno'),'language'=>array(''=>'Svi jezici')+SND_I18n::languages()) as $key=>$options){
            echo '<select aria-label="'.esc_attr($key).'" name="'.$key.'">';foreach($options as $value=>$label){echo '<option value="'.esc_attr($value).'" '.selected($value,$f[$key],false).'>'.esc_html($label).'</option>';}echo '</select>';
        }
        echo '<label>ID igrača <input type="number" min="0" name="user_id" value="'.(int)$f['user_id'].'"></label><button class="button button-primary">Primeni</button></form>';
    }
    private static function exports($f) {
        echo '<p class="snd-exports">Izvezi filtrirane podatke: ';
        foreach(array('games'=>'Partije','events'=>'Aktivnosti','players'=>'Igrači') as $type=>$label){foreach(array('csv','json') as $format){$url=wp_nonce_url(add_query_arg(array_merge($f,array('action'=>'snd_analytics_export','type'=>$type,'format'=>$format)),admin_url('admin-post.php')),'snd_analytics_export');echo '<a class="button" href="'.esc_url($url).'">'.esc_html($label.' '.strtoupper($format)).'</a> ';}}
        echo '</p><p class="description">Partije prate sve filtere. Aktivnosti prate datum, ID igrača i jezik. Izvoz igrača obuhvata WP naloge i njihov trenutni progres (nezavisno od datuma).</p>';
    }
    public static function analytics() {
        self::begin('Analitika');$f=SND_Analytics::filters();self::filter_form('analytics',$f);$s=SND_Analytics::summary($f);$t=$s['totals'];
        if(!SND_Settings::get('analytics_enabled')){echo '<div class="notice notice-warning"><p>Beleženje analitike je trenutno isključeno. Postojeći podaci ostaju vidljivi.</p></div>';}
        echo '<div class="snd-metrics">';$metrics=array('Partije'=>$t['games']??0,'Završene'=>$t['completed']??0,'Pogođene teritorije'=>$t['solved']??0,'Prijavljeni igrači'=>$t['players']??0,'Partije gostiju'=>$t['guest_games']??0,'Pregledi stranica'=>$s['views'],'Približno posetilaca'=>$s['visitors'],'Aktivno vreme (min)'=>round($s['active_seconds']/60,1),'Tačni odgovori u kvizu'=>($t['correct']??0).'/'.($t['questions']??0),'Partije uz pomoć'=>$t['assisted']??0,'Prosek pokušaja (pogodak)'=>round((float)($t['avg_guesses']??0),2),'Proteklo vreme (min)'=>round((float)($t['avg_elapsed']??0)/60,1));
        foreach($metrics as $label=>$value){echo '<article><span>'.esc_html($label).'</span><strong>'.esc_html($value).'</strong></article>';}echo '</div><p>Posetioci su procena prema lokalnom anonimnom identifikatoru ili WP nalogu. Aktivno vreme je procena dok je stranica vidljiva i u fokusu. Proteklo vreme partije uključuje pauze. Pregledi i aktivnosti ne koriste filter igre, režima ili faze.</p><h2>Partije po danu</h2><table class="widefat striped"><thead><tr><th>Datum</th><th>Započete / evidentirane</th><th>Završene</th></tr></thead><tbody>';
        $max=max(1,...array_map('intval',array_column($s['daily'],'started')));
        foreach($s['daily'] as $day){echo '<tr><td>'.esc_html($day['played_on']).'</td><td><span class="snd-bar" style="width:'.max(1,round(100*$day['started']/$max)).'%"></span> '.(int)$day['started'].'</td><td>'.(int)$day['completed'].'</td></tr>';}
        if(!$s['daily']){echo '<tr><td colspan="3">Nema podataka za izabrani period.</td></tr>';}echo '</tbody></table><h2>Teritorije — najviše igrane</h2><table class="widefat striped"><thead><tr><th>Teritorija</th><th>Partije</th><th>Pogodak</th><th>Prosek pokušaja</th></tr></thead><tbody>';
        foreach($s['territories'] as $row){echo '<tr><td>'.esc_html($row['territory_name']).'</td><td>'.(int)$row['games'].'</td><td>'.(int)$row['solved'].' ('.round(100*$row['solved']/max(1,$row['games'])).'%)</td><td>'.esc_html(round((float)$row['guesses'],2)).'</td></tr>';}echo '</tbody></table><div class="snd-breakdowns">';
        foreach(array('languages'=>'Jezici','themes'=>'Teme','devices'=>'Uređaji','referrers'=>'Domeni preporuke','errors'=>'Greške API-ja') as $key=>$label){echo '<section><h2>'.esc_html($label).'</h2><table class="widefat striped"><tbody>';foreach($s[$key] as $row){$values=array_values($row);echo '<tr><td>'.esc_html($values[0]?:'Direktno / nepoznato').'</td><td>'.(int)$values[1].'</td></tr>';}echo '</tbody></table></section>';}
        echo '</div><p>Stariji uvezeni dnevni rezultati: '.(int)($t['legacy']??0).'. Za njih ne postoje precizno vreme, pojedinačni pokušaji ili odgovori. Nezavršena partija znači da još nije završena; ne zaključujemo automatski da je igrač odustao.</p>';self::exports($f);echo '</div>';
    }
    private static function user_label($id) {
        if(!$id){return 'Gost / anonimizovano';}$u=get_userdata($id);return $u?$u->display_name.' (#'.$id.')':'Obrisan nalog (#'.$id.')';
    }
    private static function pages($page,$total,$size,$args=array()) {
        $current=max(1,(int)($_GET['paged']??1));$base=add_query_arg('paged','%#%',self::url($page,$args));
        echo '<div class="tablenav"><span>Ukupno: '.(int)$total.'</span> '.wp_kses_post(paginate_links(array('base'=>$base,'format'=>'','current'=>$current,'total'=>(int)ceil($total/$size)))).'</div>';
    }
    public static function players() {
        self::begin('Igrači i progres');$search=sanitize_text_field(wp_unslash($_GET['s']??''));$paged=max(1,(int)($_GET['paged']??1));
        $query=new WP_User_Query(array('blog_id'=>get_current_blog_id(),'number'=>30,'offset'=>($paged-1)*30,'search'=>$search?'*'.$search.'*':'','search_columns'=>array('user_login','user_email','display_name'),'orderby'=>'ID','order'=>'DESC'));
        echo '<form method="get"><input type="hidden" name="page" value="snd-players"><label>Pronađi igrača <input type="search" name="s" value="'.esc_attr($search).'" placeholder="Ime, nalog ili email"></label> <button class="button">Traži</button></form><table class="widefat striped"><thead><tr><th>Igrač / email</th><th>Registracija (UTC)</th><th>Okruzi</th><th>Opštine i gradovi</th><th>Istorija</th></tr></thead><tbody>';
        foreach($query->get_results() as $u){$p=SND_Progress::summary($u->ID);echo '<tr><td><a href="'.esc_url(get_edit_user_link($u->ID)).'">'.esc_html($u->display_name).' (#'.(int)$u->ID.')</a><br>'.esc_html($u->user_login).' · '.esc_html($u->user_email).'</td><td>'.esc_html($u->user_registered).'</td>';
            foreach(array('district','municipality') as $kind){$x=$p[$kind];echo '<td>Dnevne: '.(int)$x['daily_count'].'<br>Vežbanje: '.(int)$x['practice_count'].'<br>Otkriveno: '.count($x['discovered']).'<br>Kviz: '.(int)$x['quiz_correct'].'/'.(int)$x['quiz_total'].'<br>Niz: '.(int)$x['streak'].' dana</td>';}
            echo '<td><a class="button" href="'.esc_url(self::url('games',array('user_id'=>$u->ID,'from'=>'2000-01-01','to'=>SND_Game::date()))).'">Partije igrača</a></td></tr>';
        }
        echo '</tbody></table><p>Ovo je spisak WP korisnika ovog sajta. Zbirni progres može obuhvatati i starije vežbanje za koje detaljna istorija nije postojala.</p>';self::pages('players',$query->get_total(),30,array('s'=>$search));echo '</div>';
    }
    public static function games() {
        self::begin('Istorija partija');global $wpdb;$table=SND_Analytics::games_table();$id=max(0,(int)($_GET['game']??0));
        if($id){$row=$wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id=%d",$id),ARRAY_A);if($row){self::detail($row);}else{echo '<p>Partija nije pronađena.</p>';}echo '</div>';return;}
        $f=SND_Analytics::filters();self::filter_form('games',$f);$where=SND_Analytics::where($f);$paged=max(1,(int)($_GET['paged']??1));$total=(int)$wpdb->get_var("SELECT COUNT(*) FROM $table WHERE $where");
        $rows=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE $where ORDER BY id DESC LIMIT 30 OFFSET %d",($paged-1)*30),ARRAY_A);
        echo '<table class="widefat striped"><thead><tr><th>Datum / ID</th><th>Igrač</th><th>Teritorija / igra</th><th>Faza / rezultat</th><th>Pokušaji / kviz</th><th>Pomoć / jezik</th></tr></thead><tbody>';
        foreach($rows as $r){echo '<tr><td><a href="'.esc_url(self::url('games',array('game'=>$r['id']))).'">'.esc_html($r['played_on']).' #'.(int)$r['id'].'</a>'.($r['legacy']?'<br>Stariji zapis':'').'</td><td>'.esc_html(self::user_label((int)$r['user_id'])).'</td><td>'.esc_html($r['territory_name']).'<br>'.esc_html($r['kind'].' / '.$r['mode']).'</td><td>'.esc_html($r['stage'].' / '.$r['outcome']).'</td><td>'.(int)$r['guesses'].'/'.(int)$r['max_guesses'].' · '.(int)$r['score'].'/'.(int)$r['question_count'].'</td><td>'.($r['assisted']?'Da':'Ne').' · '.esc_html($r['language'].' / '.$r['theme']).'</td></tr>';}
        if(!$rows){echo '<tr><td colspan="6">Nema zabeleženih partija za ove filtere.</td></tr>';}echo '</tbody></table>';self::pages('games',$total,30,$f);self::exports($f);echo '</div>';
    }
    private static function detail($r) {
        $d=json_decode($r['details'],true)?:array();echo '<h2>'.esc_html($r['territory_name']).' — #'.(int)$r['id'].'</h2><p>'.esc_html(self::user_label((int)$r['user_id'])).' · '.esc_html($r['kind'].' / '.$r['mode'].' / '.$r['stage'].' / '.$r['outcome']).'</p><p>Pokušaji: '.(int)$r['guesses'].'/'.(int)$r['max_guesses'].' · Kviz: '.(int)$r['score'].'/'.(int)$r['question_count'].' · Pomoć: '.($r['assisted']?'da':'ne').'</p><p>Početak (UTC): '.esc_html($r['started_at']?:'Nije poznat').' · '.(!empty($d['summary_only'])?'Vreme uvoza':'Poslednja aktivnost').': '.esc_html($r['last_at']).' · Kraj: '.esc_html($r['completed_at']?:'Nije poznat').' · Proteklo sekundi: '.esc_html($r['elapsed_seconds']??'Nije poznato').'</p>';
        if(!empty($d['summary_only'])){echo '<div class="notice notice-info"><p>Uvezen je samo stariji dnevni rezultat. Pojedinačni pokušaji, odgovori i tačno vreme nisu bili sačuvani.</p></div>';}
        echo '<h3>Pokušaji</h3><ol>';foreach($d['guesses']??array() as $g){echo '<li>'.esc_html($g['name']).' — '.(!empty($g['correct'])?'Pogođeno':esc_html(($g['km']??'').' km · '.($g['direction']['name']??''))).'</li>';}echo '</ol><h3>Dodatna pitanja</h3>';
        foreach($d['questions']??array() as $q){echo '<article class="snd-admin-question"><strong>'.(int)$q['index'].'. '.esc_html($q['question']).'</strong><p>Odgovor: '.esc_html($q['skipped']?'Preskočeno':$q['choice']).' · '.($q['correct']?'Tačno':'Netačno').'</p><p>Tačan odgovor: '.esc_html($q['correct_answer']).'</p>'.(!empty($q['correct_image'])?'<img width="140" src="'.esc_url($q['correct_image']).'" alt="">':'').'<p>'.(!empty($q['source_help'])?'Korišćen izvor kao pomoć. ':'').(!empty($q['technical_replacement'])?'Tehnička zamena slike.':'').'</p></article>';}
        echo '<h3>Tok partije (UTC)</h3><table class="widefat striped"><thead><tr><th>Vreme</th><th>Radnja</th><th>Faza</th></tr></thead><tbody>';foreach($d['activity']??array() as $a){echo '<tr><td>'.esc_html(isset($a['at'])?gmdate('Y-m-d H:i:s',is_numeric($a['at'])?(int)$a['at']:(strtotime($a['at'])?:0)):'').'</td><td>'.esc_html($a['action']??'').'</td><td>'.esc_html($a['stage']??'').'</td></tr>';}echo '</tbody></table><p><a href="'.esc_url(self::url('games',array('user_id'=>$r['user_id'],'from'=>'2000-01-01','to'=>SND_Game::date()))).'">← Istorija igrača</a></p>';
    }
    public static function translations() {
        self::begin('Prevodi interfejsa');$lang=sanitize_key($_GET['language']??'en');if(!isset(SND_I18n::languages()[$lang])){$lang='en';}$search=sanitize_text_field(wp_unslash($_GET['s']??''));$all=SND_I18n::data();$catalog=SND_I18n::catalog($lang);$rows=array();
        foreach($all as $key=>$values){if(!$search || stripos($key,$search)!==false || stripos($catalog[$key]??$key,$search)!==false){$rows[$key]=$values;}}
        $paged=max(1,(int)($_GET['paged']??1));$slice=array_slice($rows,($paged-1)*40,40,true);
        echo '<form method="get"><input type="hidden" name="page" value="snd-translations"><label>Jezik <select name="language">';foreach(SND_I18n::languages() as $key=>$label){echo '<option value="'.$key.'" '.selected($key,$lang,false).'>'.esc_html($label).'</option>';}echo '</select></label> <input type="search" aria-label="Pretraga prevoda" name="s" value="'.esc_attr($search).'"> <button class="button">Prikaži</button></form><p>Sačuvaj izmene na ovoj strani pre prelaska na sledeću. Oznake u vitičastim zagradama, npr. {count}, moraju ostati iste. HTML se ne prihvata. Nazivi i opisi znamenitosti menjaju se u uredniku teritorije.</p>';
        self::form('snd_translation_save');echo '<input type="hidden" name="language" value="'.$lang.'"><input type="hidden" name="paged" value="'.$paged.'"><input type="hidden" name="search" value="'.esc_attr($search).'"><table class="widefat"><thead><tr><th>Izvor / oznaka</th><th>Prevod</th><th>Vrati ugrađeni</th></tr></thead><tbody>';
        foreach($slice as $key=>$values){$hash=hash('sha256',$key);echo '<tr><td>'.esc_html($values['sr']??$key).'<br><small>'.esc_html($key).'</small></td><td><textarea aria-label="'.esc_attr($key).'" rows="3" name="translations['.$hash.']">'.esc_textarea($catalog[$key]??$key).'</textarea></td><td><input aria-label="Vrati ugrađeni prevod" type="checkbox" name="reset['.$hash.']" value="1"></td></tr>';}
        echo '</tbody></table>';submit_button('Sačuvaj prevode na ovoj strani');echo '</form>';self::pages('translations',count($rows),40,array('language'=>$lang,'s'=>$search));echo '</div>';
    }
    public static function validated_translations($lang,$input,$reset=array()) {
        $all=SND_I18n::data();$out=(array)get_option('snd_translations',array());
        foreach($all as $key=>$values){$hash=hash('sha256',$key);if(!empty($reset[$hash])){unset($out[$lang][$key]);continue;}if(!isset($input[$hash]) || !is_string($input[$hash])){continue;}
            $value=sanitize_textarea_field($input[$hash]);$base=$values[$lang]??($values['sr']??$key);
            preg_match_all('/\{[a-z_]+\}/',$base,$expected);preg_match_all('/\{[a-z_]+\}/',$value,$actual);sort($expected[0]);sort($actual[0]);
            if($value==='' || $expected[0]!==$actual[0]){return new WP_Error('snd_translation','Prevod je prazan ili su oznake u zagradama promenjene: '.$key);}
            if($value===$base){unset($out[$lang][$key]);}else{$out[$lang][$key]=$value;}
        }return $out;
    }
    public static function save_translations() {
        self::guard('snd_translation_save');$lang=sanitize_key($_POST['language']??'');if(!isset(SND_I18n::languages()[$lang])){wp_die('Nepoznat jezik.');}
        $out=self::validated_translations($lang,wp_unslash($_POST['translations']??array()),$_POST['reset']??array());if(is_wp_error($out)){wp_die(esc_html($out->get_error_message()));}update_option('snd_translations',$out,false);
        wp_safe_redirect(self::url('translations',array('saved'=>1,'language'=>$lang,'paged'=>max(1,(int)($_POST['paged']??1)),'s'=>sanitize_text_field(wp_unslash($_POST['search']??'')))));exit;
    }
    public static function csv_cell($value) {
        if(is_array($value)){$value=wp_json_encode($value,JSON_UNESCAPED_UNICODE);}$value=(string)$value;
        return preg_match('/^[\s\x00-\x1f]*[=+@-]/u',$value)?"'".$value:$value;
    }
    public static function export() {
        self::guard('snd_analytics_export');$type=sanitize_key($_GET['type']??'games');$format=($_GET['format']??'csv')==='json'?'json':'csv';if(!in_array($type,array('games','events','players'),true)){wp_die('Nepoznat izvoz.');}
        global $wpdb;$f=SND_Analytics::filters();nocache_headers();header('Content-Type: '.($format==='csv'?'text/csv':'application/json').'; charset=utf-8');header('Content-Disposition: attachment; filename="snd-'.$type.'-'.SND_Game::date().'.'.$format.'"');header('X-Content-Type-Options: nosniff');
        $stream=fopen('php://output','w');$first=true;$cursor=0;
        if($format==='csv'){fwrite($stream,"\xEF\xBB\xBF");}else{fwrite($stream,'[');}
        do{
            if($type==='players'){$q=new WP_User_Query(array('blog_id'=>get_current_blog_id(),'number'=>200,'offset'=>$cursor,'orderby'=>'ID','order'=>'ASC'));$rows=array();foreach($q->get_results() as $u){$rows[]=array('id'=>$u->ID,'name'=>$u->display_name,'login'=>$u->user_login,'email'=>$u->user_email,'registered_utc'=>$u->user_registered,'progress'=>SND_Progress::summary($u->ID));}$cursor+=count($rows);}
            else{$table=$type==='games'?SND_Analytics::games_table():SND_Analytics::events_table();$where=SND_Analytics::where($f,$type==='events');$rows=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE $where AND id>%d ORDER BY id ASC LIMIT 200",$cursor),ARRAY_A);if($rows){$cursor=(int)end($rows)['id'];}}
            foreach($rows as $row){if($format==='csv'){if($first){fputcsv($stream,array_keys($row),',','"','');}fputcsv($stream,array_map(array(__CLASS__,'csv_cell'),array_values($row)),',','"','');}else{if(!$first){fwrite($stream,',');}fwrite($stream,wp_json_encode($row,JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE));}$first=false;}
        }while(count($rows)===200);
        if($format==='json'){fwrite($stream,']');}fclose($stream);exit;
    }
    public static function clear() {
        self::guard('snd_analytics_clear');if(empty($_POST['confirm'])){wp_die('Potvrdi brisanje.');}global $wpdb;foreach(array(SND_Analytics::games_table(),SND_Analytics::events_table()) as $table){$wpdb->query("DELETE FROM $table");}
        // Do not silently restore erased historical summaries on the next cron.
        wp_clear_scheduled_hook('snd_analytics_backfill');update_option('snd_backfill_complete',1,false);wp_safe_redirect(self::url('settings',array('saved'=>1)));exit;
    }
}
