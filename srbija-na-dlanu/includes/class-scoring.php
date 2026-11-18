<?php
defined('ABSPATH') || exit;

final class SND_Scoring {
    public static function rules() {
        $hint=(int)SND_Settings::get('points_hint');
        return array('version'=>3,'season'=>4,'territory'=>(int)SND_Settings::get('points_territory'),'step'=>(int)SND_Settings::get('points_step'),'hint'=>$hint,'hint_costs'=>self::hint_costs($hint),'rank_enabled'=>(bool)SND_Settings::get('ranking_enabled'));
    }
    public static function hint_costs($total){$total=max(0,min(100,(int)$total));return array((int)ceil($total/2),(int)floor($total/2));}
    public static function current_hint_policy($rules){
        if((int)($rules['version']??0)===2){$rules['version']=3;$rules['hint_costs']=self::hint_costs($rules['hint']);}
        return $rules;
    }
    public static function hint_prices($s){
        $r=$s['scoring']??array();if(!$r){return array(0,0);}
        return (int)($r['version']??1)>=3?self::hint_costs($r['hint']):array((int)$r['hint'],0);
    }
    public static function hint_spent($s){return !empty($s['hint'])?array_sum(array_slice(self::hint_prices($s),0,SND_Hints::level($s))):0;}
    public static function for_challenge($c) {
        if(isset($c['scoring'])){return $c['scoring'];}
        if($c['mode']!=='daily'){return self::rules();}
        // A daily challenge prepared before the update gets one shared policy.
        $key='snd_scoring_day_'.str_replace('-','',$c['date']).'_'.$c['kind'];
        add_option($key,self::rules(),'',false);return get_option($key);
    }
    public static function calculate($s) {
        if(empty($s['scoring'])){return null;}
        $r=$s['scoring'];$questions=count($s['challenge']['questions']);
        $base=$questions?(int)$r['territory']:100;
        $territory=!empty($s['solved'])?max(0,$base-max(0,count($s['guesses'])-1)*(int)$r['step']):0;
        $correct=0;foreach($s['answers'] as $i=>$a){if(!empty($a['correct']) && empty($a['skipped']) && empty($s['source_help_questions'][$i])){$correct++;}}
        $quiz=$questions?(int)floor((100-$base)*min($questions,$correct)/$questions):0;
        $penalty=(int)($r['version']??1)>=3?min($territory+$quiz,self::hint_spent($s)):(!empty($s['hint'])?((int)($r['version']??1)>=2?min($territory,(int)$r['hint']):(int)$r['hint']):0);
        return array('total'=>max(0,min(100,$territory+$quiz-$penalty)),'territory'=>$territory,'quiz'=>$quiz,'penalty'=>$penalty,'correct'=>$correct,'questions'=>$questions,'rules'=>$r);
    }
}
