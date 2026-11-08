<?php
defined('ABSPATH') || exit;

final class SND_Kinds {
    const ALL = array('district','municipality','city','river','mountain');
    const EXPLORE = array('city','river','mountain');
    public static function labels(){return array('district'=>'Okruzi','municipality'=>'Opštine i gradovi','city'=>'Gradovi i naselja','river'=>'Reke','mountain'=>'Planine');}
    public static function is_explore($kind){return in_array($kind,self::EXPLORE,true);}
    public static function counts(){ $out=array();foreach(self::ALL as $kind){$out[$kind]=count(SND_Data::pool($kind));}return $out; }
    public static function available(){return array_filter(self::labels(),function($label,$kind){return SND_Settings::get($kind.'_enabled');},ARRAY_FILTER_USE_BOTH);}
}
