<?php
// Isolated local/development WordPress only. Creates then deletes two subscribers.
$root=getenv('WP_ROOT');if(!$root||!is_file(rtrim($root,'/').'/wp-load.php')){exit("Set WP_ROOT.\n");}
$_SERVER['HTTP_HOST']=parse_url(getenv('WP_TEST_URL')?:'http://snd.test',PHP_URL_HOST);
$_SERVER['REQUEST_METHOD']='GET';$_SERVER['REQUEST_URI']='/';$_SERVER['SERVER_PROTOCOL']='HTTP/1.1';
require rtrim($root,'/').'/wp-load.php';
if(!in_array(wp_get_environment_type(),array('local','development'),true)){exit("Refusing production/staging.\n");}
if(!class_exists('SND_Progress')){exit("Activate Srbija na dlanu first.\n");}
$count=0;$users=array();$tokens=array();$old_user=get_current_user_id();$old_flag=get_option('snd_flag_overrides',array());
function expect_progress($condition,$message){global $count;$count++;if(!$condition){throw new RuntimeException($message);}}
function p_api($path,$body){$r=new WP_REST_Request('POST','/srbija-na-dlanu/v1/'.$path);$r->set_header('Content-Type','application/json');$r->set_body(wp_json_encode($body));return rest_do_request($r);}
function p_move($g,$action,$value=null){$r=p_api('action',array('token'=>$g['token'],'revision'=>$g['state']['revision'],'action'=>$action,'value'=>$value));expect_progress($r->get_status()===200,'Action '.$action);return $r->get_data();}
try{
    foreach(array('a','b') as $s){$id=wp_insert_user(array('user_login'=>'snd-test-'.bin2hex(random_bytes(6)).$s,'user_pass'=>wp_generate_password(30),'role'=>'subscriber'));expect_progress(!is_wp_error($id),'Create test subscriber');$users[]=$id;}
    foreach(SND_Flags::all() as $key=>$flag){
        expect_progress(!is_wp_error(SND_Flags::valid($flag)),'Valid licensed flag '.$key);
        expect_progress(is_file(SND_DIR.'assets/'.$flag['image']),'Local flag file '.$key);
    }
    expect_progress(SND_Text::key('Ђаковица')===SND_Text::key('Đakovica'),'Cyrillic flags/search normalization');
    expect_progress(is_wp_error(SND_Flags::valid(array('image'=>'javascript:alert(1)'))),'Flag script URL rejected');
    expect_progress(is_wp_error(SND_Flags::valid(array('name'=>'X','image'=>'https://example.test/a.png'))),'Flag needs credit and license');
    $lease_key='snd_test_lock_'.bin2hex(random_bytes(6));$lease1=SND_Lock::acquire($lease_key);
    expect_progress((bool)$lease1,'Lock acquired');expect_progress(SND_Lock::acquire($lease_key)===false,'Second request cannot acquire current lock');
    global $wpdb;$wpdb->update($wpdb->options,array('option_value'=>(time()-120).':expired'),array('option_name'=>$lease_key));
    $lease2=SND_Lock::acquire($lease_key);expect_progress((bool)$lease2,'Expired lock can be recovered');
    SND_Lock::release($lease_key,$lease1);expect_progress(SND_Lock::acquire($lease_key)===false,'Stale owner cannot release new lease');
    SND_Lock::release($lease_key,$lease2);$lease3=SND_Lock::acquire($lease_key);expect_progress((bool)$lease3,'Released lock can be reacquired');SND_Lock::release($lease_key,$lease3);
    wp_set_current_user($users[0]);
    $g=p_api('start',array('kind'=>'district','mode'=>'practice','fresh'=>true))->get_data();$tokens[]=$g['token'];
    expect_progress(p_api('start',array('kind'=>'district','mode'=>'practice'))->get_data()['token']===$g['token'],'Account practice resumes server token');
    expect_progress(SND_Game::load($g['token'])['owner']===$users[0],'Owner set by authenticated server identity');
    wp_set_current_user($users[1]);expect_progress(p_api('resume',array('token'=>$g['token']))->get_status()===403,'Other account denied resume');
    expect_progress(p_api('action',array('token'=>$g['token'],'revision'=>0,'action'=>'giveup'))->get_status()===403,'Other account denied action');
    expect_progress(p_api('claim',array('token'=>$g['token']))->get_status()===403,'Other account denied claim');
    wp_set_current_user(0);expect_progress(p_api('resume',array('token'=>$g['token']))->get_status()===403,'Guest denied private token');
    $anonymous=p_api('start',array('kind'=>'municipality','mode'=>'practice'))->get_data();$tokens[]=$anonymous['token'];$anonymous=p_move($anonymous,'giveup');$anonymous=p_move($anonymous,'finish');
    expect_progress(p_api('claim',array('token'=>$anonymous['token']))->get_status()===401,'Guest cannot own a result');
    wp_set_current_user($users[0]);
    $g=p_move($g,'giveup');$g=p_move($g,'next');
    for($i=0;$i<4;$i++){$s=SND_Game::load($g['token']);$q=$s['challenge']['questions'][$i];$g=p_move($g,'answer',$q['correct']);$g=p_move($g,'next');}
    $p=SND_Progress::summary($users[0])['district'];expect_progress($p['practice_count']===1&&$p['quiz_correct']===4&&$p['quiz_total']===4,'Only server-scored answers recorded');
    p_api('resume',array('token'=>$g['token']));expect_progress(SND_Progress::summary($users[0])['district']===$p,'Repeated result is idempotent');
    $r=p_api('claim',array('token'=>$anonymous['token']));expect_progress($r->get_status()===200,'Complete guest game claimed');
    expect_progress(SND_Progress::summary($users[0])['municipality']['practice_count']===1,'Guest result recorded from server state');
    p_api('claim',array('token'=>$anonymous['token']));expect_progress(SND_Progress::summary($users[0])['municipality']['practice_count']===1,'Claim cannot duplicate progress');
    $lock='snd_start_lock_'.$users[0].'_district_daily';add_option($lock,time(),'',false);
    expect_progress(p_api('start',array('kind'=>'district','mode'=>'daily'))->get_status()===409,'Concurrent start is locked');delete_option($lock);
    $daily=p_api('start',array('kind'=>'district','mode'=>'daily'))->get_data();$tokens[]=$daily['token'];
    expect_progress(p_api('start',array('kind'=>'district','mode'=>'daily','fresh'=>true))->get_data()['token']===$daily['token'],'Daily cannot reset account attempt');
    $lock='snd_lock_'.$daily['token'];add_option($lock,time(),'',false);
    expect_progress(p_api('action',array('token'=>$daily['token'],'revision'=>0,'action'=>'giveup'))->get_status()===409,'Concurrent action is locked');delete_option($lock);
    $daily=p_move($daily,'giveup');$daily=p_move($daily,'finish');
    $p=SND_Progress::summary($users[0])['district'];expect_progress($p['daily_count']===1&&$p['streak']===1,'Daily result and streak recorded');
    // A guest daily completion on another device cannot add a second daily score.
    wp_set_current_user(0);$another=p_api('start',array('kind'=>'district','mode'=>'daily'))->get_data();$tokens[]=$another['token'];$another=p_move($another,'giveup');$another=p_move($another,'finish');
    wp_set_current_user($users[0]);$r=p_api('claim',array('token'=>$another['token']));expect_progress($r->get_data()['token']===$daily['token'],'Existing daily progress wins over guest completion');
    expect_progress(SND_Progress::summary($users[0])['district']===$p,'One daily result per kind and date');
}finally{
    update_option('snd_flag_overrides',$old_flag,false);wp_set_current_user($old_user);
    require_once ABSPATH.'wp-admin/includes/user.php';foreach($users as $id){wp_delete_user($id);}
    foreach($tokens as $token){delete_transient('snd_session_'.$token);}
}
echo 'PASS: '.$count.' account/flag assertions.'.PHP_EOL;
