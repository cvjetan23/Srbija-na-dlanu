<?php
defined('ABSPATH') || exit;

/** Only the requested pieces of the answer leave the server. */
final class SND_Hints {
    public static function level($s){return min(2,max(0,(int)($s['hint_level']??(!empty($s['hint'])?1:0))));}
    public static function describe($s){
        $level=self::level($s);if(!$level){return null;}
        $name=SND_I18n::territory_name($s['challenge']['target']);$name=trim(preg_replace('/\s*\([^)]*\)/u','',$name));
        preg_match_all('/\X/u',$name,$matches);$chars=$matches[0];$letters=array();
        for($i=0;$i<count($chars);$i++){$char=$chars[$i];$pair=$char.($chars[$i+1]??'');if(SND_I18n::lang()==='sr' && preg_match('/^(?:[Ll][Jj]|[Nn][Jj]|[Dd][Žž])$/uD',$pair)){$char=$pair;$i++;}$letters[]=$char;}
        $first='';$count=0;$pattern=array();foreach($letters as $char){$is_letter=(bool)preg_match('/[\p{L}\p{N}]/u',$char);if($is_letter){$count++;if($first===''){$first=$char;}$pattern[]=array('letter'=>true,'value'=>$count===1?$char:'');}else{$pattern[]=array('letter'=>false,'value'=>$char);}}
        $out=array('level'=>$level,'first'=>$first);if($level===2){$out['count']=$count;$out['pattern']=$pattern;}return $out;
    }
}
