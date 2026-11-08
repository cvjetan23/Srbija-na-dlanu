<?php
// Run only on an isolated local/development WordPress installation.
$root=getenv('WP_ROOT');if(!$root || !is_file(rtrim($root,'/').'/wp-load.php')){exit("Set WP_ROOT.\n");}
$_SERVER['HTTP_HOST']='snd.test';$_SERVER['REQUEST_METHOD']='GET';$_SERVER['REQUEST_URI']='/';$_SERVER['SERVER_PROTOCOL']='HTTP/1.1';
require rtrim($root,'/').'/wp-load.php';
if(!in_array(wp_get_environment_type(),array('local','development'),true)){exit("Refusing production/staging.\n");}
require __DIR__.'/legacy-mode.php';
$count=0;$tokens=array();$saved_user=get_current_user_id();$saved_cookie=$_COOKIE['snd_client']??null;$saved_ip=$_SERVER['REMOTE_ADDR']??null;
function qa_check($value,$label){global $count;$count++;if(!$value){throw new RuntimeException($label);}}
function qa_request($path,$body){$request=new WP_REST_Request('POST','/srbija-na-dlanu/v1/'.$path);$request->set_header('Content-Type','application/json');$request->set_body(wp_json_encode($body));return rest_do_request($request);}
function qa_game($target,$index){
    global $tokens;$c=SND_Game::challenge($target['kind'],'practice');$c['target']=$target;$c['questions']=SND_Game::questions($target,SND_Data::pool($target['kind']),SND_Data::pool('district'),'qa:'.$target['id']);
    $s=array('challenge'=>$c,'stage'=>'quiz','guesses'=>array(),'hint'=>false,'solved'=>false,'answers'=>array(),'question_index'=>$index,'revision'=>0,'owner'=>0,'game_id'=>bin2hex(random_bytes(16)),'recorded'=>false);
    $token=bin2hex(random_bytes(24));$tokens[]=$token;SND_Game::persist($token,$s);return array('token'=>$token,'state'=>SND_Game::public_state($s));
}
function qa_move($g,$action,$value=null){$r=qa_request('action',array('token'=>$g['token'],'revision'=>$g['state']['revision'],'action'=>$action,'value'=>$value));qa_check($r->get_status()===200,'New action '.$action);return $r->get_data();}
function qa_page($id){$GLOBALS['wp_query']=new WP_Query(array('page_id'=>$id));$GLOBALS['wp_the_query']=$GLOBALS['wp_query'];$GLOBALS['post']=get_post($id);}
try{
    wp_set_current_user(0);
    foreach(array(0=>'0 dana',1=>'1 dan',2=>'2 dana',11=>'11 dana',21=>'21 dan',101=>'101 dan',111=>'111 dana') as $n=>$expected){qa_check(SND_Text::days($n)===$expected,'Day grammar '.$n);}
    $districts=SND_Data::pool('district');$flag_target=null;$photo_target=null;
    foreach(SND_Data::get()['territories'] as $t){
        $questions=SND_Game::questions($t,SND_Data::pool($t['kind']),$districts,'qa:'.$t['id']);
        qa_check(count($questions)===4,'Four questions '.$t['id']);
        qa_check($questions[2]['type']!=='region','Hint region is never graded again '.$t['id']);
        qa_check(count(array_unique(array_column($questions[2]['options'],'label')))===4,'Unique flag/fallback choices '.$t['id']);
        if($questions[2]['type']==='distance'){qa_check(!empty($questions[2]['sources']) && !empty($questions[2]['source']),'Geography replacement has sources '.$t['id']);}
        if($questions[3]['type']!=='landmark'){qa_check(strpos($questions[3]['subtitle'],'Zamensko')!==false,'Final geography replacement is labeled '.$t['id']);}
        foreach($questions as $q){
            if(in_array($q['type'],array('distance','nearby'),true)){
                qa_check(!empty($q['sources']),'Geometry question has explicit sources '.$t['id']);
                if($t['region']==='Kosovo i Metohija'){qa_check(strpos(wp_json_encode($q['sources']),'mre.gov.rs')!==false,'KiM geometry cites MRE '.$t['id']);}
            }
            if($q['type']==='population'){
                $correct=0;foreach($q['options'] as $o){if($o['id']===$q['correct']){$correct=(int)preg_replace('/\D/','',$o['label']);}}
                foreach($q['options'] as $o){$value=(int)preg_replace('/\D/','',$o['label']);qa_check($value>0 && $value/$correct>=.4 && $value/$correct<=2,'Plausible same-scale population choices '.$t['id']);}
            }
        }
        if($t['kind']==='district' && SND_Flags::for_territory($t)){$flag_target=$t;}
        if($t['kind']==='district' && !empty($t['landmark']['image'])){$photo_target=$t;}
    }
    qa_check((bool)$flag_target && (bool)$photo_target,'Fixtures have real verified visual questions');
    $g=qa_game($photo_target,3);$public=$g['state'];
    qa_check(!isset($public['question']['image_source']) && !isset($public['question']['source']) && !isset($public['question']['correct']),'Photo source cannot reveal unanswered question');
    qa_check(!isset($public['map']) && !isset($public['catalog']),'Quiz does not repeat full map/catalog');
    qa_check(!empty($public['question']['credit']) && !empty($public['question']['license']),'Photo attribution visible before answer');
    $g=qa_move($g,'source');qa_check(!empty($g['state']['source_help']) && !empty($g['state']['question']['image_source']),'Viewing source explicitly marks assistance');
    qa_check(!isset($g['state']['question']['correct']),'Source help does not expose option id');
    $internal=SND_Game::load($g['token']);$q=SND_Game::question_for($internal,3);$g=qa_move($g,'answer',$q['correct']);$g=qa_move($g,'finish');
    qa_check($g['state']['review'][3]['source_help_used'],'Assistance survives final review');
    qa_check(!empty($g['state']['review'][3]['correct_image']),'Photo appears in learning review');

    $g=qa_game($flag_target,2);$internal=SND_Game::load($g['token']);$q=SND_Game::question_for($internal,2);
    $wrong=null;foreach($q['options'] as $o){if($o['id']!==$q['correct']){$wrong=$o;break;}}
    $g=qa_move($g,'answer',$wrong['id']);$g=qa_move($g,'finish');$review=$g['state']['review'][2];
    qa_check($review['choice_label']===$wrong['label'] && !$review['answer']['correct'],'Wrong flag choice retained separately');
    qa_check(!empty($review['title']) && !empty($review['correct_image']) && strpos($review['correct_label'],'Zastava mesta')===0,'Review contains question and actual correct flag');
    $restored=qa_request('resume',array('token'=>$g['token']))->get_data();qa_check($restored['state']['review'][2]===$review,'Correct flag survives reload/resume');

    foreach(array(array($photo_target,3),array($flag_target,2)) as $fixture){
        $g=qa_game($fixture[0],$fixture[1]);$before=$g['state']['guesses'];$g=qa_move($g,'media_unavailable');
        qa_check($g['state']['question']['type']==='distance','Failed visual becomes server-authored geography');
        qa_check($g['state']['guesses']===$before && $g['state']['score']===0,'Technical failure does not consume guess or score');
        $internal=SND_Game::load($g['token']);$replacement=SND_Game::question_for($internal,$fixture[1]);$g=qa_move($g,'answer',$replacement['correct']);$g=qa_move($g,'finish');
        qa_check($g['state']['review'][$fixture[1]]['technical_replacement'] && $g['state']['score']===1,'Replacement is scored once and identified in review');
    }
    // Existing unfinished 2.0 questions migrate without resetting attempts/answers.
    $g=qa_game($flag_target,2);$legacy=SND_Game::load($g['token']);
    $legacy_q=array('type'=>'region','title'=>'Širi položaj?','options'=>array(array('id'=>'r1','label'=>$flag_target['region']),array('id'=>'r2','label'=>'Drugo'),array('id'=>'r3','label'=>'Treće'),array('id'=>'r4','label'=>'Četvrto')),'correct'=>'r1','explanation'=>'Urednička podela igre.','source'=>'');
    $legacy['challenge']['questions'][2]=$legacy_q;$legacy['challenge']['ruleset']=2;
    $current=SND_Game::public_state($legacy);qa_check($current['question']['type']==='distance','Unanswered legacy region is replaced');
    $effective=SND_Game::question_for($legacy,2);qa_check(SND_Game::act($legacy,'answer',$effective['correct'])===true,'Migrated choice accepted');
    qa_check(SND_Game::public_state($legacy)['question']['answer']['correct'],'Migrated correct choice stays correct after answer');
    $legacy['question_overrides']=array();$legacy['answers'][2]=array('choice'=>'r1','correct'=>true,'skipped'=>false);
    $old=SND_Game::question_for($legacy,2);qa_check($old['type']==='region' && $old['correct']==='r1' && !empty($old['source']),'Already-answered legacy question retains score with documented source');

    $legacy_geo=SND_Game::nearby_question($photo_target,SND_Data::pool('district'),'qa:legacy-geography');
    unset($legacy_geo['sources']);$legacy_geo['source']='https://www.geoboundaries.org/';
    $legacy['challenge']['target']=$photo_target;$legacy['challenge']['questions'][2]=$legacy_geo;$legacy['answers'][2]=array('choice'=>$legacy_geo['correct'],'correct'=>true,'skipped'=>false);
    $fixed_geo=SND_Game::question_for($legacy,2);
    qa_check(strpos(wp_json_encode($fixed_geo['sources']),'mre.gov.rs')!==false,'Legacy KiM geometry source is corrected too');
    qa_check($fixed_geo['correct']===$legacy_geo['correct'] && $legacy['answers'][2]['correct'],'Legacy citation repair preserves choices and score');

    // School/workplace NAT: independent actors have independent normal quotas.
    $bucket='qa-group-'.bin2hex(random_bytes(5));$_SERVER['REMOTE_ADDR']='192.0.2.77';
    for($actor=0;$actor<30;$actor++){for($turn=0;$turn<12;$turn++){qa_check(SND_Rate::allow($bucket,240,600,'user:'.$actor,12000),'Shared NAT supports group play');}}
    $single='qa-single-'.bin2hex(random_bytes(5));for($i=0;$i<3;$i++){qa_check(SND_Rate::allow($single,3,600,'a',100),'Actor normal quota');}
    qa_check(!SND_Rate::allow($single,3,600,'a',100),'Actor quota enforced');qa_check(SND_Rate::allow($single,3,600,'b',100),'Peer is not blocked by another actor');
    $registration=new ReflectionMethod('SND_Accounts','limit');$registration->setAccessible(true);
    for($i=0;$i<10;$i++){$_COOKIE['snd_client']=bin2hex(random_bytes(16));qa_check($registration->invoke(null,'register','qa-'.bin2hex(random_bytes(8))),'Group registration requests do not share a four-person quota');}

    $pages=SND_Site::install();qa_page($pages['library']);$_GET['okrug']='rs-12';$t=SND_Site::current_district();
    qa_check(strpos(SND_Site::document_title(),$t['name'])!==false && strpos(SND_Site::document_title(),'Srbija na dlanu')!==false,'District title identifies territory and app');
    qa_check(strpos(SND_Site::canonical(),'okrug=rs-12')!==false,'District canonical preserves selected district');
    qa_check(strpos(apply_filters('slim_seo_canonical_url','http://wrong.test/'),'okrug=rs-12')!==false,'Slim SEO canonical uses same district');
    qa_check(apply_filters('slim_seo_open_graph_locale','en_US')==='sr_RS','Open Graph locale is Serbian');
    $graph=SND_Site::schema(array(array('@type'=>'WebSite','@id'=>'http://snd.test/#website','url'=>'http://snd.test/','inLanguage'=>'en'),array('@type'=>'WebPage','inLanguage'=>'en')));
    qa_check($graph[0]['inLanguage']==='sr-Latn' && $graph[1]['inLanguage']==='sr-Latn','Schema language is Serbian');
    qa_check($graph[0]['url']===SND_Site::url() && $graph[1]['url']===SND_Site::canonical(),'Schema has standalone app and correct detail URLs');
    unset($_GET['okrug']);
    wp_enqueue_style('astra-google-fonts','https://fonts.googleapis.com/css?family=Test');
    wp_enqueue_style('qa-elementor','http://snd.test/wp-content/uploads/elementor/css/post-1529.css');
    wp_enqueue_style('snd-app',SND_URL.'assets/app.css');
    wp_enqueue_script('qa-security','http://snd.test/wp-content/plugins/qa-security/form.js');
    SND_Site::trim_theme_assets();
    qa_check(!wp_style_is('astra-google-fonts','enqueued') && !wp_style_is('qa-elementor','enqueued'),'Theme fonts and generated builder CSS removed in standalone app');
    qa_check(wp_style_is('snd-app','enqueued') && wp_script_is('qa-security','enqueued'),'Own assets and unrelated security integration retained');
    $tag='<script src="'.SND_URL.'assets/game.js">const label = "Pogođeno iz 4. pokušaja.";</script>';
    $protected=snd_preserve_asset_tag($tag,'snd-game');qa_check(strpos($protected,'<!--noptimize-->')===0 && strpos($protected,'<!--/noptimize-->')!==false,'Own script is excluded from unsafe optimizer transformation');
    qa_check(snd_preserve_asset_tag($tag,'unrelated')===$tag,'Other site scripts keep original optimization settings');
    if(class_exists('autoptimizeScripts')){
        $instance=(new ReflectionClass('autoptimizeScripts'))->newInstanceWithoutConstructor();
        $hide=new ReflectionMethod('autoptimizeScripts','hide_noptimize');$hide->setAccessible(true);$restore=new ReflectionMethod('autoptimizeScripts','restore_noptimize');$restore->setAccessible(true);
        $masked=$hide->invoke($instance,$protected);qa_check(strpos($masked,'Pogođeno iz')===false,'Actual Autoptimize masks protected script before minification');
        qa_check($restore->invoke($instance,$masked)===$protected,'Actual Autoptimize restores exact script bytes and spaces');
    }
    $_GET['podela']='regioni';ob_start();SND_Site::content();$regions=ob_get_clean();unset($_GET['podela']);
    qa_check(strpos($regions,'urednička podela')!==false && substr_count($regions,'<tr>')===31,'Regional convention is documented for all districts');
}finally{
    wp_set_current_user($saved_user);foreach($tokens as $token){delete_transient('snd_session_'.$token);}
    if($saved_cookie===null){unset($_COOKIE['snd_client']);}else{$_COOKIE['snd_client']=$saved_cookie;}
    if($saved_ip===null){unset($_SERVER['REMOTE_ADDR']);}else{$_SERVER['REMOTE_ADDR']=$saved_ip;}
}
echo 'PASS: '.$count.' QA WordPress regressions; '.get_bloginfo('version').'; PHP '.PHP_VERSION.PHP_EOL;
