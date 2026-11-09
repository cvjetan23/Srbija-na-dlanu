<?php
$root=getenv('WP_ROOT');if(!$root || !is_file($root.'/wp-load.php')){exit("Set isolated WP_ROOT.\n");}$_SERVER['HTTP_HOST']='snd.test';$_SERVER['REQUEST_METHOD']='GET';$_SERVER['REQUEST_URI']='/';require $root.'/wp-load.php';
if(PHP_SAPI!=='cli' || !in_array(wp_get_environment_type(),array('local','development'),true)){exit("Refusing production.\n");}
$count=0;$tokens=array();$users=array();$saved=get_option('snd_settings',null);$saved_user=get_current_user_id();$custom=get_option('snd_explore_catalogue',null);
function ex_ok($v,$label){global $count;$count++;if(!$v){throw new RuntimeException($label);}}
function ex_api($route,$data=array()){$r=new WP_REST_Request($route==='bootstrap'?'GET':'POST','/srbija-na-dlanu/v1/'.$route);$r->set_body_params($data);return rest_do_request($r);}
function ex_move($g,$action,$value=null){$r=ex_api('action',array('token'=>$g['token'],'revision'=>$g['state']['revision'],'action'=>$action,'value'=>$value,'points'=>999));ex_ok($r->get_status()===200,'Move '.$action);return $r->get_data();}
try{
 delete_option('snd_explore_catalogue');SND_Explore::reset();update_option('snd_settings',SND_Settings::defaults(),false);SND_Settings::reset();SND_Site::install();
 ex_ok(SND_Settings::get('daily_only') && !SND_Settings::public_config()['practice_enabled'],'Production defaults only daily games');
 $boot=ex_api('bootstrap')->get_data();ex_ok(count($boot['counts'])===5 && count($boot['games'])===5,'All five game types published');
 $valid=SND_Explore::validate(SND_Explore::data());ex_ok(!is_wp_error($valid),'Imported catalogues validate');
 foreach(SND_Kinds::EXPLORE as $kind){
  ex_ok(count(SND_Explore::pool($kind))>=($kind==='river'?60:100),'Substantial sourced catalogue '.$kind);ex_ok(count(SND_Explore::targets($kind))>=20,'Complete daily tasks with photos '.$kind);
  $kim=array_filter(SND_Explore::pool($kind),function($t){return $t['region']==='Kosovo i Metohija';});ex_ok(count($kim)>0,'KiM geography in '.$kind);
  foreach($kim as $t){if($kind==='city'){ex_ok(empty($t['population']) && empty($t['year']),'No invented KiM 2022 census');}}
  foreach(SND_Explore::targets($kind) as $t){
   ex_ok(is_file(SND_DIR.'assets/'.$t['photo']['image']) && !empty($t['photo']['credit']) && !empty($t['photo']['license']) && !empty($t['photo']['source_url']),'Local photo and rights '.$t['id']);
   $questions=SND_Explore::questions($t,SND_Explore::pool($kind),'all-targets:'.$t['id']);ex_ok(count($questions)===4,'Four meaningful questions '.$t['id']);
   foreach(array('sr','en','de','ru') as $lang){SND_I18n::force($lang);foreach($questions as $q){$q=SND_I18n::question($q,$t);ex_ok(count($q['options'])===4 && count(array_unique(array_column($q['options'],'id')))===4,'Four unique choices');ex_ok(in_array($q['correct'],array_column($q['options'],'id'),true),'Correct option exists');ex_ok(strpos($q['title'],'{')===false && strpos($q['explanation'],'{')===false,'All localized variables resolved');if($lang!=='sr'){ex_ok($q['title']!==SND_I18n::t($questions[array_search($q['type'],array_column($questions,'type'))]['title_key'],array(),'sr'),'Localized question title');}}}
  }
 }
 SND_I18n::force('sr');
 foreach(array('a','b') as $letter){$id=wp_insert_user(array('user_login'=>'snd-explore-'.$letter.'-'.bin2hex(random_bytes(5)),'user_pass'=>bin2hex(random_bytes(20)),'user_email'=>bin2hex(random_bytes(8)).'@example.invalid','role'=>'subscriber'));ex_ok(!is_wp_error($id),'Test account');$users[]=$id;}
 wp_set_current_user($users[0]);$sum=0;
 foreach(SND_Kinds::ALL as $kind){
  delete_option('snd_day_v4_'.str_replace('-','',SND_Game::date()).'_'.$kind);$r=ex_api('start',array('kind'=>$kind,'mode'=>'daily','fresh'=>true,'request_id'=>bin2hex(random_bytes(16))));ex_ok($r->get_status()===200,'Daily start '.$kind);$g=$r->get_data();$tokens[]=$g['token'];$s=SND_Game::load($g['token']);
  if(SND_Kinds::is_explore($kind)){ex_ok(isset($g['state']['explore_map']) && !isset($g['state']['explore_map']['target']['name']) && !isset($g['state']['explore_map']['target']['id']),'Anonymous target geometry');}
  $same=ex_api('start',array('kind'=>$kind,'mode'=>'daily','fresh'=>true))->get_data();ex_ok($same['token']===$g['token'],'Fresh never restarts an account daily task');
  $g=ex_move($g,'guess',$s['challenge']['target']['id']);$g=ex_move($g,'next');
  while($g['state']['stage']==='quiz'){
   $q=$g['state']['question'];ex_ok(!isset($q['correct'])&&!isset($q['explanation'])&&!isset($q['correct_label']),'Answers private before submit');$internal=SND_Game::load($g['token']);$q=SND_Game::question_for($internal,$internal['question_index']);$g=ex_move($g,'answer',$q['correct']);$g=ex_move($g,'next');
  }
  ex_ok($g['state']['points']['total']===100 && $g['state']['rank_eligible'],'Server calculates 100/100 '.$kind);$sum+=$g['state']['points']['total'];ex_ok(count($g['state']['review'])===4,'Four review entries');
  ex_api('resume',array('token'=>$g['token']));ex_api('resume',array('token'=>$g['token']));ex_ok(SND_Progress::summary($users[0])[$kind]['daily_count']===1,'Resume never duplicates progress');
 }
 ex_ok($sum===500,'Five games maximum500');$ranking=SND_Ranking::results(array('board'=>'general','period'=>'day'));$own=array_values(array_filter($ranking['rows'],function($r)use($users){return (int)$r['user_id']===$users[0];}));ex_ok(count($own)===1 && (int)$own[0]['total']===500 && (int)$own[0]['games']===5,'Combined daily ranking 500 across five games');
 foreach(SND_Kinds::ALL as $kind){$rows=SND_Ranking::results(array('game'=>$kind,'period'=>'day'));$own=array_values(array_filter($rows['rows'],function($r)use($users){return (int)$r['user_id']===$users[0];}));ex_ok(count($own)===1 && (int)$own[0]['total']===100,'Per-game board '.$kind);}
 ex_ok(ex_api('start',array('kind'=>'city','mode'=>'practice'))->get_status()===503 || ex_api('start',array('kind'=>'city','mode'=>'practice'))->get_status()===403,'Practice rejected by production policy');
 wp_set_current_user($users[1]);ex_ok(ex_api('resume',array('token'=>$tokens[0]))->get_status()===403,'Ownership enforced');
 $positions=array();foreach(SND_Explore::targets('city') as $photo_target){$photo=SND_Explore::photo_question($photo_target,SND_Explore::pool('city'),'labels:'.$photo_target['id']);foreach($photo['options'] as $pos=>$option){if($option['id']===$photo['correct']){$positions[$pos]=true;}ex_ok($option['label']==='Fotografija '.chr(65+$pos),'Photo letters follow displayed positions');}}ex_ok(count($positions)===4,'Correct photo appears in all four positions across the daily fund');
 $r=ex_api('start',array('kind'=>'river','mode'=>'daily'));$g=$r->get_data();$tokens[]=$g['token'];$internal=SND_Game::load($g['token']);$wrong=array_values(array_filter($g['state']['catalog'],function($x)use($internal){return $x['id']!==$internal['challenge']['target']['id'];}));
 for($i=0;$i<3;$i++){$g=ex_move($g,'guess',$wrong[$i]['id']);ex_ok(count($g['state']['explore_map']['attempts'])===$i+1 && $g['state']['explore_map']['attempts'][$i]['path']!=='','Wrong river geometry appears');}
 $g=ex_move($g,'hint');$body=array('token'=>$g['token'],'revision'=>$g['state']['revision'],'action'=>'guess','value'=>$internal['challenge']['target']['id']);$r=ex_api('action',$body);ex_ok($r->get_status()===200,'Fourth correct guess');$g=$r->get_data();ex_ok(ex_api('action',$body)->get_status()===409,'Duplicate request consumes no attempt');$g=ex_move($g,'finish');ex_ok($g['state']['points']['total']===32,'Fourth guess42 less hint10; skipped quiz0');
 $bad=SND_Explore::data();$bad['items'][0]['map_path']='<script>alert(1)</script>';ex_ok(is_wp_error(SND_Explore::validate($bad)),'SVG script rejected');$bad=SND_Explore::data();$bad['items'][0]['center']=array(0,0);ex_ok(is_wp_error(SND_Explore::validate($bad)),'Out-of-country coordinates rejected');
 $bad=SND_Explore::data();$bad['items'][0]['photo']['image']='javascript:alert(1)';ex_ok(is_wp_error(SND_Explore::validate($bad)),'Unsafe photo URL rejected');
 global $wpdb;$table=SND_Ranking::table();$legacy='archive-'.bin2hex(random_bytes(10));$wpdb->insert($table,array('slot_key'=>hash('sha256',$legacy),'game_id'=>$legacy,'user_id'=>$users[0],'kind'=>'district','mode'=>'practice','season'=>0,'played_on'=>SND_Game::date(),'points'=>100,'policy'=>'{}'));$rows=SND_Ranking::results(array('board'=>'general','period'=>'day'));$own=array_values(array_filter($rows['rows'],function($r)use($users){return (int)$r['user_id']===$users[0];}));ex_ok((int)$own[0]['total']===500,'Archive practice cannot enter new public season');
 echo 'PASS: '.$count.' five-game geography, provenance, photos, daily policy, score, ranking, ownership and validation assertions.'.PHP_EOL;
}finally{
 require_once ABSPATH.'wp-admin/includes/user.php';foreach($users as $id){wp_delete_user($id);}foreach($tokens as $t){delete_transient('snd_session_'.$t);}if($saved===null){delete_option('snd_settings');}else{update_option('snd_settings',$saved,false);}if($custom===null){delete_option('snd_explore_catalogue');}else{update_option('snd_explore_catalogue',$custom,false);}SND_Settings::reset();SND_Explore::reset();SND_I18n::force(null);wp_set_current_user($saved_user);
}
