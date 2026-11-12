<?php
defined('ABSPATH') || exit;

final class SND_I18n {
    private static $data;
    private static $forced;
    private static $facts;
    public static function languages(){return array('sr'=>'Srpski','en'=>'English','de'=>'Deutsch','ru'=>'Русский');}
    public static function lang() {
        if(self::$forced){return self::$forced;}
        $allowed=SND_Settings::get('languages',array('sr','en','de','ru'));
        $value=isset($_GET['lang']) && is_string($_GET['lang'])?sanitize_key(wp_unslash($_GET['lang'])):($_COOKIE['snd_lang']??'');
        if(!is_string($value) || !in_array($value,$allowed,true)){$value=SND_Settings::get('default_language','sr');}
        return in_array($value,array('sr','en','de','ru'),true)?$value:'sr';
    }
    public static function force($lang=null){self::$forced=$lang;}
    public static function html_lang(){return self::lang()==='sr'?'sr-Latn':self::lang();}
    public static function theme() {
        $default=SND_Settings::get('default_theme','auto');if(!SND_Settings::get('theme_switch')){return $default;}
        $value=isset($_GET['snd_theme']) && is_string($_GET['snd_theme'])?$_GET['snd_theme']:($_COOKIE['snd_theme']??$default);
        return is_string($value) && in_array($value,array('light','dark','auto'),true)?$value:$default;
    }
    public static function wp_locale(){return array('sr'=>'sr_RS','en'=>'en_US','de'=>'de_DE','ru'=>'ru_RU')[self::lang()];}
    public static function data() {
        if(self::$data===null){self::$data=is_file(SND_DIR.'data/i18n.php')?require SND_DIR.'data/i18n.php':array();if(is_file(SND_DIR.'data/i18n-v4.php')){self::$data=array_merge(self::$data,require SND_DIR.'data/i18n-v4.php');}if(is_file(SND_DIR.'data/i18n-v41.php')){self::$data=array_merge(self::$data,require SND_DIR.'data/i18n-v41.php');}if(is_file(SND_DIR.'data/i18n-v42.php')){self::$data=array_merge(self::$data,require SND_DIR.'data/i18n-v42.php');}}
        return self::$data;
    }
    public static function catalog($lang=null) {
        $lang=$lang?:self::lang();$overrides=(array)get_option('snd_translations',array());$out=array();
        foreach(self::data() as $key=>$values){$value=$overrides[$lang][$key]??($values[$lang]??($values['sr']??$key));if($value!==$key){$out[$key]=$value;}}
        return $out;
    }
    public static function t($source,$vars=array(),$lang=null) {
        $catalog=self::catalog($lang);$translated=$catalog[$source]??$source;
        foreach($vars as $key=>$value){$translated=str_replace('{'.$key.'}',(string)$value,$translated);}
        return $translated;
    }
    public static function plural($n,$lang=null) {
        $n=abs((int)$n);$lang=$lang?:self::lang();
        if(in_array($lang,array('sr','ru'),true)){
            if($n%10===1 && $n%100!==11){return 'one';}
            if($n%10>=2 && $n%10<=4 && !($n%100>=12 && $n%100<=14)){return 'few';}
            return 'many';
        }
        return $n===1?'one':'many';
    }
    public static function days($n){return self::t('@days_'.self::plural($n),array('count'=>(int)$n));}
    public static function text($text) {
        $text=(string)$text;$catalog=self::catalog();if(isset($catalog[$text])){return $catalog[$text];}
        if(preg_match('/^(\d+) dana?$/uD',$text,$m)){return self::days((int)$m[1]);}
        if(preg_match('/^(\d+) teritorij[ae]$/uD',$text,$m)){return self::t('@territories_'.self::plural((int)$m[1]),array('count'=>$m[1]));}
        foreach($catalog as $source=>$translated){
            if(strpos($source,'{')===false || $source[0]==='@'){continue;}
            preg_match_all('/\{([a-z_]+)\}/',$source,$names);if(!$names[1]){continue;}
            $pattern=preg_quote($source,'~');foreach($names[1] as $key){$pattern=str_replace(preg_quote('{'.$key.'}','~'),'(.+?)',$pattern);}
            if(preg_match('~^'.$pattern.'$~usD',$text,$matches)){
                $vars=array();foreach($names[1] as $i=>$key){$value=$matches[$i+1];if($key==='days' && preg_match('/^(\d+) dana?$/uD',$value,$m)){$value=self::days($m[1]);}elseif(isset($catalog[$value])){$value=$catalog[$value];}$vars[$key]=$value;}
                return self::t($source,$vars);
            }
        }
        return $text;
    }
    public static function html($html) {
        // Only trusted generated markup is processed; names, links, scripts and
        // SVG geometry are untouched. Translated text is escaped as text.
        return preg_replace_callback('~<(script|style)\b[^>]*>.*?</\1>|<([a-z][a-z0-9]*)\b[^>]*\bdata-snd-original(?:="[^"]*")?[^>]*>.*?</\2>|<[^>]*>|[^<]+~is',function($match){
            $part=$match[0];if(preg_match('~^<(script|style)\b~i',$part) || strpos($part,'data-snd-original')!==false){return $part;}
            if(isset($part[0]) && $part[0]==='<'){
                return preg_replace_callback('/\b(aria-label|title|placeholder|alt)="([^"]*)"/u',function($a){
                    $original=html_entity_decode($a[2],ENT_QUOTES|ENT_HTML5,'UTF-8');$translated=self::text($original);
                    return $translated===$original?$a[0]:$a[1].'="'.esc_attr($translated).'"';
                },$part);
            }
            $decoded=html_entity_decode($part,ENT_QUOTES|ENT_HTML5,'UTF-8');$trim=trim($decoded);if($trim===''){return $part;}
            $translated=self::text($trim);if($translated===$trim){return $part;}
            preg_match('/^(\s*).*?(\s*)$/us',$decoded,$space);return ($space[1]??'').esc_html($translated).($space[2]??'');
        },$html);
    }
    public static function url($url,$lang=null) {return add_query_arg('lang',$lang?:self::lang(),$url);}
    public static function region($name){$key='@region_'.SND_Text::key($name);$value=self::t($key);return $value===$key?$name:$value;}
    public static function place($name) {return $name==='Beograd'?self::t('@belgrade'):$name;}
    public static function territory_name($territory) {
        if(SND_Kinds::is_explore($territory['kind']??'')){return SND_Explore::display_name($territory);}
        if(($territory['kind']??'')==='district'){return self::t($territory['name']);}
        return self::place($territory['name']);
    }
    public static function landmark_name($name) {
        $facts=self::facts();
        foreach(SND_Data::get()['territories'] as $t){if(($t['landmark']['name']??'')===$name && !empty($t['landmark']['translations'][self::lang()]['name'])){return $t['landmark']['translations'][self::lang()]['name'];}}
        return $facts[$name][self::lang()]['name']??$name;
    }
    public static function landmark_quiz_name($name,$language=null) {
        $language=$language?:self::lang();
        foreach(SND_Data::get()['territories'] as $t){$l=$t['landmark']??array();if(($l['name']??'')===$name){
            if(!empty($l['translations'][$language]['quiz_name'])){return $l['translations'][$language]['quiz_name'];}
            if($language==='sr' && !empty($l['quiz_name'])){return $l['quiz_name'];}
        }}
        static $labels=null;if($labels===null){$labels=require SND_DIR.'data/landmark-labels.php';}
        if(isset($labels[$name][$language])){return $labels[$name][$language];}
        $facts=self::facts();
        foreach(SND_Data::get()['territories'] as $t){if(($t['landmark']['name']??'')===$name && !empty($t['landmark']['translations'][$language]['name'])){return $t['landmark']['translations'][$language]['name'];}}
        return $facts[$name][$language]['name']??$name;
    }
    private static function facts(){if(self::$facts===null){self::$facts=require SND_DIR.'data/landmark-translations.php';}return self::$facts;}
    public static function fact($territory) {
        $landmark=$territory['landmark']??array();$facts=self::facts();$name=$landmark['name']??'';
        if(!empty($landmark['translations'][self::lang()]['fact'])){return $landmark['translations'][self::lang()]['fact'];}
        if(($facts[$name]['sr']['fact']??null)===($landmark['fact']??'')){return $facts[$name][self::lang()]['fact']??$landmark['fact'];}
        return $landmark['fact']??'';
    }
    public static function number($n) {
        $lang=self::lang();return number_format((float)$n,0,$lang==='en'?'.':',',$lang==='en'?',':($lang==='ru'?"\u{00A0}":'.'));
    }
    public static function direction($name) {
        $key=SND_Text::key($name);return self::t('@direction_'.$key);
    }
    public static function question($q,$target) {
        if(strpos($q['type'],'explore_')===0){return SND_Explore::translate($q,$target);}
        $original=$q;$correct='';foreach($original['options'] as $option){if($option['id']===$original['correct']){$correct=$option['label'];}}
        $name=self::territory_name($target);$vars=array('name'=>$name,'seat'=>self::place($target['seat']??$target['name']),'year'=>$target['year']??2022,
            'population'=>self::number($target['population']??0),'answer'=>$correct);
        $type=$q['type'];$meta=$q['i18n_data']??array();
        if($type==='population'){
            $q['title']=self::t('Koliko približno stanovnika ima cela teritorija?');
            $q['subtitle']=self::t('Popis {year} · stanovništvo cele teritorije',$vars);
            $q['explanation']=self::t('Prema Popisu {year}, cela teritorija ima {population} stanovnika. Podatak se ne odnosi samo na naselje.',$vars);
        }elseif($type==='orientation'){
            preg_match('/„([^“]+)“/u',$original['title'],$m);$vars['anchor']=self::text($meta['anchor']??($m[1]??''));$vars['direction']=self::direction($correct);
            $q['title']=self::t('U kom smeru je centar teritorije „{anchor}“ u odnosu na ovu teritoriju?',$vars);
            $reason=$target['region']==='Kosovo i Metohija'?'Popis RZS 2022. nije sproveden na teritoriji Kosova i Metohije; zato je ovde geografsko pitanje.':'Za ovu teritoriju u bazi nije naveden uporediv podatak o stanovništvu, pa je postavljeno geografsko pitanje.';
            $q['explanation']=self::t('Od centra teritorije {name} ka centru teritorije {anchor} kreće se u smeru: {direction}.',$vars).' '.self::t($reason);
        }elseif($type==='seat'){$q['title']=self::t('Koje je sedište ovog okruga?');$q['explanation']=self::t('Sedište okruga je {seat}.',$vars);
        }elseif($type==='district'){$vars['district']=self::text($correct);$q['title']=self::t('Kojem okrugu pripada ova teritorija?');$q['explanation']=self::t('{name} pripada administrativnoj celini: {district}.',$vars);
        }elseif($type==='membership'){$vars['member']=self::place($correct);$q['title']=self::t('Koja od ovih opština pripada ovom okrugu?');$q['explanation']=self::t('Opština {member} pripada celini: {name}, prema podeli na pet upravnih okruga na Kosovu i Metohiji.',$vars);
        }elseif($type==='city_membership'){$q['title']=self::t('Kom gradu pripada ova gradska opština?');$vars['city']=self::place($target['parent_name']??'Beograd');$q['explanation']=($target['parent_id']??'')==='m-beograd'?self::t('Gradska opština {name} pripada Gradu Beogradu.',$vars):self::t('Gradska opština {name} pripada gradu {city}.',$vars);
        }elseif($type==='river'){$q['title']=self::t('Na ušću kojih reka se nalazi Beograd?');$q['explanation']=self::t('Beograd se nalazi na ušću Save u Dunav.');
        }elseif($type==='flag'){
            $vars['place']=self::place($meta['place']??($target['kind']==='district'?$target['seat']:$target['name']));
            $q['title']=self::t('Koja je zastava mesta „{place}“?',$vars);$q['correct_label']=self::t('Zastava mesta {place}',$vars);
            $q['explanation']=self::t('Prikazana je zastava mesta {place}.',$vars);
        }elseif($type==='landmark'){$q['title']=self::t('Koja je znamenitost na fotografiji?');$q['explanation']=self::fact($target);$q['correct_label']=self::landmark_name($correct);
        }elseif($type==='distance'){
            preg_match_all('/„([^“]+)“/u',$original['title'],$m);$vars['anchor']=self::text($meta['anchor']??($m[1][1]??''));
            preg_match('/približno (\d+) km/u',$original['explanation'],$distance);$vars['km']=self::number($meta['km']??($distance[1]??0));
            $q['title']=self::t('Koliko su približno udaljeni centri teritorija „{name}“ i „{anchor}“?',$vars);
            $q['explanation']=self::t('Vazdušna udaljenost između geometrijskih centara je približno {km} km. Ne meri se put automobilom ni razmak između granica.',$vars);
        }elseif($type==='nearby'){
            preg_match('/približno (\d+) km/u',$original['explanation'],$distance);$vars['km']=self::number($meta['km']??($distance[1]??0));$vars['nearby']=self::text($correct);
            $q['title']=self::t('Koja od ovih teritorija ima centar najbliži centru tražene teritorije?');
            $q['explanation']=self::t('Od ponuđenih odgovora, najbliža je teritorija {nearby}: približno {km} km vazdušnom linijom između geometrijskih centara.',$vars);
        }elseif($type==='region'){$vars['region']=self::region($target['region']);$q['title']=self::t('U kom širem delu Srbije se nalazi ova teritorija?');$q['explanation']=self::t('Širi položaj teritorije: {region}.',$vars);}
        foreach($q['options'] as &$option){
            if($type==='population' || $type==='distance'){
                if(preg_match('/^Oko ([\d.]+)( km)?$/uD',$option['label'],$m)){$option['label']=self::t('Oko {value}',array('value'=>self::number((int)str_replace('.','',$m[1])))).(!empty($m[2])?' km':'');}
            }elseif($type==='orientation'){$option['label']=self::direction($option['label']);}
            elseif($type==='landmark'){$option['label']=self::landmark_quiz_name($option['label']);}
            elseif($type==='region'){$option['label']=self::region($option['label']);}
            else{$option['label']=self::text(self::place($option['label']));}
        }
        unset($option);
        if(isset($q['subtitle'])){$q['subtitle']=self::text($q['subtitle']);}
        foreach(array('sources','credits') as $key){if(!empty($q[$key])){foreach($q[$key] as &$item){if(isset($item['label'])){$item['label']=self::text($item['label']);}}unset($item);}}
        unset($q['i18n_data']);return $q;
    }
    public static function prepare() {
        if(!SND_Site::view()){return;}
        if(!defined('DONOTCACHEPAGE')){define('DONOTCACHEPAGE',true);}nocache_headers();
        if(isset($_GET['lang']) && is_string($_GET['lang']) && in_array($_GET['lang'],SND_Settings::get('languages'),true)){
            setcookie('snd_lang',self::lang(),array('expires'=>time()+YEAR_IN_SECONDS,'path'=>wp_parse_url(home_url('/'),PHP_URL_PATH)?:'/','secure'=>is_ssl(),'samesite'=>'Lax'));
        }
        if(isset($_GET['snd_theme']) && is_string($_GET['snd_theme']) && in_array($_GET['snd_theme'],array('light','dark','auto'),true) && SND_Settings::get('theme_switch')){
            setcookie('snd_theme',$_GET['snd_theme'],array('expires'=>time()+YEAR_IN_SECONDS,'path'=>wp_parse_url(home_url('/'),PHP_URL_PATH)?:'/','secure'=>is_ssl(),'samesite'=>'Lax'));
        }
        header('Content-Language: '.self::html_lang());
    }
    public static function enqueue() {
        wp_enqueue_script('snd-i18n',SND_URL.'assets/i18n.js',array(),SND_VERSION,true);
        wp_enqueue_style('snd-motion',SND_URL.'assets/motion.css',array(),SND_VERSION);
        static $done=false;if($done){return;}$done=true;
        $config=SND_Settings::public_config();$config['loader']=SND_Site::loader();$config['dictionary']=self::catalog();$config['api']=rest_url('srbija-na-dlanu/v1/');$config['ajax']=admin_url('admin-ajax.php');$config['view']=SND_Site::view();$config['cookie_path']=wp_parse_url(home_url('/'),PHP_URL_PATH)?:'/';
        wp_add_inline_script('snd-i18n','window.SND_CONFIG='.wp_json_encode($config,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT).';','before');
    }
    public static function head() {
        if(!SND_Site::view()){return;}
        $theme=self::theme();$switch=SND_Settings::get('theme_switch');
        $script='(function(){function settle(e){var v=e.viewTransition;if(v){v.ready.catch(function(){});v.finished.catch(function(){});}}window.addEventListener("pageswap",settle);window.addEventListener("pagereveal",settle);var p='.wp_json_encode($theme).';';
        if($switch && !isset($_GET['snd_theme'])){$script.='var saved;try{saved=localStorage.getItem("snd:theme");}catch(e){}p=saved||((document.cookie.match(/(?:^|; )snd_theme=(light|dark|auto)(?:;|$)/)||[])[1])||p;';}
        $script.='if(["light","dark","auto"].indexOf(p)<0)p="auto";document.documentElement.dataset.sndTheme=p==="auto"?(window.matchMedia&&matchMedia("(prefers-color-scheme: dark)").matches?"dark":"light"):p;document.documentElement.dataset.sndPreference=p;})();';
        echo '<script data-noptimize="1">'.$script.'</script>';
        $colors=array('primary_color'=>'brand','highlight_color'=>'highlight','ink_color'=>'deep','surface_color'=>'white','pale_color'=>'pale','border_color'=>'edge','dark_surface_color'=>'panel','dark_accent'=>'dark-accent','dark_button_color'=>'dark-button');
        echo '<style id="snd-custom-colors">@view-transition{navigation:auto}:root{';
        foreach($colors as $key=>$variable){echo '--snd-'.$variable.':'.esc_html(SND_Settings::get($key)).';';}
        echo '--snd-on-accent:'.SND_Settings::foreground(SND_Settings::get('primary_color')).';--snd-on-dark-button:'.SND_Settings::foreground(SND_Settings::get('dark_button_color')).';--snd-font-scale:'.((int)SND_Settings::get('font_scale')/100).';}</style>';
        foreach(SND_Settings::get('languages') as $lang){echo '<link rel="alternate" hreflang="'.esc_attr($lang==='sr'?'sr-Latn':$lang).'" href="'.esc_url(self::url(SND_Site::canonical(),$lang)).'">';}
    }
    public static function rest($response,$server,$request) {
        if(strpos($request->get_route(),'/srbija-na-dlanu/v1/')!==0){return $response;}
        if($response instanceof WP_REST_Response){$data=$response->get_data();if($response->get_status()>=400 && isset($data['message'])){$data['message']=self::text($data['message']);$response->set_data($data);}$response->header('Content-Language',self::html_lang());}
        return $response;
    }
}
