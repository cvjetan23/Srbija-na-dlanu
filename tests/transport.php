<?php
$root=getenv('WP_ROOT');if(!$root || !is_file($root.'/wp-load.php')){exit("Set WP_ROOT.\n");}$_SERVER['HTTP_HOST']='snd.test';$_SERVER['REQUEST_METHOD']='GET';$_SERVER['REQUEST_URI']='/';require $root.'/wp-load.php';
if(!in_array(wp_get_environment_type(),array('local','development'),true)){exit("Refusing production.\n");}
$count=0;$users=array();$tokens=array();$id=get_current_user_id();
function tx_ok($v,$label){global $count;$count++;if(!$v){throw new RuntimeException($label);}}
function tx_call($route,$data,$nonce=''){return SND_API::ajax_request($route,wp_json_encode($data),$nonce);}
try{
    wp_set_current_user(0);tx_ok(tx_call('admin/setup',array())->get_status()===400,'Bridge cannot expose admin routes');tx_ok(SND_API::ajax_request('start','not-json')->get_status()===400,'Malformed bridge payload rejected');
    tx_ok(tx_call('claim',array('token'=>str_repeat('a',48)))->get_status()===401,'Guest cannot claim account game');
    $request_id=bin2hex(random_bytes(16));$r=tx_call('start',array('kind'=>'district','mode'=>'practice','fresh'=>true,'request_id'=>$request_id));tx_ok($r->get_status()===200,'Guest bridge starts game');$g=$r->get_data();$tokens[]=$g['token'];
    $second=tx_call('start',array('kind'=>'district','mode'=>'practice','fresh'=>true,'request_id'=>$request_id))->get_data();tx_ok($second['token']===$g['token'],'Lost start response does not create another game');
    $body=array('token'=>$g['token'],'revision'=>$g['state']['revision'],'action'=>'giveup');$r=tx_call('action',$body);tx_ok($r->get_status()===200,'AJAX action accepted');$g=$r->get_data();
    tx_ok(tx_call('action',$body)->get_status()===409,'Duplicate action does not advance twice');$r=tx_call('resume',array('token'=>$g['token']));tx_ok($r->get_data()['state']['revision']===$g['state']['revision'],'Resume confirms committed revision');
    foreach(array('a','b') as $suffix){$u=wp_insert_user(array('user_login'=>'snd-transport-'.$suffix.'-'.bin2hex(random_bytes(6)),'user_email'=>bin2hex(random_bytes(6)).'@example.invalid','user_pass'=>bin2hex(random_bytes(20)),'role'=>'subscriber'));tx_ok(!is_wp_error($u),'Isolated account');$users[]=$u;}
    wp_set_current_user($users[0]);tx_ok(tx_call('bootstrap',array())->get_status()===403,'Logged-in bridge requires REST nonce');tx_ok(tx_call('resume',array('token'=>$g['token']),'bad')->get_status()===403,'Invalid nonce rejected');$nonce=wp_create_nonce('wp_rest');
    $r=tx_call('claim',array('token'=>$g['token']),$nonce);tx_ok($r->get_status()===200,'Authenticated claim preserves owner rules');$g=$r->get_data();$r=tx_call('action',array('token'=>$g['token'],'revision'=>$g['state']['revision'],'action'=>'finish'),$nonce);tx_ok($r->get_status()===200,'Authenticated finish through bridge');
    tx_ok(SND_Progress::summary($users[0])['district']['practice_count']===1,'Progress recorded once');$r=tx_call('resume',array('token'=>$g['token']),$nonce);tx_ok(SND_Progress::summary($users[0])['district']['practice_count']===1,'Resume never duplicates score');
    wp_set_current_user($users[1]);tx_ok(tx_call('resume',array('token'=>$g['token']),wp_create_nonce('wp_rest'))->get_status()===403,'Other account cannot open private token');
    echo 'PASS: '.$count.' native WP bridge, nonce, ownership and idempotency assertions.'.PHP_EOL;
}finally{require_once ABSPATH.'wp-admin/includes/user.php';foreach($users as $u){wp_delete_user($u);}foreach($tokens as $t){delete_transient('snd_session_'.$t);}wp_set_current_user($id);}
