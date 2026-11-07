<?php
// Integration checks only on isolated WordPress.
$root=getenv('WP_ROOT');if(!$root || !is_file($root.'/wp-load.php')){exit("Set WP_ROOT.\n");}
$_SERVER['HTTP_HOST']='snd.test';$_SERVER['REQUEST_METHOD']='GET';$_SERVER['REQUEST_URI']='/';require $root.'/wp-load.php';
if(!in_array(wp_get_environment_type(),array('local','development'),true)){exit("Refusing production.\n");}
$saved=get_option('snd_settings',array());$n=0;
function palette_ok($condition,$label){global $n;$n++;if(!$condition){throw new RuntimeException($label);}}
try{
 $old=array('primary_color'=>'#355d3d','dark_accent'=>'#a5c797','dark_button_color'=>'#35513d','rounds'=>array('flag'),'max_guesses'=>9);
 update_option('snd_settings',$old,false);SND_Settings::reset();
 palette_ok(SND_Settings::get('primary_color')==='#6f7c45' && SND_Settings::get('dark_accent')==='#eef0e5','Stock colors migrate to main-site palette');
 palette_ok(SND_Settings::get('rounds')===array('flag') && SND_Settings::get('max_guesses')===9,'Palette migration preserves game settings');
 foreach(SND_Settings::color_fields() as $key=>$label){
  $input=SND_Settings::defaults();$input[$key]='#123456';$out=SND_Settings::validate($input);
  palette_ok($out[$key]==='#123456','Valid custom color '.$key);
  $input[$key]='red;}body{display:none}';palette_ok(SND_Settings::validate($input)[$key]===SND_Settings::defaults()[$key],'CSS injection rejected '.$key);
 }
 $out=SND_Settings::validate(array_merge(SND_Settings::defaults(),$old));update_option('snd_settings',$out,false);SND_Settings::reset();
 palette_ok(SND_Settings::get('primary_color')==='#355d3d','Deliberate choice of former stock color survives save');
 palette_ok(SND_Settings::foreground('#6f7c45')==='#ffffff','Brand olive keeps AA white button text');
 palette_ok(SND_Settings::foreground('#ffd447')==='#101810','Bright custom button gets readable dark text');
 echo 'PASS: '.$n.' palette migration, validation and contrast assertions.'.PHP_EOL;
}finally{update_option('snd_settings',$saved,false);SND_Settings::reset();}
