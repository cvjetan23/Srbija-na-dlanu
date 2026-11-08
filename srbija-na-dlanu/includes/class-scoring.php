<?php
defined('ABSPATH') || exit;

final class SND_Scoring {
    public static function rules() {
        return array('version'=>2,'season'=>4,'territory'=>(int)SND_Settings::get('points_territory'),'step'=>(int)SND_Settings::get('points_step'),'hint'=>(int)SND_Settings::get('points_hint'),'rank_enabled'=>(bool)SND_Settings::get('ranking_enabled'));
    }
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
        $penalty=!empty($s['hint'])?((int)($r['version']??1)>=2?min($territory,(int)$r['hint']):(int)$r['hint']):0;
        return array('total'=>max(0,min(100,$territory+$quiz-$penalty)),'territory'=>$territory,'quiz'=>$quiz,'penalty'=>$penalty,'correct'=>$correct,'questions'=>$questions,'rules'=>$r);
    }
}
