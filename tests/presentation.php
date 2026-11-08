<?php
// Neutral landmark choices, stable IDs, hidden solutions and administrator edits.
$root=getenv('WP_ROOT');if(!$root || !is_file($root.'/wp-load.php')){exit("Set WP_ROOT.\n");}$_SERVER['HTTP_HOST']='snd.test';$_SERVER['REQUEST_METHOD']='GET';$_SERVER['REQUEST_URI']='/';require $root.'/wp-load.php';
if(!in_array(wp_get_environment_type(),array('local','development'),true)){exit("Refusing production.\n");}
require __DIR__.'/legacy-mode.php';
$count=0;$user=get_current_user_id();$labels=require SND_DIR.'data/landmark-labels.php';
function presentation_ok($v,$label){global $count;$count++;if(!$v){throw new RuntimeException($label);}}
try{
    wp_set_current_user(0);$districts=SND_Data::pool('district');
    foreach(array('sr','en','de','ru') as $lang){SND_I18n::force($lang);$all=array();foreach($labels as $name=>$row){$all[]=$row[$lang];presentation_ok(SND_I18n::landmark_quiz_name($name)===$row[$lang],'All neutral names '.$lang.' '.$name);}
        presentation_ok(count(array_unique($all))===30,'Thirty unambiguous labels '.$lang);
        foreach(SND_Data::get()['territories'] as $t){if(empty($t['landmark']['image'])){continue;}$questions=SND_Game::questions($t,SND_Data::pool($t['kind']),$districts,'presentation:'.$t['id']);$q=$questions[3];$translated=SND_I18n::question($q,$t);
            presentation_ok($q['type']==='landmark' && $translated['type']==='landmark','Photo question retained');presentation_ok(array_column($q['options'],'id')===array_column($translated['options'],'id'),'Saved answer IDs stay stable');
            foreach($q['options'] as $i=>$option){presentation_ok($translated['options'][$i]['label']===$labels[$option['label']][$lang],'Neutral option '.$t['id']);}
            presentation_ok(count(array_unique(array_column($translated['options'],'label')))===4,'Distinct visible choices '.$t['id']);presentation_ok($translated['correct_label']===SND_I18n::landmark_name($t['landmark']['name']),'Official name in answer review');
            $c=SND_Game::challenge($t['kind'],'practice');$c['target']=$t;$c['questions']=$questions;$s=array('challenge'=>$c,'stage'=>'quiz','guesses'=>array(),'hint'=>false,'solved'=>false,'answers'=>array(),'question_index'=>3,'revision'=>0,'owner'=>0);
            $out=SND_Game::public_state($s);presentation_ok(!array_intersect(array('correct','correct_label','explanation','image_source'),array_keys($out['question'])),'No solution or official name before answer');
            $s['answers'][3]=array('choice'=>$q['correct'],'correct'=>true,'skipped'=>false);$out=SND_Game::public_state($s);presentation_ok($out['question']['correct_label']===SND_I18n::landmark_name($t['landmark']['name']),'Official name available after answering');
        }
    }
    $data=SND_Data::editable();$index=array_search('rs-00',array_column($data['territories'],'id'));$data['territories'][$index]['landmark']['quiz_name']='<b>Neutralno ime</b>';$data['territories'][$index]['landmark']['translations']['de']['quiz_name']='<b>Neutraler Name</b>';$valid=SND_Data::validate($data);
    presentation_ok(!is_wp_error($valid),'Quiz names survive dataset validation');presentation_ok($valid['territories'][$index]['landmark']['quiz_name']==='Neutralno ime' && $valid['territories'][$index]['landmark']['translations']['de']['quiz_name']==='Neutraler Name','Admin quiz names sanitized in both languages');
    $pages=SND_Site::install();foreach(array('login','account') as $view){if($view==='account'){$id=wp_insert_user(array('user_login'=>'snd-presentation-'.bin2hex(random_bytes(6)),'user_pass'=>bin2hex(random_bytes(18)),'role'=>'subscriber'));wp_set_current_user($id);}$GLOBALS['wp_query']=new WP_Query(array('page_id'=>$pages[$view]));$GLOBALS['wp_the_query']=$GLOBALS['wp_query'];ob_start();SND_Site::header();$html=ob_get_clean();$dom=new DOMDocument();@$dom->loadHTML($html);$x=new DOMXPath($dom);presentation_ok($x->query('//nav/a')->length===4,'Four compact navigation links including game hub and ranking');presentation_ok($x->query('//header/a[last()][contains(@class,"snd-account-link")]/svg')->length===1,'Account is the last header icon');if($view==='account'){require_once ABSPATH.'wp-admin/includes/user.php';wp_delete_user($id);wp_set_current_user(0);}}
    echo 'PASS: '.$count.' neutral-landmark, solution privacy, editable labels and account navigation assertions.'.PHP_EOL;
}finally{SND_I18n::force(null);wp_set_current_user($user);}
