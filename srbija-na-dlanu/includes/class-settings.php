<?php
defined('ABSPATH') || exit;

final class SND_Settings {
    private static $cache;
    public static function defaults() {
        return array(
            'app_name'=>'Srbija na dlanu','default_language'=>'sr','languages'=>array('sr','en','de','ru'),
            'default_theme'=>'auto','theme_switch'=>true,'primary_color'=>'#355d3d','dark_accent'=>'#a5c797','font_scale'=>100,
            'district_enabled'=>true,'municipality_enabled'=>true,'daily_enabled'=>true,'practice_enabled'=>true,
            'max_guesses'=>6,'hint_after'=>3,'hint_enabled'=>true,'source_help_enabled'=>true,'media_fallback'=>true,
            'rounds'=>array('population','administration','flag','landmark'),'show_sources'=>true,
            'analytics_enabled'=>true,'guest_analytics'=>true,'retention_days'=>365,'erase_deleted_users'=>true,
        );
    }
    public static function all() {
        if(self::$cache===null){self::$cache=array_merge(self::defaults(),(array)get_option('snd_settings',array()));}
        return self::$cache;
    }
    public static function get($key,$fallback=null) {
        if($key==='registration_enabled'){return (bool)get_option('snd_registration_enabled',true);}
        $all=self::all();return $all[$key]??$fallback;
    }
    public static function reset(){self::$cache=null;}
    public static function foreground($color) {
        if(strlen($color)===4){$color='#'.$color[1].$color[1].$color[2].$color[2].$color[3].$color[3];}$rgb=sscanf($color,'#%02x%02x%02x');$l=0;foreach(array(.2126,.7152,.0722) as $i=>$weight){$v=($rgb[$i]??0)/255;$l+=($v<=.04045?$v/12.92:pow(($v+.055)/1.055,2.4))*$weight;}
        return $l>.179?'#101810':'#ffffff';
    }
    public static function validate($input) {
        $input=is_array($input)?$input:array();$out=self::defaults();
        $out['app_name']=wp_html_excerpt(sanitize_text_field($input['app_name']??$out['app_name']),100,'');
        if(!$out['app_name']){$out['app_name']='Srbija na dlanu';}
        $out['languages']=array_values(array_intersect(array('sr','en','de','ru'),(array)($input['languages']??array())));
        if(!$out['languages']){$out['languages']=array('sr');}
        $out['default_language']=in_array($input['default_language']??'',$out['languages'],true)?$input['default_language']:$out['languages'][0];
        $out['default_theme']=in_array($input['default_theme']??'',array('light','dark','auto'),true)?$input['default_theme']:'auto';
        foreach(array('theme_switch','district_enabled','municipality_enabled','daily_enabled','practice_enabled','hint_enabled','source_help_enabled','media_fallback','show_sources','analytics_enabled','guest_analytics','erase_deleted_users') as $key){$out[$key]=!empty($input[$key]);}
        foreach(array('primary_color','dark_accent') as $key){$out[$key]=sanitize_hex_color($input[$key]??'')?:$out[$key];}
        $out['font_scale']=max(90,min(140,(int)($input['font_scale']??100)));
        $out['max_guesses']=max(2,min(12,(int)($input['max_guesses']??6)));
        $out['hint_after']=max(1,min($out['max_guesses']-1,(int)($input['hint_after']??3)));
        $out['retention_days']=(int)($input['retention_days']??365);if($out['retention_days']!==0){$out['retention_days']=max(7,min(3650,$out['retention_days']));}
        $out['rounds']=array_values(array_intersect(array('population','administration','flag','landmark'),(array)($input['rounds']??array())));
        return $out;
    }
    public static function game_options() {
        $all=self::all();return array_intersect_key($all,array_flip(array('max_guesses','hint_after','hint_enabled','source_help_enabled','media_fallback','rounds')));
    }
    public static function options_for($challenge) {
        // Old sessions retain the rules under which they were started.
        return array_merge(array('max_guesses'=>6,'hint_after'=>3,'hint_enabled'=>true,'source_help_enabled'=>true,'media_fallback'=>true,'rounds'=>array('population','administration','flag','landmark')),$challenge['options']??array());
    }
    public static function public_config() {
        return array('app_name'=>self::get('app_name'),'languages'=>self::get('languages'),'language'=>SND_I18n::lang(),
            'district_enabled'=>self::get('district_enabled'),'municipality_enabled'=>self::get('municipality_enabled'),'daily_enabled'=>self::get('daily_enabled'),'practice_enabled'=>self::get('practice_enabled'),
            'theme'=>self::get('default_theme'),'theme_switch'=>self::get('theme_switch'),'font_scale'=>self::get('font_scale'),
            'analytics'=>self::get('analytics_enabled'),'guest_analytics'=>self::get('guest_analytics'),'show_sources'=>self::get('show_sources'));
    }
}
