<?php
// Content replacement must not rewrite frozen games or saved answer IDs.
$root=getenv('WP_ROOT');if(!$root || !is_file($root.'/wp-load.php')){exit("Set WP_ROOT.\n");}$_SERVER['HTTP_HOST']='snd.test';$_SERVER['REQUEST_METHOD']='GET';$_SERVER['REQUEST_URI']='/';require $root.'/wp-load.php';
if(!in_array(wp_get_environment_type(),array('local','development'),true)){exit('Refusing production.');}
require __DIR__.'/legacy-mode.php';$n=0;$tokens=array();$user=get_current_user_id();$date=SND_Game::date();$key='snd_day_v'.SND_Game::RULESET.'_'.str_replace('-','',$date).'_district';$saved=get_option($key,null);
function fix425_ok($v,$label){global $n;$n++;if(!$v){throw new RuntimeException($label);}}
try{
 wp_set_current_user(0);$data=SND_Data::get()['territories'];$current=$data['rs-00'];$raw=require SND_DIR.'data/territories.php';$old=array_values(array_filter($raw['territories'],function($t){return $t['id']==='rs-00';}))[0];
 fix425_ok($current['landmark']['name']==='Hram Svetog Save' && $data['m-beograd']['landmark']['name']==='Hram Svetog Save','Reviewed Belgrade church is the new default');
 fix425_ok($data['m-bg-stari-grad']['landmark']['name']==='Beogradska tvrđava','Old Town retains its geographically correct landmark');
 fix425_ok(is_file(SND_DIR.'assets/'.$current['landmark']['image']) && $current['landmark']['license']==='Public domain','Attributed church image is available offline');
 $c=SND_Game::challenge('district','practice');$c['target']=$old;$c['questions']=SND_Game::questions($old,SND_Data::pool('district'),SND_Data::pool('district'),'425-old-day');$c['date']=$date;$c['mode']='daily';$c['storage_key']=$key;$c['options']['rounds']=array('population','administration','flag','landmark');update_option($key,$c,false);
 $new=SND_Game::create('district','daily');$tokens[]=$new['token'];$s=SND_Game::load($new['token']);$q=SND_Game::question_for($s,3);
 fix425_ok($q['image_source']===$current['landmark']['source_url'],'New session replaces cached default photograph');
 fix425_ok(get_option($key)===$c,'Shared frozen challenge is unchanged');
 $s['stage']='quiz';$s['question_index']=3;
 foreach(array('sr','en','de','ru') as $lang){SND_I18n::force($lang);$out=SND_Game::public_state($s);fix425_ok(!array_intersect(array('correct','correct_label','explanation','image_source','landmark'),array_keys($out['question'])),'No new solution field leaked '.$lang);$translated=SND_I18n::question($q,$old);fix425_ok($translated['correct_label']===SND_I18n::landmark_name('Hram Svetog Save') && strpos($translated['explanation'],'Kalemegdan')===false,'Cached target explains the actual new photograph '.$lang);}
 $legacy=$s;unset($legacy['question_overrides']);$legacy['answers'][3]=array('choice'=>$c['questions'][3]['correct'],'correct'=>true,'skipped'=>false);SND_Game::persist($new['token'],$legacy);$loaded=SND_Game::load($new['token']);
 fix425_ok(SND_Game::question_for($loaded,3)===$c['questions'][3] && $loaded['answers'][3]['choice']===$c['questions'][3]['correct'],'Started game keeps its original image and answer ID');
 fix425_ok(SND_I18n::landmark_quiz_name('Beogradska tvrđava','sr')==='Beogradska tvrđava (Kalemegdan)','Legacy fortress has a familiar name');
 echo 'PASS: '.$n.' reviewed church, cached daily replacement, saved-answer stability and localization assertions.'.PHP_EOL;
}finally{foreach($tokens as $token){delete_transient('snd_session_'.$token);}if($saved===null){delete_option($key);}else{update_option($key,$saved,false);}SND_I18n::force(null);wp_set_current_user($user);}
