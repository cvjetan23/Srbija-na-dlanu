<?php
defined('ABSPATH') || exit;

final class SND_Analytics {
    const DB_VERSION=1;
    public static function games_table(){global $wpdb;return $wpdb->prefix.'snd_games';}
    public static function events_table(){global $wpdb;return $wpdb->prefix.'snd_events';}
    public static function ready(){return (int)get_option('snd_analytics_db',0)===self::DB_VERSION;}
    public static function install() {
        if(self::ready()){return;}
        global $wpdb;require_once ABSPATH.'wp-admin/includes/upgrade.php';$collate=$wpdb->get_charset_collate();
        $games=self::games_table();$events=self::events_table();
        dbDelta("CREATE TABLE $games (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            game_id varchar(64) NOT NULL,
            user_id bigint(20) unsigned NOT NULL DEFAULT 0,
            actor_hash varchar(64) NOT NULL DEFAULT '',
            kind varchar(20) NOT NULL,
            mode varchar(20) NOT NULL,
            played_on date NOT NULL,
            territory_id varchar(80) NOT NULL,
            territory_name varchar(190) NOT NULL,
            stage varchar(20) NOT NULL,
            outcome varchar(20) NOT NULL DEFAULT 'playing',
            guesses smallint(5) unsigned NOT NULL DEFAULT 0,
            max_guesses smallint(5) unsigned NOT NULL DEFAULT 6,
            score smallint(5) unsigned NOT NULL DEFAULT 0,
            question_count smallint(5) unsigned NOT NULL DEFAULT 4,
            skipped smallint(5) unsigned NOT NULL DEFAULT 0,
            assisted tinyint(1) unsigned NOT NULL DEFAULT 0,
            language varchar(5) NOT NULL DEFAULT 'sr',
            theme varchar(8) NOT NULL DEFAULT 'light',
            started_at datetime NULL,
            last_at datetime NOT NULL,
            completed_at datetime NULL,
            elapsed_seconds int(10) unsigned NULL,
            revision int(10) unsigned NOT NULL DEFAULT 0,
            legacy tinyint(1) unsigned NOT NULL DEFAULT 0,
            details longtext NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY game_id (game_id),
            KEY user_date (user_id,played_on),
            KEY date_kind (played_on,kind,mode),
            KEY stage_date (stage,played_on),
            KEY actor (actor_hash)
        ) $collate;");
        dbDelta("CREATE TABLE $events (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            created_at datetime NOT NULL,
            played_on date NOT NULL,
            event varchar(32) NOT NULL,
            user_id bigint(20) unsigned NOT NULL DEFAULT 0,
            actor_hash varchar(64) NOT NULL,
            game_id varchar(64) NOT NULL DEFAULT '',
            view varchar(24) NOT NULL DEFAULT '',
            language varchar(5) NOT NULL DEFAULT 'sr',
            theme varchar(8) NOT NULL DEFAULT 'light',
            device varchar(12) NOT NULL DEFAULT '',
            referrer varchar(190) NOT NULL DEFAULT '',
            value int(10) unsigned NOT NULL DEFAULT 0,
            code varchar(64) NOT NULL DEFAULT '',
            PRIMARY KEY  (id),
            KEY day_event (played_on,event),
            KEY user_date (user_id,played_on),
            KEY actor (actor_hash),
            KEY game_id (game_id)
        ) $collate;");
        update_option('snd_analytics_db',self::DB_VERSION,false);
        if(!wp_next_scheduled('snd_analytics_backfill')){wp_schedule_single_event(time()+30,'snd_analytics_backfill');}
    }
    public static function actor($user_id=0){return hash_hmac('sha256',$user_id?'user:'.$user_id:'guest:'.SND_Rate::client_id(),wp_salt('auth'));}
    public static function context($input=array()) {
        $input=is_array($input)?$input:array();
        $theme=$input['theme']??($_SERVER['HTTP_X_SND_THEME']??'light');
        return array('language'=>SND_I18n::lang(),'theme'=>in_array($theme,array('light','dark'),true)?$theme:'light');
    }
    public static function enabled($user_id=0){return self::ready() && SND_Settings::get('analytics_enabled') && ($user_id || SND_Settings::get('guest_analytics'));}
    public static function event($event,$data=array(),$user_id=null) {
        $user_id=$user_id===null?get_current_user_id():(int)$user_id;if(!self::enabled($user_id)){return false;}
        global $wpdb;$ctx=self::context($data);
        return $wpdb->insert(self::events_table(),array('created_at'=>gmdate('Y-m-d H:i:s'),'played_on'=>SND_Game::date(),
            'event'=>substr(sanitize_key($event),0,32),'user_id'=>$user_id,'actor_hash'=>self::actor($user_id),
            'game_id'=>substr(sanitize_key($data['game_id']??''),0,64),'view'=>substr(sanitize_key($data['view']??''),0,24),
            'language'=>$ctx['language'],'theme'=>$ctx['theme'],'device'=>in_array($data['device']??'',array('mobile','tablet','desktop'),true)?$data['device']:'',
            'referrer'=>substr(sanitize_text_field($data['referrer']??''),0,190),'value'=>max(0,min(90,(int)($data['value']??0))),
            'code'=>substr(sanitize_key($data['code']??''),0,64)));
    }
    public static function details($s) {
        $c=$s['challenge'];$questions=array();
        foreach($c['questions'] as $i=>$unused){
            if(!isset($s['answers'][$i]) && $s['stage']!=='complete'){continue;}
            $q=SND_Game::question_for($s,$i);$a=$s['answers'][$i]??array('choice'=>null,'correct'=>false,'skipped'=>true);$choice=array();$correct=array();
            foreach($q['options'] as $o){if($o['id']===($a['choice']??null)){$choice=$o;}if($o['id']===$q['correct']){$correct=$o;}}
            $questions[]=array('index'=>$i+1,'type'=>$q['type'],'question'=>$q['title'],'choice'=>$choice['label']??'',
                'correct_answer'=>$q['correct_label']??($correct['label']??''),'correct_image'=>$correct['image']??($q['image']??''),
                'correct'=>!empty($a['correct']),'skipped'=>!empty($a['skipped']),'source_help'=>!empty($s['source_help_questions'][$i]),
                'technical_replacement'=>!empty($s['technical_replacements'][$i]));
        }
        return array('guesses'=>$s['guesses'],'questions'=>$questions,'activity'=>array_slice($s['activity_log']??array(),-100),
            'ruleset'=>$c['ruleset']??1,'legacy'=>empty($s['started_at']),'points'=>SND_Scoring::calculate($s),'rank_eligible'=>!empty($s['rank_eligible']));
    }
    public static function capture($s) {
        $user_id=(int)($s['owner']??0);if(!self::enabled($user_id) || empty($s['challenge']) || empty($s['game_id'])){return;}
        global $wpdb;$table=self::games_table();$c=$s['challenge'];$id=$s['game_id'];
        $old=$wpdb->get_row($wpdb->prepare("SELECT id,revision,user_id,stage,completed_at FROM $table WHERE game_id=%s",$id),ARRAY_A);
        if($old && ((int)$old['revision']>(int)$s['revision'] || ((int)$old['user_id'] && !$user_id))){return;}
        $complete=$s['stage']==='complete';$options=SND_Settings::options_for($c);$max=$options['max_guesses'];
        $answers=$s['answers'];$now=gmdate('Y-m-d H:i:s');$started=!empty($s['started_at'])?(int)$s['started_at']:0;
        $row=array('game_id'=>$id,'user_id'=>$user_id,'actor_hash'=>self::actor($user_id),'kind'=>$c['kind'],'mode'=>$c['mode'],
            'played_on'=>$c['date'],'territory_id'=>$c['target']['id'],'territory_name'=>$c['target']['name'],'stage'=>$s['stage'],
            'outcome'=>!empty($s['solved'])?'solved':($s['stage']==='guess'?'playing':(count($s['guesses'])>=$max?'missed':'revealed')),
            'guesses'=>count($s['guesses']),'max_guesses'=>$max,'score'=>count(array_filter($answers,function($a){return !empty($a['correct']);})),
            'question_count'=>count($c['questions']),'skipped'=>count(array_filter($answers,function($a){return !empty($a['skipped']);})),
            'assisted'=>(int)(!empty($s['hint']) || !empty($s['source_help'])),'language'=>SND_I18n::lang(),
            'theme'=>self::context()['theme'],'started_at'=>$started?gmdate('Y-m-d H:i:s',$started):null,'last_at'=>$now,
            'completed_at'=>$complete?($old['completed_at']??gmdate('Y-m-d H:i:s',$s['completed_at']??time())):null,'elapsed_seconds'=>$complete && $started?max(0,($s['completed_at']??time())-$started):null,
            'revision'=>(int)$s['revision'],'legacy'=>$started?0:1,'details'=>wp_json_encode(self::details($s),JSON_UNESCAPED_UNICODE));
        if($old){$saved=$wpdb->update($table,$row,array('id'=>$old['id'],'revision'=>$old['revision'],'user_id'=>$old['user_id']));}
        else{$saved=$wpdb->insert($table,$row);}
        if($saved===false || ($old && $saved===0 && ((int)$old['revision']!==(int)$s['revision'] || (int)$old['user_id']!==$user_id))){return;}
        if($user_id && $complete && $c['mode']==='daily'){$wpdb->query($wpdb->prepare("DELETE FROM $table WHERE legacy=1 AND game_id LIKE %s AND user_id=%d AND kind=%s AND mode='daily' AND played_on=%s",'legacy-%',$user_id,$c['kind'],$c['date']));}
        if(!$old){self::event($started?'game_start':'game_seen',array('game_id'=>$id),$user_id);}
        if($complete && (!$old || $old['stage']!=='complete')){self::event('game_complete',array('game_id'=>$id),$user_id);}
        if($old && !$old['user_id'] && $user_id){$wpdb->update(self::events_table(),array('user_id'=>$user_id,'actor_hash'=>self::actor($user_id)),array('game_id'=>$id));}
    }
    public static function activity($request) {
        if(!self::enabled(get_current_user_id())){return SND_API::response(array('recorded'=>false));}
        if(!SND_Rate::allow('telemetry',180,600,SND_Rate::actor(),18000)){return new WP_Error('snd_rate','Sačekaj pre sledećeg zahteva.',array('status'=>429));}
        $view=$request->get_param('view');$event=$request->get_param('event');
        if(!in_array($view,SND_Site::VIEWS,true) || !in_array($event,array('pageview','heartbeat','preference'),true)){return new WP_Error('snd_activity','Nepoznata aktivnost.',array('status'=>400));}
        $actor=self::actor(get_current_user_id());$key='snd_metric_'.substr(hash('sha256',$actor.':'.$view.':'.$event),0,32);
        $last=(int)get_transient($key);$gap=$event==='pageview'?300:($event==='heartbeat'?15:2);
        if($last && time()-$last<$gap){return SND_API::response(array('recorded'=>false));}
        $value=$event==='heartbeat'?min(max(0,(int)$request->get_param('seconds')),max(0,min(90,$last?time()-$last:30))):1;
        set_transient($key,time(),10*MINUTE_IN_SECONDS);
        $referrer=$request->get_param('referrer');$host=is_string($referrer)?wp_parse_url($referrer,PHP_URL_HOST):'';
        if(!$host && is_string($referrer) && preg_match('/^[a-z0-9.-]+$/iD',$referrer)){$host=$referrer;}
        if($host===wp_parse_url(home_url(),PHP_URL_HOST)){$host='';}
        self::event($event,array('view'=>$view,'theme'=>$request->get_param('theme'),'device'=>$request->get_param('device'),'referrer'=>$host?:'','value'=>$value));
        return SND_API::response(array('recorded'=>true));
    }
    public static function rest_errors($result,$server,$request) {
        if(strpos($request->get_route(),'/srbija-na-dlanu/v1/')!==0 || $request->get_route()==='/srbija-na-dlanu/v1/activity'){return $result;}
        if(is_wp_error($result)){$code=$result->get_error_code();}
        elseif($result instanceof WP_REST_Response && $result->get_status()>=400){$data=$result->get_data();$code=is_array($data)?($data['code']??'request_error'):'request_error';}
        else{return $result;}
        $key='snd_error_metric_'.substr(hash('sha256',self::actor(get_current_user_id()).':'.$code),0,32);
        if(!get_transient($key)){self::event('error',array('code'=>$code));set_transient($key,1,MINUTE_IN_SECONDS);}
        return $result;
    }
    public static function backfill() {
        if(!self::ready() || get_option('snd_backfill_complete')){return;}
        global $wpdb;$cursor=(int)get_option('snd_backfill_cursor',0);$table=self::games_table();
        $rows=$wpdb->get_results($wpdb->prepare("SELECT user_id,meta_value FROM {$wpdb->usermeta} WHERE meta_key='snd_progress_v2' AND user_id>%d ORDER BY user_id ASC LIMIT 25",$cursor),ARRAY_A);
        foreach($rows as $row){
            $user_id=(int)$row['user_id'];$progress=maybe_unserialize($row['meta_value']);
            if(is_array($progress)){foreach(array('district','municipality') as $kind){foreach((array)($progress[$kind]['days']??array()) as $date=>$day){
                if(!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/D',(string)$date,$parts) || !checkdate((int)$parts[2],(int)$parts[3],(int)$parts[1]) || !is_array($day)){continue;}
                $retention=(int)SND_Settings::get('retention_days');if($retention && $date<(new DateTimeImmutable(SND_Game::date()))->modify('-'.$retention.' days')->format('Y-m-d')){continue;}
                if($wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE user_id=%d AND kind=%s AND mode='daily' AND played_on=%s AND stage='complete' LIMIT 1",$user_id,$kind,$date))){continue;}
                $inserted=$wpdb->insert($table,array('game_id'=>'legacy-'.substr(hash_hmac('sha256',$user_id.':'.$kind.':'.$date,wp_salt('auth')),0,57),'user_id'=>$user_id,'actor_hash'=>self::actor($user_id),
                    'kind'=>$kind,'mode'=>'daily','played_on'=>$date,'territory_id'=>sanitize_key($day['territory']??''),'territory_name'=>sanitize_text_field($day['name']??''),
                    'stage'=>'complete','outcome'=>!empty($day['solved'])?'solved':((int)($day['guesses']??0)>=6?'missed':'revealed'),'guesses'=>(int)($day['guesses']??0),'max_guesses'=>6,
                    'score'=>(int)($day['score']??0),'question_count'=>(int)($day['question_count']??4),'assisted'=>(int)!empty($day['hint']),
                    'last_at'=>gmdate('Y-m-d H:i:s'),'legacy'=>1,'details'=>wp_json_encode(array('legacy'=>true,'summary_only'=>true,'guesses'=>array(),'questions'=>array(),'activity'=>array()))));
                if($inserted===false){if(!wp_next_scheduled('snd_analytics_backfill')){wp_schedule_single_event(time()+60,'snd_analytics_backfill');}return;}
            }}}
            $cursor=$user_id;
        }
        update_option('snd_backfill_cursor',$cursor,false);
        if(count($rows)===25){wp_schedule_single_event(time()+15,'snd_analytics_backfill');}else{update_option('snd_backfill_complete',1,false);}
    }
    public static function clean() {
        if(!self::ready()){return;}$days=(int)SND_Settings::get('retention_days');if(!$days){return;}
        global $wpdb;$cutoff=(new DateTimeImmutable('now',new DateTimeZone('Europe/Belgrade')))->modify('-'.$days.' days')->format('Y-m-d');
        foreach(array(self::games_table(),self::events_table()) as $table){$wpdb->query($wpdb->prepare("DELETE FROM $table WHERE played_on<%s",$cutoff));}
    }
    public static function delete_user($user_id) {
        if(!self::ready()){return;}global $wpdb;
        if(!SND_Settings::get('erase_deleted_users')){
            $games=self::games_table();$events=self::events_table();$actor=hash('sha256',random_bytes(24));
            $ids=$wpdb->get_col($wpdb->prepare("SELECT game_id FROM $games WHERE user_id=%d",$user_id));
            foreach($ids as $id){$replacement='anon-'.bin2hex(random_bytes(20));$wpdb->update($games,array('game_id'=>$replacement),array('game_id'=>$id));$wpdb->update($events,array('game_id'=>$replacement),array('game_id'=>$id));}
            foreach(array($games,$events) as $table){$wpdb->update($table,array('user_id'=>0,'actor_hash'=>$actor),array('user_id'=>(int)$user_id));}return;
        }
        foreach(array(self::games_table(),self::events_table()) as $table){
            $wpdb->delete($table,array('user_id'=>(int)$user_id));
        }
    }
    public static function privacy_exporters($exporters) {$exporters['snd']=array('exporter_friendly_name'=>'Srbija na dlanu','callback'=>array(__CLASS__,'privacy_export'));return $exporters;}
    public static function privacy_erasers($erasers) {$erasers['snd']=array('eraser_friendly_name'=>'Srbija na dlanu','callback'=>array(__CLASS__,'privacy_erase'));return $erasers;}
    public static function privacy_export($email,$page=1) {
        $u=get_user_by('email',$email);if(!$u){return array('data'=>array(),'done'=>true);}global $wpdb;$data=array();$page=max(1,(int)$page);
        if($page===1){$data[]=array('group_id'=>'snd-progress','group_label'=>'Srbija na dlanu — progres','item_id'=>'snd-progress-'.$u->ID,'data'=>array(array('name'=>'Progres','value'=>wp_json_encode(SND_Progress::summary($u->ID),JSON_UNESCAPED_UNICODE))));}
        if($page===1 && SND_Ranking::ready()){$data[]=array('group_id'=>'snd-rank','group_label'=>'Srbija na dlanu — rang lista','item_id'=>'snd-rank-profile-'.$u->ID,'data'=>array(array('name'=>'Javni profil','value'=>wp_json_encode(get_user_meta($u->ID,'snd_rank_profile',true),JSON_UNESCAPED_UNICODE))));}
        if(SND_Ranking::ready()){$rank=$wpdb->get_results($wpdb->prepare('SELECT game_id,kind,mode,played_on,points,territory_points,quiz_points,penalty,hidden,completed_at,policy FROM '.SND_Ranking::table().' WHERE user_id=%d ORDER BY id LIMIT 100 OFFSET %d',$u->ID,($page-1)*100),ARRAY_A);foreach($rank as $row){$data[]=array('group_id'=>'snd-rank','group_label'=>'Srbija na dlanu — rang lista','item_id'=>'snd-rank-'.$row['game_id'],'data'=>array(array('name'=>'Bodovanje','value'=>wp_json_encode($row,JSON_UNESCAPED_UNICODE))));}}
        $done=empty($rank)||count($rank)<100;
        if(self::ready()){foreach(array(self::games_table()=>'partija',self::events_table()=>'aktivnost') as $table=>$type){
            $rows=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE user_id=%d ORDER BY id LIMIT 100 OFFSET %d",$u->ID,($page-1)*100),ARRAY_A);if(count($rows)===100){$done=false;}
            foreach($rows as $row){unset($row['actor_hash']);$data[]=array('group_id'=>'snd-'.$type,'group_label'=>'Srbija na dlanu — '.$type,'item_id'=>'snd-'.$type.'-'.$row['id'],'data'=>array(array('name'=>'Podaci','value'=>wp_json_encode($row,JSON_UNESCAPED_UNICODE))));}
        }}return array('data'=>$data,'done'=>$done);
    }
    public static function privacy_erase($email,$page=1) {
        $u=get_user_by('email',$email);if(!$u){return array('items_removed'=>false,'items_retained'=>false,'messages'=>array(),'done'=>true);}global $wpdb;
        if(self::ready()){foreach(array(self::games_table(),self::events_table()) as $table){$wpdb->delete($table,array('user_id'=>$u->ID));}}
        SND_Ranking::delete_user($u->ID);
        foreach(array('district','municipality') as $kind){foreach(array('daily','practice') as $mode){$slot=SND_Progress::slot($kind,$mode);$token=get_user_meta($u->ID,$slot,true);if(is_string($token) && preg_match('/^[a-f0-9]{48}$/D',$token)){delete_transient('snd_session_'.$token);}delete_user_meta($u->ID,$slot);}}
        update_user_meta($u->ID,'snd_privacy_epoch',bin2hex(random_bytes(16)));delete_user_meta($u->ID,'snd_progress_v2');return array('items_removed'=>true,'items_retained'=>false,'messages'=>array(),'done'=>true);
    }
    public static function filters($input=null) {
        $input=$input===null?wp_unslash($_GET):$input;$out=array();
        foreach(array('from','to') as $key){$value=$input[$key]??'';$valid=is_string($value) && preg_match('/^(\d{4})-(\d{2})-(\d{2})$/D',$value,$m) && checkdate((int)$m[2],(int)$m[3],(int)$m[1]);$out[$key]=$valid?$value:($key==='from'?(new DateTimeImmutable(SND_Game::date()))->modify('-29 days')->format('Y-m-d'):SND_Game::date());}
        if($out['from']>$out['to']){list($out['from'],$out['to'])=array($out['to'],$out['from']);}
        $out['kind']=in_array($input['kind']??'',array('district','municipality'),true)?$input['kind']:'';
        $out['mode']=in_array($input['mode']??'',array('daily','practice'),true)?$input['mode']:'';
        $out['user_id']=max(0,(int)($input['user_id']??0));$out['stage']=in_array($input['stage']??'',array('guess','reveal','quiz','complete'),true)?$input['stage']:'';
        $out['language']=in_array($input['language']??'',array('sr','en','de','ru'),true)?$input['language']:'';
        return $out;
    }
    public static function where($filters,$events=false) {
        global $wpdb;$clauses=array('played_on>=%s','played_on<=%s');$args=array($filters['from'],$filters['to']);
        foreach(($events?array('user_id','language'):array('kind','mode','user_id','stage','language')) as $key){if(!empty($filters[$key])){$clauses[]="$key=".($key==='user_id'?'%d':'%s');$args[]=$filters[$key];}}
        return $wpdb->prepare(implode(' AND ',$clauses),$args);
    }
    public static function summary($filters) {
        global $wpdb;$games=self::games_table();$events=self::events_table();$where=self::where($filters);$ew=self::where($filters,true);
        $totals=$wpdb->get_row("SELECT COUNT(*) games,SUM(stage='complete') completed,SUM(outcome='solved') solved,SUM(user_id=0) guest_games,COUNT(DISTINCT NULLIF(user_id,0)) players,AVG(CASE WHEN outcome='solved' THEN guesses END) avg_guesses,SUM(CASE WHEN stage='complete' THEN score ELSE 0 END) correct,SUM(question_count*(stage='complete')) questions,SUM(assisted) assisted,AVG(elapsed_seconds) avg_elapsed,SUM(legacy) legacy FROM $games WHERE $where",ARRAY_A);
        return array('totals'=>$totals?:array(),'views'=>(int)$wpdb->get_var("SELECT COUNT(*) FROM $events WHERE $ew AND event='pageview'"),
            'visitors'=>(int)$wpdb->get_var("SELECT COUNT(DISTINCT actor_hash) FROM $events WHERE $ew AND event='pageview'"),
            'active_seconds'=>(int)$wpdb->get_var("SELECT COALESCE(SUM(value),0) FROM $events WHERE $ew AND event='heartbeat'"),
            'daily'=>$wpdb->get_results("SELECT played_on,COUNT(*) started,SUM(stage='complete') completed FROM $games WHERE $where GROUP BY played_on ORDER BY played_on",ARRAY_A),
            'territories'=>$wpdb->get_results("SELECT territory_id,territory_name,COUNT(*) games,SUM(outcome='solved') solved,AVG(CASE WHEN outcome='solved' THEN guesses END) guesses FROM $games WHERE $where GROUP BY territory_id,territory_name ORDER BY games DESC LIMIT 15",ARRAY_A),
            'languages'=>$wpdb->get_results("SELECT language,COUNT(*) count FROM $events WHERE $ew AND event='pageview' GROUP BY language ORDER BY count DESC",ARRAY_A),
            'themes'=>$wpdb->get_results("SELECT theme,COUNT(*) count FROM $events WHERE $ew AND event='pageview' GROUP BY theme ORDER BY count DESC",ARRAY_A),
            'devices'=>$wpdb->get_results("SELECT device,COUNT(*) count FROM $events WHERE $ew AND event='pageview' GROUP BY device ORDER BY count DESC",ARRAY_A),
            'referrers'=>$wpdb->get_results("SELECT referrer,COUNT(*) count FROM $events WHERE $ew AND event='pageview' GROUP BY referrer ORDER BY count DESC LIMIT 10",ARRAY_A),
            'errors'=>$wpdb->get_results("SELECT code,COUNT(*) count FROM $events WHERE $ew AND event='error' GROUP BY code ORDER BY count DESC LIMIT 10",ARRAY_A));
    }
}
