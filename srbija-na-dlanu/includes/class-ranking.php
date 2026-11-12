<?php
defined('ABSPATH') || exit;

final class SND_Ranking {
    const DB_VERSION=2;
    public static function table(){global $wpdb;return $wpdb->prefix.'snd_ranking';}
    public static function ready(){return (int)get_option('snd_ranking_db')===self::DB_VERSION;}
    public static function install() {
        if(self::ready()){return;}global $wpdb;require_once ABSPATH.'wp-admin/includes/upgrade.php';$t=self::table();$collate=$wpdb->get_charset_collate();
        dbDelta("CREATE TABLE $t (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            slot_key varchar(64) NOT NULL,
            game_id varchar(64) NOT NULL,
            user_id bigint(20) unsigned NOT NULL,
            kind varchar(20) NOT NULL,
            mode varchar(20) NOT NULL,
            season smallint(5) unsigned NOT NULL DEFAULT 0,
            played_on date NOT NULL,
            points smallint(5) unsigned NULL,
            territory_points smallint(5) unsigned NOT NULL DEFAULT 0,
            quiz_points smallint(5) unsigned NOT NULL DEFAULT 0,
            penalty smallint(5) unsigned NOT NULL DEFAULT 0,
            hidden tinyint(1) unsigned NOT NULL DEFAULT 0,
            completed_at datetime NULL,
            policy longtext NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY slot_key (slot_key),
            UNIQUE KEY game_id (game_id),
            KEY board_date (played_on,mode,kind),
            KEY season_date (season,played_on,kind),
            KEY user_date (user_id,played_on)
        ) $collate;");
        if($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s',$wpdb->esc_like($t)))!==$t){return;}
        update_option('snd_ranking_db',self::DB_VERSION,false);add_option('snd_ranking_season_4',SND_Game::date(),'',false);add_option('snd_ranking_started',gmdate('Y-m-d H:i:s'),'',false);
    }
    public static function alias($id) {
        $p=get_user_meta($id,'snd_rank_profile',true);$alias=is_array($p)?self::clean_alias($p['alias']??''):'';
        return $alias?:SND_I18n::t('Igrač {code}',array('code'=>strtoupper(substr(hash_hmac('sha256','rank:'.$id,wp_salt('auth')),0,6))));
    }
    public static function clean_alias($value) {
        if(!is_string($value)){return '';}$value=trim(sanitize_text_field($value));
        return strpos($value,'@')===false?wp_html_excerpt($value,32,''):'';
    }
    public static function reserve($s) {
        if(!self::ready() || !SND_Settings::get('ranking_enabled') || empty($s['owner']) || empty($s['scoring']['rank_enabled'])){return false;}
        if(SND_Settings::get('daily_only') && $s['challenge']['mode']!=='daily'){return false;}
        global $wpdb;$c=$s['challenge'];$user=(int)$s['owner'];$p=(array)get_user_meta($user,'snd_rank_profile',true);
        $slot=$c['mode']==='daily'?'daily:'.($s['scoring']['season']??0).':'.$user.':'.$c['kind'].':'.$c['date']:'game:'.$s['game_id'];
        $row=array('slot_key'=>hash('sha256',$slot),'game_id'=>$s['game_id'],'user_id'=>$user,'kind'=>$c['kind'],'mode'=>$c['mode'],'season'=>(int)($s['scoring']['season']??0),'played_on'=>$c['date'],'hidden'=>!empty($p['hidden'])?1:0,'policy'=>wp_json_encode($s['scoring']));
        $old=$wpdb->get_row($wpdb->prepare('SELECT game_id FROM '.self::table().' WHERE slot_key=%s',$row['slot_key']),ARRAY_A);
        if($old){return $old['game_id']===$s['game_id'];}
        return $wpdb->insert(self::table(),$row)!==false;
    }
    public static function complete($s) {
        if($s['stage']!=='complete' || empty($s['owner']) || empty($s['rank_eligible']) || !self::ready()){return true;}
        global $wpdb;$points=SND_Scoring::calculate($s);if(!$points){return true;}
        $date=$s['challenge']['mode']==='daily'?$s['challenge']['date']:SND_Game::date();
        $r=$wpdb->query($wpdb->prepare('UPDATE '.self::table().' SET points=%d,territory_points=%d,quiz_points=%d,penalty=%d,completed_at=%s,played_on=%s WHERE game_id=%s AND user_id=%d AND points IS NULL',
            $points['total'],$points['territory'],$points['quiz'],$points['penalty'],gmdate('Y-m-d H:i:s',$s['completed_at']??time()),$date,$s['game_id'],(int)$s['owner']));
        if($r!==false){$saved=$wpdb->get_var($wpdb->prepare('SELECT points FROM '.self::table().' WHERE game_id=%s AND user_id=%d',$s['game_id'],(int)$s['owner']));if($saved!==null){return true;}}return new WP_Error('snd_rank_save','Bodovi još nisu sačuvani. Pokušaj ponovo.',array('status'=>503));
    }
    public static function filters($input=null) {
        $input=$input===null?wp_unslash($_GET):$input;$today=SND_Game::date();$out=array();
        $out['board']=($input['board']??'')==='general'?'general':'daily';
        $out['period']=in_array($input['period']??'',array('day','month','all'),true)?$input['period']:'day';
        $out['game']=in_array($input['game']??'',SND_Kinds::ALL,true)?$input['game']:'';
        $date=$input['rank_date']??$today;$valid=is_string($date)&&preg_match('/^(\d{4})-(\d{2})-(\d{2})$/D',$date,$m)&&checkdate((int)$m[2],(int)$m[3],(int)$m[1]);
        $out['rank_date']=$valid && $date<=$today?$date:$today;
        $month=$input['rank_month']??substr($today,0,7);$valid=is_string($month)&&preg_match('/^(\d{4})-(\d{2})$/D',$month,$m)&&checkdate((int)$m[2],1,(int)$m[1]);
        $out['rank_month']=$valid && $month<=substr($today,0,7)?$month:substr($today,0,7);
        $out['rank_page']=is_scalar($input['rank_page']??1)?max(1,min(100000,(int)($input['rank_page']??1))):1;return $out;
    }
    public static function results($filters,$private=false) {
        $empty=array('rows'=>array(),'players'=>0,'page'=>1,'pages'=>1);if(!self::ready()){return $empty;}
        global $wpdb;$f=self::filters($filters);$t=self::table();$where='r.points IS NOT NULL';$args=array();
        if(!$private){$where.=' AND r.hidden=0';}
        if(SND_Settings::get('daily_only') && !$private){$where.=" AND r.mode='daily' AND r.season=4";}elseif($f['board']==='daily'){$where.=" AND r.mode='daily'";}
        if($f['game']){$where.=' AND r.kind=%s';$args[]=$f['game'];}
        if($f['period']==='day'){$where.=' AND r.played_on=%s';$args[]=$f['rank_date'];}
        elseif($f['period']==='month'){$where.=' AND r.played_on>=%s AND r.played_on<=%s';$args[]=$f['rank_month'].'-01';$args[]=(new DateTimeImmutable($f['rank_month'].'-01'))->format('Y-m-t');}
        if($args){$where=$wpdb->prepare($where,$args);}
        // Join users to exclude deleted accounts even if a cleanup hook was interrupted.
        $base="SELECT r.user_id,SUM(r.points) total,COUNT(*) games,SUM(r.mode='daily') daily_games,AVG(r.points) average FROM $t r INNER JOIN {$wpdb->users} u ON u.ID=r.user_id WHERE $where GROUP BY r.user_id";
        $players=(int)$wpdb->get_var("SELECT COUNT(*) FROM ($base) snd_players");$size=25;$pages=max(1,(int)ceil($players/$size));$page=min($f['rank_page'],$pages);$offset=($page-1)*$size;
        $rows=$wpdb->get_results($wpdb->prepare("$base ORDER BY total DESC,r.user_id ASC LIMIT %d OFFSET %d",$size,$offset),ARRAY_A);
        $last=null;$position=0;foreach($rows as $i=>&$row){
            if($i===0){$position=1+(int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM ($base) snd_higher WHERE total>%d",$row['total']));}
            elseif((int)$row['total']!==$last){$position=$offset+$i+1;}
            $row['rank']=$position;$last=(int)$row['total'];$row['alias']=self::alias($row['user_id']);
        }unset($row);
        return array('rows'=>$rows,'players'=>$players,'page'=>$page,'pages'=>$pages);
    }
    private static function link($f,$change){return add_query_arg(array_merge($f,$change,array('rank_page'=>1)),SND_Site::url('ranking'));}
    public static function page() {
        echo '<section class="snd-ranking"><header class="snd-ranking-head"><span class="snd-ranking-emblem" aria-hidden="true">'.self::trophy().'</span><h1>Rang lista</h1><p>Svaka partija donosi od 0 do 100 poena.</p></header>';
        if(!SND_Settings::get('ranking_enabled')){echo '<p class="snd-rank-empty">Rang lista trenutno nije dostupna.</p></section>';return;}
        $f=self::filters();if(SND_Settings::get('daily_only')){$f['board']='daily';}$result=self::results($f);
        echo '<div class="snd-ranking-dashboard"><aside class="snd-ranking-filters" aria-label="Filteri rang liste"><h2>Filteri</h2>';
        if(!SND_Settings::get('daily_only')){
        echo '<nav class="snd-ranking-boards" aria-label="Vrsta rang liste">';foreach(array('daily'=>'Dnevni izazovi','general'=>'Opšta rang lista') as $key=>$name){echo '<a href="'.esc_url(self::link($f,array('board'=>$key))).'"'.($f['board']===$key?' aria-current="true"':'').'>'.esc_html($name).'</a>';}echo '</nav><p class="snd-rank-description">'.($f['board']==='daily'?'Samo dnevni izazovi: najviše jedan rezultat po igri i danu.':'Dnevni izazovi i sva vežbanja. Više završenih partija donosi veći zbir.').'</p>';
        }else{echo '<p class="snd-rank-description">Jedan rezultat po igri i danu. Ukupno do 500 poena dnevno za pet igara. Vežbanje ne ulazi u plasman.</p><p class="snd-rank-note">Nova sezona dnevnih igara. Raniji rezultati ostaju u istoriji naloga.</p>';}
        echo '<h3>Period</h3><nav class="snd-ranking-periods" aria-label="Period rang liste">';foreach(array('day'=>'Dnevna','month'=>'Mesečna','all'=>'Večna') as $key=>$name){echo '<a href="'.esc_url(self::link($f,array('period'=>$key))).'"'.($f['period']===$key?' aria-current="true"':'').'>'.esc_html($name).'</a>';}echo '</nav>';
        echo '<h3>Izaberi igru</h3><nav class="snd-ranking-games" aria-label="Igra na rang listi">';foreach(array(''=>'Sve igre')+SND_Kinds::labels() as $key=>$name){echo '<a href="'.esc_url(self::link($f,array('game'=>$key))).'"'.($f['game']===$key?' aria-current="true"':'').'>'.esc_html($name).'</a>';}echo '</nav>';
        if($f['period']!=='all'){echo '<form class="snd-rank-date" method="get" action="'.esc_url(SND_Site::url('ranking')).'">';$route=array();parse_str(wp_parse_url(SND_Site::url('ranking'),PHP_URL_QUERY)?:'',$route);foreach(array('page_id','pagename') as $key){if(isset($route[$key]) && is_scalar($route[$key])){echo '<input type="hidden" name="'.$key.'" value="'.esc_attr($route[$key]).'">';}}foreach(array('lang'=>SND_I18n::lang(),'board'=>$f['board'],'period'=>$f['period'],'game'=>$f['game']) as $key=>$value){echo '<input type="hidden" name="'.$key.'" value="'.esc_attr($value).'">';}$month=$f['period']==='month';echo '<label for="snd-rank-date">'.($month?'Mesec':'Datum').'</label><input id="snd-rank-date" type="'.($month?'month':'date').'" name="'.($month?'rank_month':'rank_date').'" value="'.esc_attr($month?$f['rank_month']:$f['rank_date']).'" max="'.esc_attr($month?substr(SND_Game::date(),0,7):SND_Game::date()).'"><button class="snd-app-button snd-button-outline" type="submit">Prikaži</button></form>';}
        echo '</aside><div class="snd-ranking-results" aria-label="Rezultati rang liste"><h2>Rang lista igrača</h2>';
        echo '<p class="snd-rank-summary">'.esc_html(SND_I18n::t('Igrača na listi: {count}',array('count'=>$result['players']))).'</p><div class="snd-ranking-scroll"><table class="snd-ranking-table"><caption class="snd-app-sr-only">Rang lista igrača</caption><thead><tr><th scope="col">Mesto</th><th scope="col">Igrač</th><th scope="col">Poeni ukupno</th><th scope="col">Partije</th><th scope="col">Prosek / 100</th></tr></thead><tbody>';
        foreach($result['rows'] as $row){echo '<tr'.((int)$row['user_id']===get_current_user_id()?' class="is-me"':'').'><td><span class="snd-rank-position'.($row['rank']<=3?' is-podium':'').'">'.(int)$row['rank'].'</span></td><th scope="row"><span data-snd-original>'.esc_html($row['alias']).'</span><span class="snd-rank-mobile-meta">'.esc_html(SND_I18n::t('Partije: {games} · Prosek: {average}',array('games'=>(int)$row['games'],'average'=>number_format((float)$row['average'],1,SND_I18n::lang()==='en'?'.':',','')))).'</span>'.((int)$row['user_id']===get_current_user_id()?'<small>Ti</small>':'').'</th><td class="snd-rank-points">'.esc_html(SND_I18n::number($row['total'])).'</td><td>'.(int)$row['games'].'</td><td>'.esc_html(number_format_i18n((float)$row['average'],1)).'</td></tr>';}
        if(!$result['rows']){echo '<tr><td colspan="5" class="snd-rank-empty">Još nema rezultata za ovaj izbor. Odigraj i postavi prvi rezultat.</td></tr>';}
        echo '</tbody></table></div><nav class="snd-rank-pagination" aria-label="Stranice rang liste">';foreach(array(-1=>'Prethodna',1=>'Sledeća') as $delta=>$label){$page=$result['page']+$delta;if($page>0 && $page<=$result['pages']){echo '<a href="'.esc_url(add_query_arg(array_merge($f,array('rank_page'=>$page)),SND_Site::url('ranking'))).'">'.esc_html($label).'</a>';}}echo '</nav><p class="snd-rank-note">Isti zbir poena znači deljeno mesto. Datum se računa po vremenu u Srbiji.</p>';
        $started=get_option('snd_ranking_started');if($started){$started=wp_date('Y-m-d',strtotime($started.' UTC'),new DateTimeZone('Europe/Belgrade'));echo '<p class="snd-rank-note">'.esc_html(SND_I18n::t('Bodovanje se beleži od {date}. Raniji rezultati ostaju u istoriji.',array('date'=>substr($started,0,10)))).'</p>';}
        echo '<a class="snd-app-button snd-button-outline" href="'.esc_url(SND_Site::url(is_user_logged_in()?'account':'login')).'">'.(is_user_logged_in()?'Moj nadimak i privatnost':'Prijavi se za rang listu').'</a>';self::rules();echo '</div></div></section>';
    }
    public static function rules() {
        $r=SND_Scoring::rules();echo '<details class="snd-scoring-rules"><summary>Kako se dobijaju poeni?</summary><ul><li>'.esc_html(SND_I18n::t('Teritorija: do {base} poena. Svaki naredni pokušaj umanjuje taj deo za {step}.',array('base'=>$r['territory'],'step'=>$r['step']))).'</li><li>'.esc_html(SND_I18n::t('Dodatna pitanja: do {quiz} poena, ravnomerno po tačnom odgovoru.',array('quiz'=>100-$r['territory']))).'</li><li>'.esc_html(SND_I18n::t('Pomoći za naziv ukupno oduzimaju {hint} poena. Pitanje uz pomoć izvora ne donosi poene.',array('hint'=>$r['hint']))).'</li><li>Otkrivena ili nepogođena teritorija donosi 0 poena za taj deo. Preskočeno pitanje donosi 0.</li><li>Bez dodatnih pitanja teritorija vredi do 100 poena. Ukupan rezultat je uvek između 0 i 100.</li><li>Prijavom poveži dnevnu partiju sa nalogom i sačuvaj plasman. Poeni se računaju na serveru i isti rezultat se ne upisuje dva puta.</li><li>Promene bodovanja važe za nove partije i buduće dnevne izazove.</li></ul></details>';
    }
    public static function trophy(){return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 3h10v5a5 5 0 0 1-10 0V3ZM7 5H3v2a4 4 0 0 0 5 4m9-6h4v2a4 4 0 0 1-5 4M12 13v7m-4 1h8"/></svg>';}
    public static function profile_form() {
        $id=get_current_user_id();$p=(array)get_user_meta($id,'snd_rank_profile',true);
        echo '<section class="snd-rank-profile"><h2>Rang lista i javni nadimak</h2><p>Na listi se prikazuje samo nadimak. E-pošta i korisničko ime nisu javni.</p>';
        if(($_GET['rank_saved']??'')==='1'){echo '<p role="status">Podešavanja rang liste su sačuvana.</p>';}
        echo '<form method="post" action="'.esc_url(SND_Site::url('account')).'">';wp_nonce_field('snd_rank_profile');echo '<input type="hidden" name="snd_rank_action" value="profile"><label for="snd-rank-alias">Javni nadimak</label><input id="snd-rank-alias" name="rank_alias" maxlength="32" value="'.esc_attr(self::alias($id)).'" required><label class="snd-rank-hide"><input type="checkbox" name="rank_hidden" value="1" '.checked(!empty($p['hidden']),true,false).'> Sakrij moje rezultate sa javne rang liste</label><button type="submit" class="snd-app-button snd-button-outline">Sačuvaj</button></form><a href="'.esc_url(SND_Site::url('ranking')).'">Otvori rang listu</a></section>';
    }
    public static function handle_profile() {
        if(SND_Site::view()!=='account' || !is_user_logged_in() || ($_SERVER['REQUEST_METHOD']??'GET')!=='POST' || ($_POST['snd_rank_action']??'')!=='profile'){return;}
        if(!wp_verify_nonce(is_string($_POST['_wpnonce']??null)?wp_unslash($_POST['_wpnonce']):'','snd_rank_profile')){wp_die('Forma je istekla.','',array('response'=>403));}
        $id=get_current_user_id();$alias=self::clean_alias(wp_unslash($_POST['rank_alias']??''));$hidden=!empty($_POST['rank_hidden']);
        update_user_meta($id,'snd_rank_profile',array('alias'=>$alias,'hidden'=>$hidden));
        if(self::ready()){global $wpdb;$wpdb->update(self::table(),array('hidden'=>$hidden?1:0),array('user_id'=>$id));}
        wp_safe_redirect(add_query_arg('rank_saved',1,SND_Site::url('account')));exit;
    }
    public static function delete_user($id){if(self::ready()){global $wpdb;$wpdb->delete(self::table(),array('user_id'=>(int)$id));}delete_user_meta($id,'snd_rank_profile');}
    public static function clean() {
        if(!self::ready()){return;}global $wpdb;$cutoff=(new DateTimeImmutable(SND_Game::date()))->modify('-31 days')->format('Y-m-d');
        $wpdb->query($wpdb->prepare('DELETE FROM '.self::table().' WHERE points IS NULL AND played_on<%s',$cutoff));
    }
    public static function export() {
        if(!current_user_can('manage_options')){wp_die('Nedovoljne dozvole.','',array('response'=>403));}check_admin_referer('snd_ranking_export');nocache_headers();
        header('Content-Type: text/csv; charset=utf-8');header('Content-Disposition: attachment; filename="snd-bodovanje.csv"');$out=fopen('php://output','w');fwrite($out,"\xEF\xBB\xBF");fputcsv($out,array('user_id','nickname','kind','mode','date','points','territory','quiz','penalty','hidden','completed_at'),',','"','');
        if(self::ready()){global $wpdb;$f=self::filters();$where='points IS NOT NULL';$args=array();if($f['board']==='daily'){$where.=" AND mode='daily'";}if($f['game']){$where.=' AND kind=%s';$args[]=$f['game'];}if($f['period']==='day'){$where.=' AND played_on=%s';$args[]=$f['rank_date'];}elseif($f['period']==='month'){$where.=' AND played_on>=%s AND played_on<=%s';$args[]=$f['rank_month'].'-01';$args[]=(new DateTimeImmutable($f['rank_month'].'-01'))->format('Y-m-t');}if($args){$where=$wpdb->prepare($where,$args);}$cursor=0;
            do{$rows=$wpdb->get_results($wpdb->prepare('SELECT * FROM '.self::table()." WHERE $where AND id>%d ORDER BY id LIMIT 200",$cursor),ARRAY_A);foreach($rows as $r){$row=array($r['user_id'],self::alias($r['user_id']),$r['kind'],$r['mode'],$r['played_on'],$r['points'],$r['territory_points'],$r['quiz_points'],$r['penalty'],$r['hidden'],$r['completed_at']);fputcsv($out,array_map(array('SND_Control','csv_cell'),$row),',','"','');$cursor=(int)$r['id'];}}while(count($rows)===200);
        }fclose($out);exit;
    }
}
