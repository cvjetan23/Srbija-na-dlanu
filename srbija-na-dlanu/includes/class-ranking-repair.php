<?php
defined('ABSPATH') || exit;

/** Recover scores only from the application's own completed, scored history. */
final class SND_Ranking_Repair {
    public static function state($row){
        if((int)$row['user_id']<1 || !get_userdata((int)$row['user_id']) || $row['mode']!=='daily' || $row['stage']!=='complete' || !empty($row['legacy']) || !in_array($row['kind'],SND_Kinds::ALL,true) || !preg_match('/^[a-f0-9]{32,64}$/D',$row['game_id']) || $row['played_on']>SND_Game::date()){return null;}
        $d=json_decode($row['details'],true);$p=$d['points']??null;$rules=$p['rules']??null;
        if(!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/D',$row['played_on'],$date)||!checkdate((int)$date[2],(int)$date[3],(int)$date[1])||!is_array($d)||!is_array($d['guesses']??null)||!is_array($d['questions']??null)||!in_array($row['outcome'],array('solved','missed','revealed'),true)){return null;}
        if(!is_array($rules)||(int)($rules['version']??0)!==2){return null;}foreach(array('territory','step','hint') as $key){if(!isset($rules[$key])||!is_numeric($rules[$key])||(int)$rules[$key]<0||(int)$rules[$key]>100){return null;}}
        if(!is_array($p)||!is_array($rules)||(int)($rules['season']??0)!==4||empty($rules['rank_enabled'])||!empty($d['legacy'])||!empty($d['summary_only'])||!isset($d['guesses'],$d['questions'])||count($d['guesses'])!==(int)$row['guesses']||count($d['questions'])!==(int)$row['question_count']){return null;}
        $answers=array();$help=array();foreach($d['questions'] as $i=>$q){$answers[$i]=array('correct'=>!empty($q['correct']),'skipped'=>!empty($q['skipped']));if(!empty($q['source_help'])){$help[$i]=true;}}
        $correct=count(array_filter($answers,function($a){return $a['correct'];}));if($correct!==(int)$row['score']){return null;}
        $completed=strtotime(($row['completed_at']??'').' UTC');if(!$completed){return null;}
        $s=array('owner'=>(int)$row['user_id'],'game_id'=>$row['game_id'],'stage'=>'complete','solved'=>$row['outcome']==='solved','guesses'=>$d['guesses'],'answers'=>$answers,'hint'=>!empty($p['penalty']),'source_help_questions'=>$help,'scoring'=>$rules,'completed_at'=>$completed,'challenge'=>array('kind'=>$row['kind'],'mode'=>'daily','date'=>$row['played_on'],'questions'=>array_fill(0,(int)$row['question_count'],array())));
        $calculated=SND_Scoring::calculate($s);foreach(array('total','territory','quiz','penalty','correct','questions') as $key){if(!isset($p[$key]) || (int)$p[$key]!==$calculated[$key]){return null;}}
        return $s;
    }
    public static function restore($input=array()){
        if(!SND_Ranking::ready()||!SND_Analytics::ready()){return new WP_Error('snd_rank_not_ready','Istorija ili rang lista trenutno nisu dostupne.',array('status'=>503));}
        global $wpdb;$f=SND_Analytics::filters(array_merge($input,array('mode'=>'daily','stage'=>'complete')));$games=SND_Analytics::games_table();$rank=SND_Ranking::table();$where=SND_Analytics::where($f);
        $rows=$wpdb->get_results("SELECT * FROM $games WHERE $where AND user_id>0 AND legacy=0 AND game_id NOT IN (SELECT game_id FROM $rank WHERE points IS NOT NULL) ORDER BY completed_at,id LIMIT 500",ARRAY_A);
        if($wpdb->last_error){return new WP_Error('snd_rank_history','Istorija nije mogla da se pročita.',array('status'=>503));}
        $out=array('scanned'=>count($rows),'restored'=>0,'points'=>0,'skipped'=>0,'errors'=>0);
        foreach($rows as $row){$s=self::state($row);if(!$s){$out['skipped']++;continue;}$s['rank_eligible']=SND_Ranking::reserve($s);if(!$s['rank_eligible']){$out[$wpdb->last_error?'errors':'skipped']++;continue;}$r=SND_Ranking::complete($s);if(is_wp_error($r)){$out['errors']++;continue;}
            $d=json_decode($row['details'],true);$d['rank_eligible']=true;$d['ranking_recovered_at']=gmdate('c');$d['activity'][]=array('at'=>gmdate('c'),'action'=>'ranking_recovered','stage'=>'complete');$wpdb->update($games,array('details'=>wp_json_encode($d,JSON_UNESCAPED_UNICODE)),array('id'=>$row['id']));$out['restored']++;$out['points']+=SND_Scoring::calculate($s)['total'];
        }
        return $out;
    }
    public static function form(){
        echo '<section class="snd-ranking-repair"><h2>Obnova nedostajućih rezultata</h2><p>Obnova koristi završene dnevne partije i provereno bodovanje iz serverske istorije. Čuva postojeće upise i podešavanja privatnosti.</p>';
        if(isset($_GET['recovered'])){echo '<p class="notice notice-success">Obnovljeno rezultata: '.(int)$_GET['recovered'].'.</p>';}
        echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'">';wp_nonce_field('snd_ranking_repair');echo '<input type="hidden" name="action" value="snd_ranking_repair"><label>ID igrača <input type="number" name="user_id" min="0" value="0"></label> <label>Od <input type="date" name="from" value="'.esc_attr((new DateTimeImmutable(SND_Game::date()))->modify('-7 days')->format('Y-m-d')).'"></label> <label>Do <input type="date" name="to" value="'.esc_attr(SND_Game::date()).'"></label> <button class="button">Obnovi nedostajuće rezultate</button></form></section>';
    }
    public static function handle(){if(!current_user_can('manage_options')){wp_die('Nedovoljne dozvole.','',array('response'=>403));}check_admin_referer('snd_ranking_repair');$r=self::restore(wp_unslash($_POST));if(is_wp_error($r)){wp_die(esc_html($r->get_error_message()),'',array('response'=>503));}wp_safe_redirect(add_query_arg(array('page'=>'snd-ranking','recovered'=>$r['restored']),admin_url('admin.php')));exit;}
}
