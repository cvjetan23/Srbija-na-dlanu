<?php
// CLI fixture for an isolated WordPress. Never included in the plugin ZIP.
if(PHP_SAPI!=='cli'){exit(1);}$root=getenv('WP_ROOT');if(!$root || !is_file($root.'/wp-load.php')){exit(1);}
$_SERVER['HTTP_HOST']=parse_url(getenv('SND_TEST_URL')?:'http://snd.test',PHP_URL_HOST);$_SERVER['REQUEST_METHOD']='GET';$_SERVER['REQUEST_URI']='/';$_SERVER['SERVER_PROTOCOL']='HTTP/1.1';require $root.'/wp-load.php';
if(!in_array(wp_get_environment_type(),array('local','development'),true)){exit("Refusing production/staging.\n");}$a=json_decode(stream_get_contents(STDIN),true);$op=$a['op']??'';
if($op==='setup'){
    update_option('permalink_structure','/%postname%/');update_option('snd_http_saved_settings',get_option('snd_settings',array()),false);update_option('snd_settings',SND_Settings::defaults(),false);SND_Settings::reset();update_option('snd_registration_enabled',true,false);$pages=SND_Site::install();
    foreach(array('district','municipality') as $kind){delete_option('snd_day_v3_'.str_replace('-','',SND_Game::date()).'_'.$kind);}
    foreach(array('a','b','admin') as $name){$login='snd-http-'.$name;$u=get_user_by('login',$login);if($u){$id=$u->ID;wp_set_password($a['password'],$id);}else{$id=wp_insert_user(array('user_login'=>$login,'user_pass'=>$a['password'],'user_email'=>$login.'@example.invalid','role'=>$name==='admin'?'administrator':'subscriber'));}delete_user_meta($id,'snd_progress_v2');foreach(array('district','municipality') as $kind){foreach(array('daily','practice') as $mode){delete_user_meta($id,SND_Progress::slot($kind,$mode));}}}
    wp_mkdir_p(WPMU_PLUGIN_DIR);file_put_contents(WPMU_PLUGIN_DIR.'/snd-http-mail.php',"<?php if(in_array(wp_get_environment_type(),array('local','development'),true)){add_filter('pre_wp_mail',function(\$r,\$a){update_option('snd_http_mail',\$a,false);return true;},10,2);}");
    global $wpdb;foreach($wpdb->get_col("SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_snd_rate_%'") as $key){delete_option($key);}flush_rewrite_rules(false);echo wp_json_encode($pages);
}elseif($op==='state'){$u=get_user_by('login',$a['user']??'');if($u){wp_set_current_user($u->ID);}$s=SND_Game::load($a['token']);echo wp_json_encode(is_wp_error($s)?array('error'=>$s->get_error_code()):$s);
}elseif($op==='scenario'){
    wp_set_current_user(0);$s=SND_Game::load($a['token']);$t=SND_Data::get()['territories'][$a['target']]??null;
    if(is_wp_error($s) || !$t || !empty($s['owner']) || $s['challenge']['mode']!=='practice' || $s['challenge']['kind']!==$t['kind']){exit(1);}
    $pool=SND_Data::pool($t['kind']);if($t['id']==='m-beograd' && in_array('m-beograd',SND_Data::retired_ids(),true)){$seed=require SND_DIR.'data/territories.php';$pool=array();foreach($seed['territories'] as $row){if($row['kind']==='municipality'){$pool[$row['id']]=$row;}}$s['challenge']['catalog']=$pool;}
    $s['challenge']['target']=$t;$s['challenge']['questions']=SND_Game::questions($t,$pool,SND_Data::pool('district'),'presentation:'.$t['id']);SND_Game::persist($a['token'],$s);echo wp_json_encode(array('target'=>$t['id']));
}elseif($op==='mail'){echo wp_json_encode(get_option('snd_http_mail'));
}elseif($op==='settings'){echo wp_json_encode(SND_Settings::all());
}elseif($op==='cleanup'){
    require_once ABSPATH.'wp-admin/includes/user.php';$q=new WP_User_Query(array('search'=>'snd-http-*','search_columns'=>array('user_login')));foreach($q->get_results() as $u){wp_delete_user($u->ID);}update_option('snd_settings',get_option('snd_http_saved_settings',array()),false);delete_option('snd_http_saved_settings');
}else{exit(1);}
