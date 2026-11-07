<?php
// Native WP route, geography and preference checks; isolated installations only.
$root=getenv('WP_ROOT');if(!$root || !is_file($root.'/wp-load.php')){exit("Set WP_ROOT.\n");}
$_SERVER['HTTP_HOST']='snd.test';$_SERVER['REQUEST_METHOD']='GET';$_SERVER['REQUEST_URI']='/';$_SERVER['SERVER_PROTOCOL']='HTTP/1.1';require $root.'/wp-load.php';
if(!in_array(wp_get_environment_type(),array('local','development'),true)){exit("Refusing production/staging.\n");}
$count=0;$get=$_GET;$cookies=$_COOKIE;$saved=get_option('snd_settings',null);$permalinks=get_option('permalink_structure');
function atlas_ok($v,$label){global $count;$count++;if(!$v){throw new RuntimeException($label);}}
function atlas_html(){ob_start();SND_Site::content();return SND_I18n::html(ob_get_clean());}
function atlas_dom($html){$dom=new DOMDocument();@$dom->loadHTML('<?xml encoding="UTF-8"><div>'.$html.'</div>');return new DOMXPath($dom);}
try{
    $pages=SND_Site::install();$GLOBALS['wp_query']=new WP_Query(array('page_id'=>$pages['library']));$GLOBALS['wp_the_query']=$GLOBALS['wp_query'];
    update_option('snd_settings',SND_Settings::defaults(),false);SND_Settings::reset();$districts=SND_Data::pool('district');$municipalities=SND_Data::pool('municipality');
    foreach(array('sr','en','de','ru') as $lang){SND_I18n::force($lang);$_GET=array();$html=atlas_html();$x=atlas_dom($html);
        atlas_ok($x->query('//a[@data-territory]')->length===30,'Country has 30 interactive districts '.$lang);
        atlas_ok($x->query('//a[contains(@class,"snd-district-card")]')->length===30,'District list retained '.$lang);
        atlas_ok(strpos($html,'snd-map-explorer')<strpos($html,'snd-district-grid'),'Map comes before list '.$lang);
        atlas_ok(strpos($html,'geoBoundaries')===false,'No implementation details in map flow');
        foreach($districts as $district){$_GET=array('okrug'=>$district['id']);$html=atlas_html();$x=atlas_dom($html);$members=SND_Atlas::members($district);
            atlas_ok($x->query('//a[@data-territory]')->length===count($members),'District map has exactly its municipalities '.$district['id']);
            atlas_ok($x->query('//table[contains(@class,"snd-member-table")]/tbody/tr/th/a')->length===count($members),'Municipality list remains clickable '.$district['id']);
            foreach($members as $m){$row=$x->query('//a[@data-atlas-item="'.$m['id'].'"]/ancestor::tr')->item(0);$cells=$x->query('./td',$row);
                atlas_ok(trim($cells->item(0)->textContent)===(!empty($m['population'])?SND_I18n::number($m['population']):SND_I18n::t('Nema podatka')),'Table population matches source '.$m['id'].' '.$lang);
                atlas_ok(trim($cells->item(1)->textContent)===(!empty($m['population']) && !empty($m['year'])?(string)$m['year']:'—'),'Table census year matches source '.$m['id'].' '.$lang);
            }
            $expected=array_keys($members);$ids=array();foreach($x->query('//a[@data-territory]') as $a){$id=$a->getAttribute('data-territory');$ids[]=$id;parse_str(parse_url(html_entity_decode($a->getAttribute('href')),PHP_URL_QUERY),$args);atlas_ok(isset($members[$id]) && $args['okrug']===$district['id'] && $args['opstina']===$id && $args['lang']===$lang,'Map link keeps true parent and locale');}
            sort($ids);sort($expected);atlas_ok($ids===$expected,'No unrelated municipality on district map');
            $box=array_map('floatval',explode(' ',SND_Atlas::viewbox(array_merge(array($district),array_values($members)))));atlas_ok(count($box)===4 && $box[2]>0 && $box[3]>0 && $box[2]*$box[3]<600*800,'District map fitted on server');
        }
        foreach($municipalities as $m){$_GET=array('opstina'=>$m['id'],'okrug'=>'rs-99');$html=atlas_html();$x=atlas_dom($html);$district=$districts[$m['district_id']];
            atlas_ok(SND_Atlas::municipality()['id']===$m['id'] && SND_Site::current_district()['id']===$district['id'],'Municipality derives real parent');
            atlas_ok($x->query('//a[@data-territory and @aria-current="location"]')->length===1,'One highlighted municipality');
            atlas_ok($x->query('//svg[contains(@class,"snd-detail-shape")]/path')->length===1,'Own outline shown');
            atlas_ok($x->query('//a[@data-atlas-item and @aria-current="page"]')->length===1,'Current municipality identified in list');
            atlas_ok(strpos(SND_Site::canonical(),'opstina='.$m['id'])!==false && strpos(SND_Site::canonical(),'okrug='.$district['id'])!==false && strpos(SND_Site::canonical(),'rs-99')===false,'Canonical resolves municipality and correct district');
            atlas_ok(strpos(SND_Site::document_title(),SND_I18n::territory_name($m))!==false,'Municipality metadata title');
            $graph=SND_Site::schema(array(array('@type'=>'BreadcrumbList')));$crumbs=$graph[0]['itemListElement'];atlas_ok(count($crumbs)===4 && $crumbs[2]['item']===SND_Atlas::url($district) && $crumbs[3]['item']===SND_Atlas::url($m),'Correct country/district/municipality breadcrumbs');
            if($m['region']==='Kosovo i Metohija'){atlas_ok(!$m['population'] && strpos($html,'2022')!==false,'KiM has no invented census');}
        }
        $_GET=array('opstina'=>'m-cacak','mode'=>'practice','date'=>'2026-11-06','key'=>'private-key','token'=>'private-token');ob_start();SND_Site::header();$header=SND_I18n::html(ob_get_clean());$x=atlas_dom($header);
        atlas_ok($x->query('//a[@data-language]')->length===4,'Four named language choices');atlas_ok($x->query('//button[@data-snd-theme-choice]')->length===3,'Sun moon and auto controls');
        foreach($x->query('//a[@data-language]') as $a){parse_str(parse_url(html_entity_decode($a->getAttribute('href')),PHP_URL_QUERY),$args);atlas_ok($args['lang']===$a->getAttribute('data-language') && $args['opstina']==='m-cacak' && $args['mode']==='practice' && !isset($args['key'],$args['token']),'Language switch preserves context without auth secrets');}
        atlas_ok($x->query('//details[contains(@class,"snd-language-picker")]/summary')->length===1,'Native accessible language disclosure');
    }
    SND_I18n::force('sr');$_GET=array('opstina'=>'<script>bad</script>');atlas_ok(SND_Atlas::municipality()===null,'Invalid municipality rejected');$_GET=array('opstina'=>array('m-cacak'));atlas_ok(SND_Atlas::municipality()===null,'Array selector rejected');
    $_GET=array('snd_theme'=>'dark');atlas_ok(SND_I18n::theme()==='dark','Explicit theme resolves before paint');$_GET=array();$_COOKIE['snd_theme']='light';atlas_ok(SND_I18n::theme()==='light','Cookie preference');$s=SND_Settings::defaults();$s['theme_switch']=false;$s['default_theme']='dark';update_option('snd_settings',$s,false);SND_Settings::reset();atlas_ok(SND_I18n::theme()==='dark','Admin forced theme respected');
    $s['theme_switch']=true;$s['languages']=array('sr','de');update_option('snd_settings',$s,false);SND_Settings::reset();ob_start();SND_Site::header();$x=atlas_dom(ob_get_clean());atlas_ok($x->query('//a[@data-language]')->length===2,'Enabled languages respected');
    $GLOBALS['wp_rewrite']->set_permalink_structure('');$_GET=array('opstina'=>'m-cacak');parse_str(parse_url(SND_Site::preference_url(),PHP_URL_QUERY),$args);atlas_ok(isset($args['page_id'],$args['okrug'],$args['opstina'],$args['lang']),'Default permalinks retained');
    atlas_ok(SND_Atlas::viewbox(array(array('map_path'=>'m0 0 c10 10 20 20 30 30z')))==='0 0 600 800','Imported curved/relative path uses client fit fallback');
    echo 'PASS: '.$count.' atlas and preference assertions; 30 districts, 174 municipalities and four languages.'.PHP_EOL;
}finally{$_GET=$get;$_COOKIE=$cookies;SND_I18n::force(null);if($saved===null){delete_option('snd_settings');}else{update_option('snd_settings',$saved,false);}SND_Settings::reset();$GLOBALS['wp_rewrite']->set_permalink_structure($permalinks);}
