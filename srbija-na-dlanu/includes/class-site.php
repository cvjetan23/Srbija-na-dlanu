<?php
defined('ABSPATH') || exit;

final class SND_Site {
    const VIEWS = array('home','games','district','municipality','city','river','mountain','library','ranking','login','account');
    public static function view() {
        if (!is_page()) { return ''; }
        $view = get_post_meta(get_queried_object_id(), '_snd_app_view', true);
        return in_array($view,self::VIEWS,true) ? $view : '';
    }
    public static function url($view='home') {
        if($view==='games'){$view='home';}
        $pages=get_option('snd_pages',array());
        if(!empty($pages[$view]) && get_post_status($pages[$view])==='publish'){return SND_I18n::url(get_permalink($pages[$view]));}
        $slugs=array('home'=>'','games'=>'igre/','city'=>'gradovi/','river'=>'reke/','mountain'=>'planine/','district'=>'okruzi/','municipality'=>'opstine/','library'=>'baza-znanja/','ranking'=>'rang-lista/','login'=>'prijava/','account'=>'nalog/');
        return SND_I18n::url(home_url('/srbija-na-dlanu/'.($slugs[$view]??'')));
    }
    public static function install() {
        SND_Analytics::install();SND_Ranking::install();
        $root=get_page_by_path('srbija-na-dlanu',OBJECT,'page');
        if(!$root){$id=wp_insert_post(array('post_type'=>'page','post_status'=>'publish','post_title'=>'Srbija na dlanu','post_name'=>'srbija-na-dlanu','post_content'=>'[srbija_na_dlanu]'),true);if(is_wp_error($id)){return $id;}$root=get_post($id);}
        $pages=array('home'=>$root->ID);
        $definitions=array('games'=>array('igre','Igre'),'city'=>array('gradovi','Gradovi i naselja'),'river'=>array('reke','Reke'),'mountain'=>array('planine','Planine'),'district'=>array('okruzi','Okruzi'),'municipality'=>array('opstine','Opštine i gradovi'),'library'=>array('baza-znanja','Baza znanja'),'ranking'=>array('rang-lista','Rang lista'),'login'=>array('prijava','Prijava'),'account'=>array('nalog','Moj nalog'));
        foreach($definitions as $view=>$details){
            $page=get_page_by_path('srbija-na-dlanu/'.$details[0],OBJECT,'page');
            if(!$page){$id=wp_insert_post(array('post_type'=>'page','post_status'=>'publish','post_parent'=>$root->ID,'post_title'=>$details[1],'post_name'=>$details[0],'post_content'=>''),true);if(is_wp_error($id)){return $id;}$page=get_post($id);}
            $pages[$view]=$page->ID;
        }
        foreach($pages as $view=>$id){
            if(!metadata_exists('post',$id,'_snd_original_template')){add_post_meta($id,'_snd_original_template',get_post_meta($id,'_wp_page_template',true));}
            update_post_meta($id,'_snd_app_view',$view);
        }
        update_option('snd_pages',$pages,false);
        update_option('snd_site_version',SND_VERSION,false);
        flush_rewrite_rules(false);
        if(function_exists('wp_cache_clear_cache')){wp_cache_clear_cache();}
        return $pages;
    }
    public static function maybe_install() {
        if(current_user_can('manage_options') && get_option('snd_site_version')!==SND_VERSION){self::install();}
    }
    public static function template($template) {return self::view()?SND_DIR.'templates/app.php':$template;}
    public static function prepare() {
        if(!self::view()){return;}
        if(self::view()==='games'){wp_safe_redirect(self::url('home'),301);exit;}
        if(self::view()==='library' && ($_GET['opstina']??'')==='m-beograd' && in_array('m-beograd',SND_Data::retired_ids(),true)){wp_safe_redirect(add_query_arg('okrug','rs-00',self::url('library')),301);exit;}
        if(self::view()==='library' && is_string($_GET['opstina']??null) && $_GET['opstina']!=='m-beograd' && in_array($_GET['opstina'],SND_Data::retired_ids(),true)){wp_safe_redirect(SND_Catalogue::url('municipality',array('grad'=>sanitize_key($_GET['opstina']))),301);exit;}
        // Account HTML contains private data or short-lived nonces; never cache it.
        if(is_user_logged_in() || in_array(self::view(),array('login','account','ranking'),true)){
            if(!defined('DONOTCACHEPAGE')){define('DONOTCACHEPAGE',true);}nocache_headers();
        }
        if(!current_theme_supports('title-tag')){add_action('wp_head',function(){echo '<title>'.esc_html(wp_get_document_title()).'</title>';},1);}
        if(!class_exists('SlimSEO\\MetaTags\\Title')){remove_action('wp_head','rel_canonical');}
        SND_Accounts::handle();SND_Ranking::handle_profile();
    }
    public static function assets() {
        $view=self::view();if(!$view){return;}SND_I18n::enqueue();wp_enqueue_style('snd-ranking',SND_URL.'assets/ranking.css',array('snd-themes'),SND_VERSION);
        wp_enqueue_script('snd-ui',SND_URL.'assets/ui.js',array('snd-i18n'),SND_VERSION,true);
        wp_enqueue_style('snd-app',SND_URL.'assets/app.css',array(),SND_VERSION);wp_enqueue_style('snd-themes',SND_URL.'assets/themes.css',array('snd-app'),SND_VERSION);wp_enqueue_style('snd-explore',SND_URL.'assets/explore.css',array('snd-themes'),SND_VERSION);wp_enqueue_style('snd-catalogue',SND_URL.'assets/catalogue.css',array('snd-explore'),SND_VERSION);wp_enqueue_style('snd-interface',SND_URL.'assets/interface.css',array('snd-app','snd-themes','snd-explore','snd-catalogue','snd-ranking'),SND_VERSION);
        if(in_array($view,SND_Kinds::ALL,true)){snd_enqueue_game_assets();}
        if($view==='library'){wp_enqueue_script('snd-library',SND_URL.'assets/library.js',array(),SND_VERSION,true);wp_enqueue_style('snd-atlas',SND_URL.'assets/atlas.css',array('snd-themes'),SND_VERSION);}
    }
    public static function trim_theme_assets() {
        if(!self::view()){return;}
        foreach(array(wp_styles(),wp_scripts()) as $registry){
            foreach($registry->queue as $handle){
                if(strpos($handle,'snd-')===0){continue;}
                $src=$registry->registered[$handle]->src??'';
                $theme_or_builder=preg_match('~/(themes/|plugins/(?:elementor(?:-pro)?|astra-addon|header-footer-elementor|ultimate-elementor|ultimate-addons-for-elementor-lite)/|uploads/(?:elementor|astra-addon)/)~',(string)$src);
                $builder_handle=preg_match('/^(?:astra|ast-|elementor|uael|hfe|ehf)/',$handle);
                $unused_core=in_array($handle,array('global-styles','classic-theme-styles','wp-block-library','wp-block-library-theme'),true);
                if($theme_or_builder || $builder_handle || $unused_core){
                    if($registry instanceof WP_Styles){wp_dequeue_style($handle);}else{wp_dequeue_script($handle);}
                }
            }
        }
    }
    public static function compass() {return '<svg class="snd-compass" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.4"/><path d="m16 8-2.6 5.4L8 16l2.6-5.4L16 8Z" fill="currentColor"/><path d="M12 1v3m0 16v3M1 12h3m16 0h3" stroke="currentColor"/></svg>';}
    public static function header() {
        $view=self::view();
        echo '<header class="snd-app-header"><a class="snd-app-brand" href="'.esc_url(self::url()).'">'.self::compass().'<span data-snd-original>'.esc_html(SND_Settings::get('app_name')).'</span></a><nav class="snd-app-nav" aria-label="Glavni meni">';
        $items=array('home'=>'Početna','games'=>'Igre','library'=>'Baza znanja','ranking'=>'Rang lista');
        foreach($items as $key=>$name){
            if($key==='games'){
                $active=$view==='games' || in_array($view,SND_Kinds::ALL,true);
                echo '<details class="snd-games-menu"><summary'.($active?' aria-current="page"':'').' aria-label="Izaberi igru">Igre<svg viewBox="0 0 16 16" aria-hidden="true"><path d="m4 6 4 4 4-4"/></svg></summary><div class="snd-games-dropdown">';
                foreach(SND_Kinds::available() as $kind=>$label){echo '<a data-menu-game="'.esc_attr($kind).'" href="'.esc_url(self::url($kind)).'"'.($view===$kind?' aria-current="page"':'').'><span>'.esc_html($label).'</span><small>'.esc_html(self::game_description($kind)).'</small></a>';}
                echo '</div></details>';continue;
            }
            echo '<a href="'.esc_url(self::url($key)).'" '.($key===$view?'aria-current="page"':'').'>'.esc_html($name).'</a>';
        }
        echo '</nav>';self::preferences();$account=is_user_logged_in()?'account':'login';$label=SND_I18n::t(is_user_logged_in()?'Moj nalog':'Prijava');
        echo '<a class="snd-account-link" href="'.esc_url(self::url($account)).'" aria-label="'.esc_attr($label).'" title="'.esc_attr($label).'"'.($account===$view?' aria-current="page"':'').'><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="8" r="3.5"/><path d="M4.5 21v-2a7.5 7.5 0 0 1 15 0v2"/></svg></a></header>';
    }
    private static function preferences() {
        $languages=SND_I18n::languages();$lang=SND_I18n::lang();$base=self::preference_url();
        echo '<div class="snd-preferences" role="group" aria-label="Podešavanja prikaza"><div class="snd-preference-group"><details class="snd-language-picker"><summary aria-label="'.esc_attr(SND_I18n::t('Izaberi jezik')).'">'.self::preference_icon('globe').'<span data-snd-original>'.esc_html($languages[$lang]).'</span><svg class="snd-picker-chevron" viewBox="0 0 16 16" aria-hidden="true"><path d="m4 6 4 4 4-4"/></svg></summary><div class="snd-language-options">';
        foreach($languages as $code=>$label){if(!in_array($code,SND_Settings::get('languages'),true)){continue;}
            echo '<a href="'.esc_url(SND_I18n::url($base,$code)).'" lang="'.esc_attr($code==='sr'?'sr-Latn':$code).'" data-language="'.esc_attr($code).'"'.($code===$lang?' aria-current="true"':'').'><span class="snd-language-code" aria-hidden="true">'.esc_html(strtoupper($code)).'</span><span data-snd-original>'.esc_html($label).'</span><span class="snd-language-check" aria-hidden="true">'.($code===$lang?'✓':'').'</span></a>';
        }
        echo '</div></details></div>';
        if(SND_Settings::get('theme_switch')){
            echo '<div class="snd-preference-group"><form class="snd-theme-options" method="get" action="'.esc_url(self::canonical()).'" aria-label="Tema">';
            $args=array();parse_str(wp_parse_url($base,PHP_URL_QUERY)?:'',$args);foreach($args as $key=>$value){if(is_scalar($value)){echo '<input type="hidden" name="'.esc_attr($key).'" value="'.esc_attr($value).'">';}}
            foreach(array('light'=>array('Svetla tema','sun'),'dark'=>array('Tamna tema','moon'),'auto'=>array('Prema uređaju','monitor')) as $key=>$entry){echo '<button type="submit" name="snd_theme" value="'.$key.'" data-snd-theme-choice="'.$key.'" aria-pressed="'.(SND_I18n::theme()===$key?'true':'false').'" aria-label="'.esc_attr(SND_I18n::t($entry[0])).'" title="'.esc_attr(SND_I18n::t($entry[0])).'">'.self::preference_icon($entry[1]).'</button>';}
            echo '</form></div>';
        }
        echo '</div>';
    }
    private static function preference_icon($name) {
        $paths=array('globe'=>'<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c5 5 5 13 0 18-5-5-5-13 0-18Z"/>','sun'=>'<circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M2 12h2m16 0h2M5 5l1.5 1.5m11 11L19 19M5 19l1.5-1.5m11-11L19 5"/>','moon'=>'<path d="M21 13.2A9 9 0 0 1 10.8 3a9 9 0 1 0 10.2 10.2Z"/>','monitor'=>'<rect x="3" y="4" width="18" height="13" rx="2"/><path d="M8 21h8m-4-4v4"/>');
        return '<svg class="snd-preference-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$paths[$name].'</svg>';
    }
    public static function preference_url() {
        $url=self::canonical();foreach(array('mode','date','pretraga','board','period','game','rank_date','rank_month','rank_page','oblast','sliv','sort','grad') as $key){if(isset($_GET[$key]) && is_string($_GET[$key])){$url=add_query_arg($key,sanitize_text_field(wp_unslash($_GET[$key])),$url);}}return $url;
    }
    public static function game_root($atts=array()) {
        snd_enqueue_game_assets();$id=wp_unique_id('snd-');$kind=in_array($atts['kind']??'',SND_Kinds::ALL,true)?$atts['kind']:'';
        return '<div id="'.esc_attr($id).'" class="snd-game" lang="'.esc_attr(SND_I18n::html_lang()).'" data-api="'.esc_url(rest_url('srbija-na-dlanu/v1/')).'" data-assets="'.esc_url(SND_URL.'assets/').'" data-ajax="'.esc_url(admin_url('admin-ajax.php')).'" data-login="'.esc_url(self::url('login')).'" data-account="'.esc_url(self::url('account')).'" data-kind="'.esc_attr($kind).'" data-games-url="'.esc_url(self::url('games')).'" data-city-url="'.esc_url(self::url('city')).'" data-river-url="'.esc_url(self::url('river')).'" data-mountain-url="'.esc_url(self::url('mountain')).'" data-district-url="'.esc_url(self::url('district')).'" data-municipality-url="'.esc_url(self::url('municipality')).'" data-regions-url="'.esc_url(add_query_arg('podela','regioni',self::url('library'))).'" data-lang="'.esc_attr(SND_I18n::lang()).'" data-shell="'.(self::view()?'1':'0').'"><div class="snd-loading" role="status">'.self::loader().'Učitavanje igre…</div><noscript><style>.snd-loading{display:none!important}</style>Za igranje uključi JavaScript.</noscript></div>';
    }
    public static function loader() {
        return '<span class="snd-compass-loader" aria-hidden="true"><svg viewBox="0 0 48 48" fill="none"><circle cx="24" cy="24" r="19" stroke="currentColor" stroke-width="1.5"/><path d="M24 1v8m0 30v8M1 24h8m30 0h8" stroke="currentColor" stroke-width="1.5"/><g class="snd-loader-needle"><path d="m32 16-5.5 10.5L16 32l5.5-10.5L32 16Z" fill="currentColor"/></g></svg></span>';
    }
    public static function silhouette($territory,$class='') {return '<svg class="snd-atlas-shape '.esc_attr($class).'" viewBox="0 0 360 360" role="img" aria-label="Obris: '.esc_attr($territory['name']).'"><path d="'.esc_attr($territory['path']).'"/></svg>';}
    public static function content() {
        $view=self::view();
        if(in_array($view,SND_Kinds::ALL,true) && !SND_Settings::get($view.'_enabled')){echo '<section class="snd-auth-card"><h1>'.esc_html(SND_Kinds::labels()[$view]).'</h1><p>Nove partije ovog tipa trenutno nisu dostupne.</p></section>';return;}
        if(in_array($view,array('home','games'),true)){self::home();}
        elseif(in_array($view,SND_Kinds::ALL,true)){echo self::game_root(array('kind'=>$view));}
        elseif($view==='library'){self::library();}
        elseif($view==='ranking'){SND_Ranking::page();}
        elseif($view==='login'){SND_Accounts::form();}
        elseif($view==='account'){self::account();}
    }
    public static function game_description($kind){
        return array('district'=>'Prepoznaj okrug po obrisu.','municipality'=>'Prepoznaj opštinu ili grad po obrisu.','city'=>'Pronađi naselje označeno pinom.','river'=>'Prepoznaj istaknuti tok reke.','mountain'=>'Pronađi planinu označenu na mapi.')[$kind]??'';
    }
    private static function home() {
        $progress=is_user_logged_in()?SND_Progress::get(get_current_user_id()):array();$today=SND_Game::date();
        echo '<section class="snd-home-intro"><h1 data-snd-original>'.esc_html(self::view()==='games'?SND_I18n::t('Igre'):SND_Settings::get('app_name')).'</h1><p>Pet geografskih izazova. Svaki dan novo otkriće.</p></section><div id="snd-games" class="snd-home-games snd-five-games snd-game-directory">';
        foreach(SND_Kinds::available() as $kind=>$label){
            $pool=SND_Data::pool($kind);$example=$pool[$kind==='district'?'rs-12':'m-cacak']??reset($pool);if(!$example){continue;}
            $picture=SND_Kinds::is_explore($kind)?self::game_icon($kind):self::silhouette($example);
            $day=$progress[$kind]['days'][$today]??null;$active=is_user_logged_in()?SND_Progress::active(get_current_user_id(),$kind,'daily'):null;
            $status=$day?SND_I18n::t('Završeno').' · '.(int)($day['points']??0).'/100':($active?SND_I18n::t('Nastavi današnji izazov'):SND_I18n::t('Igra dana'));
            echo '<a class="snd-home-game" data-game-kind="'.esc_attr($kind).'" href="'.esc_url(self::url($kind)).'"><div class="snd-directory-icon">'.$picture.'</div><div class="snd-directory-copy"><h2>'.esc_html($label).'</h2><p class="snd-game-description">'.esc_html(self::game_description($kind)).'</p></div><div class="snd-directory-bottom"><span class="snd-daily-status" data-daily-kind="'.esc_attr($kind).'" data-daily-date="'.esc_attr($today).'">'.esc_html($status).'</span><span class="snd-card-action"><span>'.($day?'Pogledaj rezultat':($active?'Nastavi':'Igraj')).'</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 12h16m-6-6 6 6-6 6"/></svg></span></div></a>';
        }
        echo '</div><section class="snd-home-description"><h2>Upoznaj Srbiju kroz igru</h2><p>Od obrisa okruga i opština do gradova, reka i planina — svakog dana pronađi nešto novo na mapi. Pokušaji daju tragove, a dodatna pitanja i fotografije pomažu da zapamtiš ono što si otkrio.</p><p>Igraj po jedan izazov iz svake igre dnevno, sačuvaj progres na nalogu i uporedi rezultat na rang listi. Kada poželiš da istražiš više, otvori bazu znanja.</p></section>';self::home_rules();
        echo '<div class="snd-home-links"><section class="snd-home-library" aria-labelledby="snd-discover-title"><div class="snd-library-art"><img src="'.esc_url(SND_URL.'assets/library-atlas.svg').'" width="700" height="820" loading="lazy" decoding="async" alt="'.esc_attr(SND_I18n::t('Ilustracija Srbije sa okruzima, rekama i planinama')).'"></div><div class="snd-library-invitation"><p class="snd-section-eyebrow">Istraži i nauči</p><h2 id="snd-discover-title">Upoznaj Srbiju</h2><p>Istraži okruge, gradove, reke i planine. Pronađi zanimljivosti, upoznaj mesta i spremi se za sledeću igru.</p><a class="snd-app-button snd-button-outline" href="'.esc_url(self::url('library')).'">Baza znanja <span aria-hidden="true">→</span></a></div></section><section class="snd-home-signin" aria-labelledby="snd-join-title"><p class="snd-section-eyebrow">'.(is_user_logged_in()?'Tvoje geografsko putovanje':'Tvoje mesto na mapi').'</p><h2 id="snd-join-title">'.(is_user_logged_in()?'Nastavi svoje putovanje':'Sačuvaj svoj napredak').'</h2><p>'.(is_user_logged_in()?'Sve tvoje partije i rezultati na jednom mestu.':'Jedan nalog za sve tvoje geografske izazove.').'</p><ul class="snd-account-benefits"><li><span aria-hidden="true">✓</span>Nastavi na drugom uređaju</li><li><span aria-hidden="true">✓</span>Sačuvaj rezultate</li><li><span aria-hidden="true">✓</span>Prati plasman</li></ul><a class="snd-app-button snd-button-outline" href="'.esc_url(self::url(is_user_logged_in()?'account':'login')).'">'.(is_user_logged_in()?'Moj nalog':'Prijavi se').'</a></section></div>';

    }
    public static function game_icon($kind){
        $paths=array('city'=>'<path d="M70 160h180M85 160V95h42v65m20 0V65h70v95m-87-30h10m-10 15h10m27-65h12m-12 22h12m-12 22h12"/><path d="M160 28a17 17 0 1 0-34 0c0 15 17 31 17 31s17-16 17-31Z"/><circle cx="143" cy="28" r="5"/>','river'=>'<path d="M95 35c130 32-80 52 38 82s92 41-12 70M78 44c130 32-80 52 38 82s92 41-12 70M168 37l16 10-14 8m-43 88 16 10-14 8"/>','mountain'=>'<path d="m40 172 92-129 57 79 25-33 66 83H40Z"/><path d="m105 81 27 23 22-30m-54 53 20-6 13 15 17-11 22 19m28-37 14 16 13-13"/>');
        return '<svg class="snd-game-card-icon" viewBox="0 0 320 220" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.($paths[$kind]??'').'</svg>';
    }
    private static function home_rules() {
        $pool=SND_Data::pool('district');$example=$pool['rs-12']??reset($pool);if(!$example){return;}
        $pictures=array('<path class="snd-rule-fill" d="'.esc_attr($example['path']).'" transform="translate(36 2) scale(.28)"/>','<rect x="14" y="35" width="112" height="40" rx="8"/><path d="M26 55h54m-52-10v20m68-10h19m-7-7 7 7-7 7"/>','<circle cx="46" cy="54" r="25"/><path d="M46 37v34m-10-22 10-12 10 12M85 43h35m-35 18h25"/>','<rect x="18" y="18" width="45" height="35" rx="5"/><rect x="76" y="18" width="45" height="35" rx="5"/><rect x="18" y="65" width="45" height="35" rx="5"/><rect x="76" y="65" width="45" height="35" rx="5"/><path d="m29 36 9 8 16-17"/>');
        $steps=array(array('Pogledaj mapu','Prepoznaj obris, pin naselja ili planine, ili označeni tok reke. Sever je gore.'),array('Unesi odgovor',SND_I18n::t('Imaš {count} pokušaja da pronađeš traženu teritoriju.',array('count'=>SND_Settings::get('max_guesses')))),array('Prati tragove','Pinovi, kilometri i strelice vode ka odgovoru. Kod reka uporedi označene tokove.'),array('Proveri znanje','Odgovori na četiri pitanja, prepoznaj fotografiju ili zastavu i osvoji do 100 poena.'));
        echo '<section class="snd-home-rules" aria-labelledby="snd-rules-title"><h2 id="snd-rules-title">Kako se igra?</h2><ol role="list">';
        foreach($steps as $i=>$step){echo '<li><div class="snd-rule-picture"><svg viewBox="0 0 140 115" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$pictures[$i].'</svg></div><span class="snd-rule-number" aria-hidden="true">'.($i+1).'</span><h3>'.esc_html($step[0]).'</h3><p>'.esc_html($step[1]).'</p></li>';}
        echo '</ol></section>';
    }
    private static function library() {
        if(SND_Explore_Admin::library()){return;}
        SND_Explore_Admin::library_tabs();
        $districts=SND_Data::pool('district');if(isset($_GET['podela']) && $_GET['podela']==='regioni'){self::regions();return;}$key=isset($_GET['okrug'])&&is_string($_GET['okrug'])?sanitize_key(wp_unslash($_GET['okrug'])):'';
        $municipality=SND_Atlas::municipality();if($municipality && isset($districts[$municipality['district_id']])){SND_Atlas::municipality_detail($municipality,$districts[$municipality['district_id']]);return;}
        if($key && isset($districts[$key])){self::district_detail($districts[$key]);return;}
        $query=isset($_GET['pretraga'])&&is_string($_GET['pretraga'])?sanitize_text_field(wp_unslash($_GET['pretraga'])):'';
        echo '<section class="snd-library-head"><h1>Baza znanja</h1><p>Istraži Srbiju: od okruga do opštine.</p></section>'.SND_Atlas::map().'<section class="snd-library-list"><h2>Svi okruzi</h2><form class="snd-library-search" method="get" action="'.esc_url(self::url('library')).'"><input type="hidden" name="lang" value="'.esc_attr(SND_I18n::lang()).'">'.self::library_query_fields().'<label for="snd-library-search" class="snd-app-sr-only">Pronađi okrug</label><input id="snd-library-search" name="pretraga" type="search" placeholder="Pronađi okrug…" value="'.esc_attr($query).'"><button class="snd-app-button" type="submit">Pronađi</button></form><div class="snd-district-grid">';
        $matches=0;$needle=SND_Text::key($query);
        foreach($districts as $t){
            $search=$t['name'].' '.$t['seat'].' '.implode(' ',$t['aliases']??array());foreach(array('en','de','ru') as $language){$search.=' '.SND_I18n::t($t['name'],array(),$language);}if($t['seat']==='Beograd'){$search.=' Belgrade Belgrad Белград';}$show=!$needle||strpos(SND_Text::key($search),$needle)!==false;if($show){$matches++;}
            echo '<a class="snd-district-card" data-atlas-item="'.esc_attr($t['id']).'" data-search="'.esc_attr($search).'" href="'.esc_url(add_query_arg('okrug',$t['id'],self::url('library'))).'" '.(!$show?'hidden':'').'>'.self::silhouette($t).'<h2>'.esc_html($t['name']).'</h2><p>'.esc_html(SND_I18n::place($t['seat'])).'</p></a>';
        }
        echo '</div><p class="snd-library-empty" role="status" '.($matches?'hidden':'').'>Nema okruga koji odgovara pretrazi.</p></section><p class="snd-library-scope">Uključeni su 29 upravnih okruga i Grad Beograd kao posebna celina, sa Kosovom i Metohijom. <a href="'.esc_url(add_query_arg('podela','regioni',self::url('library'))).'">Šire celine korišćene u igri</a>.</p>';
        SND_Catalogue::featured();
    }
    private static function library_query_fields() {
        $args=array();parse_str(wp_parse_url(self::url('library'),PHP_URL_QUERY)?:'',$args);$html='';
        foreach(array('page_id','pagename') as $key){if(isset($args[$key]) && is_string($args[$key])){$html.='<input type="hidden" name="'.esc_attr($key).'" value="'.esc_attr($args[$key]).'">';}}return $html;
    }
    private static function district_detail($t) {
        $members=array_filter(SND_Data::pool('municipality'),function($m)use($t){return $m['district_id']===$t['id'];});$flag=SND_Flags::for_territory($t);
        echo '<article class="snd-district-detail"><a class="snd-back-link" href="'.esc_url(self::url('library')).'">← Svi okruzi</a><h1>'.esc_html($t['name']).'</h1>'.SND_Atlas::map($t).'<dl class="snd-facts"><div><dt>'.($t['id']==='rs-00'?'Celina':($t['region']==='Kosovo i Metohija'?'Središte u korišćenoj podeli':'Sedište okruga')).'</dt><dd>'.esc_html(SND_I18n::place($t['seat'])).'</dd></div><div><dt>Širi položaj</dt><dd>'.esc_html(SND_I18n::region($t['region'])).'</dd></div><div><dt>Stanovništvo</dt><dd>'.(!empty($t['population'])?esc_html(SND_I18n::number($t['population'])).' <small>(Popis '.esc_html($t['year']).')</small>':'Nema uporedivog podatka iz Popisa RZS 2022.').'</dd></div><div><dt>Opštine i gradovi</dt><dd>'.count($members).'</dd></div></dl>';
        if($t['region']==='Kosovo i Metohija'){echo '<p class="snd-detail-note">Koristi se podela na pet okruga i 29 opština Kosova i Metohije. Naziv središta odnosi se na korišćenu teritorijalnu podelu, ne na adresu izmeštene uprave. Popis RZS 2022. na ovoj teritoriji nije sproveden.</p>';}
        if($t['id']==='rs-00'){echo '<p class="snd-detail-note">Grad Beograd je posebna administrativna celina i nije upravni okrug. Njegovih 17 gradskih opština uključeno je u igru opština. Stanovništvo se računa bez dupliranja cele gradske teritorije.</p>';}
        echo '<h2>Pripadajuće opštine i gradovi</h2>';SND_Atlas::member_list($t);
        if(!empty($t['landmark']['fact'])){echo '<h2>Jedna zanimljivost</h2><p>'.esc_html(SND_I18n::fact($t)).'</p>';}
        if($flag){echo '<h2>Zastava mesta '.esc_html(SND_I18n::place($flag['name'])).'</h2><figure class="snd-knowledge-flag"><img src="'.esc_url(SND_Data::image_url($flag['image'])).'" alt="Zastava mesta '.esc_attr(SND_I18n::place($flag['name'])).'"><figcaption>'.esc_html($flag['credit'].' · '.$flag['license']).' · <a href="'.esc_url($flag['source']).'" target="_blank" rel="noopener noreferrer">Izvor</a></figcaption></figure>';}
        echo '<div class="snd-detail-sources"><h2>Izvori</h2><a href="'.esc_url($t['population_source']?:'https://www.stat.gov.rs/'). '" target="_blank" rel="noopener noreferrer">Republički zavod za statistiku</a><span> · </span><a href="'.esc_url($t['region']==='Kosovo i Metohija'?'https://kro.mre.gov.rs/arcgis/rest/services/IMIS/MINING_PUBLIC/MapServer':'https://www.geoboundaries.org/').'" target="_blank" rel="noopener noreferrer">Granice teritorije</a></div><a class="snd-app-button" href="'.esc_url(self::url('district')).'">Igraj okruge</a></article>';
    }
    private static function regions() {
        echo '<article class="snd-district-detail"><a class="snd-back-link" href="'.esc_url(self::url('library')).'">← Baza znanja</a><h1>Šire celine u igri</h1><p class="snd-region-note">Nazivi ispod služe orijentaciji i pomoći u igri. Ovo je urednička podela projektne baze, a ne zvanična statistička ili upravna regionalizacija. Ne koriste se kao bodovano zamensko pitanje.</p><table class="snd-region-table"><thead><tr><th>Okrug / posebna celina</th><th>Širi položaj u igri</th></tr></thead><tbody>';
        foreach(SND_Data::pool('district') as $t){echo '<tr><td>'.esc_html($t['name']).'</td><td>'.esc_html(SND_I18n::region($t['region'])).'</td></tr>';}
        echo '</tbody></table><p class="snd-region-note">Opština ili grad nasleđuje širi položaj svog okruga u ovoj bazi. Administrativna pripadnost i popisni podaci prikazani su odvojeno, sa izvorima na detaljima okruga. Kosovo i Metohija obuhvataju pet okruga i 29 opština prema korišćenoj podeli.</p></article>';
    }
    public static function current_district() {
        if(self::view()!=='library'){return null;}
        $m=SND_Atlas::municipality();if($m){$pool=SND_Data::pool('district');return $pool[$m['district_id']]??null;}
        $key=isset($_GET['okrug']) && is_string($_GET['okrug'])?sanitize_key(wp_unslash($_GET['okrug'])):'';
        $pool=SND_Data::pool('district');return $pool[$key]??null;
    }
    public static function document_title($original='') {
        $view=self::view();if(!$view){return $original;}
        $object=SND_Explore_Admin::current();if($object){return SND_I18n::t('{name} — Baza znanja — Srbija na dlanu',array('name'=>SND_Explore::name($object)));}
        $m=SND_Atlas::municipality();if($m){return SND_I18n::t('{name} — Baza znanja — Srbija na dlanu',array('name'=>SND_I18n::territory_name($m)));}
        $t=self::current_district();if($t){return SND_I18n::t('{name} — Baza znanja — Srbija na dlanu',array('name'=>SND_I18n::territory_name($t)));}
        if($view==='library' && ($_GET['podela']??'')==='regioni'){return SND_I18n::text('Šire celine u igri — Srbija na dlanu');}
        $labels=array('games'=>'Igre','city'=>'Gradovi i naselja','river'=>'Reke','mountain'=>'Planine','home'=>'Geografska igra','district'=>'Okruzi','municipality'=>'Opštine i gradovi','library'=>'Baza znanja','ranking'=>'Rang lista','login'=>'Prijava','account'=>'Moj nalog');
        return str_replace('Srbija na dlanu',SND_Settings::get('app_name'),SND_I18n::text($view==='home'?'Srbija na dlanu — Geografska igra':$labels[$view].' — Srbija na dlanu'));
    }
    public static function description($original='') {
        $view=self::view();if(!$view){return $original;}
        $m=SND_Atlas::municipality();if($m){$district=self::current_district();return SND_I18n::t('{name}: obris, položaj u okrugu {district}, stanovništvo i izvori.',array('name'=>SND_I18n::territory_name($m),'district'=>SND_I18n::territory_name($district)));}
        $t=self::current_district();
        if($t){return SND_I18n::t('{name}: obris teritorije, sedište {seat}, pripadajuće opštine, stanovništvo i izvori. Baza znanja igre Srbija na dlanu.',array('name'=>SND_I18n::territory_name($t),'seat'=>SND_I18n::place($t['seat'])));}
        if(SND_Settings::get('daily_only') && in_array($view,array('home','district','municipality','library'),true)){return SND_I18n::t('Pet geografskih izazova. Svaki dan novo otkriće.');}
        $values=array('home'=>'Prepoznaj okruge, opštine i gradove Srbije po obrisu. Dnevni izazov, vežbanje i dodatna pitanja, uključujući Kosovo i Metohiju.','district'=>'Pogodi okrug Srbije po obrisu, prati udaljenost i smer, pa proveri znanje kroz dodatna pitanja. Dnevni izazov i vežbanje.','municipality'=>SND_I18n::t('Prepoznaj {count} opština i gradova Srbije, sa Kosovom i Metohijom. Igraj dnevni izazov ili vežbaj.',array('count'=>count(SND_Data::pool('municipality')))),'library'=>'Pronađi svih 30 okružnih celina Srbije: obris, sedište, stanovništvo, pripadajuće opštine i izvori.','login'=>'Prijavi se da sačuvaš rezultate i nastaviš igru Srbija na dlanu na drugom uređaju.','account'=>'Tvoji rezultati, upoznate teritorije i niz igranja u igri Srbija na dlanu.');
        if(in_array($view,array('games','city','river','mountain'),true)){return SND_I18n::t('Dnevne geografske igre: gradovi i naselja, reke i planine. Mapa, šest pokušaja, fotografije i rang lista.');}
        return $view==='ranking'?SND_I18n::t('Rang lista dnevnih izazova za svih pet igara.'):SND_I18n::t($values[$view]);
    }
    public static function canonical() {
        $url=self::url(self::view()?:'home');$t=self::current_district();
        $m=SND_Atlas::municipality();if($m){return SND_Atlas::url($m);}
        if($t){return add_query_arg('okrug',$t['id'],self::url('library'));}
        if(self::view()==='library' && ($_GET['podela']??'')==='regioni'){return add_query_arg('podela','regioni',self::url('library'));}
        if(self::view()==='library' && in_array($_GET['tip']??'',SND_Kinds::EXPLORE,true)){$url=add_query_arg('tip',sanitize_key($_GET['tip']),$url);$object=SND_Explore_Admin::current();if($object){$url=add_query_arg('objekat',$object['id'],$url);}}
        return $url;
    }
    public static function canonical_filter($original) {return self::view()?self::canonical():$original;}
    public static function locale_filter($original) {return self::view()?SND_I18n::wp_locale():$original;}
    public static function brand_filter($original) {return self::view()?SND_Settings::get('app_name'):$original;}
    public static function schema($graph) {
        if(!self::view() || !is_array($graph)){return $graph;}
        $canonical=self::canonical();$home=self::url();$mapping=array();
        foreach($graph as &$node){
            if(!is_array($node)){continue;}
            if(isset($node['inLanguage'])){$node['inLanguage']=SND_I18n::html_lang();}
            $types=(array)($node['@type']??array());
            if(in_array('WebSite',$types,true)){
                if(isset($node['@id'])){$mapping[$node['@id']]=$home.'#website';}
                $node['@id']=$home.'#website';$node['url']=$home;$node['name']=SND_Settings::get('app_name');$node['inLanguage']=SND_I18n::html_lang();
                $node['potentialAction']=array('@type'=>'SearchAction','target'=>array('@type'=>'EntryPoint','urlTemplate'=>self::url('library').(strpos(self::url('library'),'?')===false?'?':'&').'pretraga={search_term_string}'),'query-input'=>'required name=search_term_string');
            }
            if(in_array('WebPage',$types,true)){$node['@id']=$canonical.'#webpage';$node['url']=$canonical;$node['name']=self::document_title();$node['description']=self::description();$node['inLanguage']=SND_I18n::html_lang();$node['isPartOf']=array('@id'=>$home.'#website');}
            if(in_array('BreadcrumbList',$types,true)){
                $crumbs=array(array('@type'=>'ListItem','position'=>1,'name'=>SND_Settings::get('app_name'),'item'=>$home));
                if(self::view()!=='home'){$crumbs[]=array('@type'=>'ListItem','position'=>2,'name'=>self::view()==='library'?SND_I18n::t('Baza znanja'):self::document_title(),'item'=>self::url(self::view()));}
                $t=self::current_district();$m=SND_Atlas::municipality();if($t){$crumbs[]=array('@type'=>'ListItem','position'=>3,'name'=>SND_I18n::territory_name($t),'item'=>SND_Atlas::url($t));}if($m){$crumbs[]=array('@type'=>'ListItem','position'=>4,'name'=>SND_I18n::territory_name($m),'item'=>$canonical);}
                $node['itemListElement']=$crumbs;
            }
        }
        unset($node);
        $walk=function($value)use(&$walk,$mapping){if(is_array($value)){foreach($value as &$part){$part=$walk($part);}unset($part);return $value;}return is_string($value) && isset($mapping[$value])?$mapping[$value]:$value;};
        return $walk($graph);
    }
    public static function metadata() {
        if(!self::view() || class_exists('SlimSEO\\MetaTags\\Title')){return;}
        $title=self::document_title();$description=self::description();$url=self::canonical();
        echo '<meta name="description" content="'.esc_attr($description).'"><link rel="canonical" href="'.esc_url($url).'"><meta property="og:title" content="'.esc_attr($title).'"><meta property="og:description" content="'.esc_attr($description).'"><meta property="og:url" content="'.esc_url($url).'"><meta property="og:site_name" content="'.esc_attr(SND_Settings::get('app_name')).'"><meta property="og:locale" content="'.esc_attr(SND_I18n::wp_locale()).'"><meta property="og:type" content="website">';
        $graph=self::schema(array(array('@type'=>'WebSite','@id'=>self::url().'#website'),array('@type'=>'WebPage')));
        echo '<script type="application/ld+json">'.wp_json_encode(array('@context'=>'https://schema.org','@graph'=>array_values($graph)),JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_AMP).'</script>';
    }

    private static function account() {
        $u=wp_get_current_user();$progress=SND_Progress::summary($u->ID);
        echo '<section class="snd-account"><h1>Moj nalog</h1><p class="snd-account-name" data-snd-original>'.esc_html($u->display_name).'</p><p>Progres se čuva na tvom nalogu i dostupan je na drugim uređajima.</p><div class="snd-account-grid">';
        foreach(SND_Kinds::labels() as $kind=>$label){$p=$progress[$kind];
            echo '<section class="snd-progress-card"><h2>'.esc_html($label).'</h2><dl><div><dt>Upoznate teritorije</dt><dd>'.count($p['discovered']).' / '.count(SND_Data::pool($kind)).'</dd></div><div><dt>Dnevne partije</dt><dd>'.$p['daily_count'].'</dd></div>'.(SND_Settings::get('practice_enabled')?'<div><dt>Vežbanje</dt><dd>'.$p['practice_count'].'</dd></div>':'').'<div><dt>Niz igranja</dt><dd>'.esc_html(SND_Text::days($p['streak'])).'</dd></div><div><dt>Tačni odgovori u kvizu</dt><dd>'.$p['quiz_correct'].' / '.$p['quiz_total'].'</dd></div></dl><a class="snd-app-button" href="'.esc_url(self::url($kind)).'">Nastavi igru</a></section>';
        }
        echo '</div>';SND_Ranking::profile_form();echo '<form method="post" class="snd-logout" action="'.esc_url(self::url('login')).'">';wp_nonce_field('snd_auth_logout');echo '<input type="hidden" name="snd_action" value="logout"><button type="submit">Odjavi se</button></form></section>';
    }
}
