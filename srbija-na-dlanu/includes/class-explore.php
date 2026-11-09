<?php
defined('ABSPATH') || exit;

/** Offline, sourced catalogues for the three map games. No third-party API in play. */
final class SND_Explore {
    private static $cache;
    private static $approval;
    public static function data(){
        if(self::$cache===null){
            $file=SND_DIR.'data/explore.json';$base=is_file($file)?json_decode(file_get_contents($file),true):array('items'=>array(),'sources'=>array());
            $custom=get_option('snd_explore_catalogue',null);if(is_array($custom) && !empty($custom['items'])){
                $review=array_column($base['items'],null,'id');foreach($custom['items'] as &$t){if(!isset($t['review_status'])){$seed=$review[$t['id']]??null;$same=$seed && $seed['kind']===$t['kind'] && SND_Text::key($seed['name'])===SND_Text::key($t['name']);foreach(array('review_status','verification_source','reviewed_on','review_note') as $key){$t[$key]=$same?($seed[$key]??''):'';}$t['review_status']=$t['review_status']?:'pending';}}unset($t);$base=$custom;
            }
            self::$cache=$base;
        }return self::$cache;
    }
    public static function project($center){
        static $p=null;if($p===null){$seed=json_decode(file_get_contents(SND_DIR.'data/explore.json'),true);$p=$seed['projection'];}
        return array(round(((float)$center[1]*$p['coslat']-$p['xmin'])*$p['scale']+$p['ox'],2),round(($p['ymax']-(float)$center[0])*$p['scale']+$p['oy'],2));
    }
    public static function reset(){self::$cache=null;self::$approval=null;SND_Data::reset_cache();}
    public static function pool($kind){$out=array();foreach(self::data()['items']??array() as $t){if(($t['kind']??'')===$kind && self::reviewed($t) && empty($t['disabled'])){$out[$t['id']]=$t;}}return $out;}
    public static function reviewed($t){return ($t['review_status']??'')==='verified' && !empty($t['verification_source']);}
    public static function approved_id($id){if(self::$approval===null){self::$approval=array();foreach(self::data()['items']??array() as $t){if(self::reviewed($t)){self::$approval[$t['id']]=true;}}}return isset(self::$approval[$id]);}
    public static function safe_decoys($q,$seed){
        if(($q['type']??'')!=='explore_city_river'){return $q;}$pool=self::pool('river');$known=array();foreach($pool as $t){foreach(array_merge(array($t['name']),$t['aliases']??array()) as $name){$known[SND_Text::key($name)]=true;}}
        $used=array_map(function($o){return SND_Text::key($o['label']);},$q['options']);$choices=SND_Game::ordered($pool,$seed);
        foreach($q['options'] as &$o){if($o['id']===$q['correct'] || isset($known[SND_Text::key($o['label'])])){continue;}foreach($choices as $candidate){$key=SND_Text::key($candidate['name']);if(!in_array($key,$used,true)){$o['label']=$candidate['name'];$o['value']=$candidate['name'];$used[]=$key;break;}}}unset($o);return $q;
    }
    public static function targets($kind){return array_filter(self::pool($kind),function($t){return !empty($t['playable']) && !empty($t['photo']['image']);});}
    public static function target_counts(){$out=array();foreach(SND_Kinds::EXPLORE as $kind){$out[$kind]=count(self::targets($kind));}return $out;}
    public static function revision(){return substr(hash('sha256',wp_json_encode(self::data())),0,16);}
    public static function geometry_sources($items){return array(array('label'=>'OpenStreetMap — položaji i tokovi','url'=>'https://www.openstreetmap.org/copyright'),array('label'=>'Planinarski savez Srbije — vrhovi i koordinate','url'=>'https://pss.rs/planine-srbije/'));}
    private static function choice($type,$title,$correct,$others,$seed,$explanation,$vars,$source,$unit=''){
        $others=array_values(array_unique(array_filter($others,function($v)use($correct){return (string)$v!==(string)$correct && $v!=='';})));
        $values=array_merge(array($correct),array_slice(SND_Game::ordered($others,$seed.':decoys'),0,3));$options=array();
        foreach($values as $v){$options[]=array('id'=>substr(hash('sha256',$seed.':'.$v),0,16),'label'=>(string)$v,'value'=>$v,'unit'=>$unit);}
        return array('type'=>'explore_'.$type,'title'=>$title,'title_key'=>$title,'explanation'=>$explanation,'explanation_key'=>$explanation,'vars'=>$vars,'options'=>SND_Game::ordered($options,$seed.':order'),'correct'=>$options[0]['id'],'source'=>$source);
    }
    private static function numeric($type,$title,$number,$unit,$seed,$source,$detail=''){
        $step=$number>=10000?1000:($number>=1000?100:($number>=100?10:5));$n=max($step,(int)round($number/$step)*$step);$others=array();
        foreach(array(.4,.65,1.5,2,2.6) as $factor){$v=max($step,(int)round($n*$factor/$step)*$step);if($v!==$n){$others[]=$v;}}
        $q=self::choice($type,$title,$n,$others,$seed,'Podatak u izvoru: {value} {unit}. {detail}',array('value'=>$number,'unit'=>$unit,'detail'=>$detail),$source,$unit);$q['approximate']=true;return $q;
    }
    public static function questions($t,$pool,$seed){
        $kind=$t['kind'];$source=$t['facts_source']??$t['source'];$q=array();
        if($kind==='city'){
            $q[]=self::choice('district','Kojem okrugu pripada ovo naselje?',$t['district_name'],array_column(SND_Data::pool('district'),'name'),$seed.':district','Naselje {name} pripada celini: {answer}.',array('name'=>$t['name']),$t['source']);
            if(!empty($t['population'])){$q[]=self::numeric('population','Koliko približno stanovnika ima naselje?',$t['population'],'',$seed.':population',$t['population_source'],'Popis 2022 · stanovništvo naselja, ne cele opštine.');}
            else{$q[]=self::choice('region','U kom širem delu Srbije se nalazi ovo naselje?',$t['region'],array('Vojvodina','Zapadna Srbija','Centralna Srbija','Istočna Srbija','Južna Srbija','Kosovo i Metohija'),$seed.':region','Širi položaj: {answer}.',array(),$t['source']);}
            if(!empty($t['river_name'])){$q[]=self::choice('city_river','Koja reka prolazi kroz ovo naselje ili pored njega?',$t['river_name'],array_column(self::pool('river'),'name'),$seed.':river','Uz naselje {name} je reka {answer}.',array('name'=>$t['name']),$t['river_source']??$t['source']);}
            else{$q[]=self::orientation($t,$pool,$seed.':orientation');}
        }elseif($kind==='river'){
            $q[]=self::choice('mouth',!empty($t['forms_river'])?'Koja reka nastaje na sastavu ove reke?':'Gde se neposredno uliva ova reka?',$t['mouth'],array_column(self::targets('river'),'mouth'),$seed.':mouth',!empty($t['forms_river'])?'Na sastavu reke {name} nastaje {answer}.':'Reka {name}: neposredni recipijent je {answer}.',array('name'=>$t['name']),$source);
            $q[]=self::choice('basin','Kojem morskom slivu pripada ova reka?',$t['sea'],array('Crno more','Jadransko more','Egejsko more','Baltičko more'),$seed.':sea','Sliv reke {name} završava u moru: {answer}.',array('name'=>$t['name']),$t['basin_source']??$source);
            $q[]=self::numeric('length','Kolika je približna ukupna dužina reke?',$t['length_km'],'km',$seed.':length',$source,'Cela reka, uključujući deo van Srbije.');
        }else{
            $q[]=self::choice('peak','Koji je referentni vrh planine u pregledu PSS za Srbiju?',$t['peak'],array_column(self::pool('mountain'),'peak'),$seed.':peak','Planina {name}: vrh {answer}. {scope}',array('name'=>$t['name'],'scope'=>$t['peak_scope']??''),$source);
            $q[]=self::numeric('height','Kolika je približna visina tog vrha?',$t['height'],'m',$seed.':height',$source,$t['peak_scope']??'');
            if(!empty($t['area_km2']) && !empty($t['area_scope']) && !empty($t['area_source'])){$q[]=self::numeric('area','Kolika je približna površina navedenog planinskog obuhvata?',$t['area_km2'],'km²',$seed.':area',$t['area_source'],$t['area_scope']);}
            else{$q[]=self::choice('region','U kom širem delu Srbije je označena planina?',$t['region'],array('Vojvodina','Zapadna Srbija','Centralna Srbija','Istočna Srbija','Južna Srbija','Kosovo i Metohija'),$seed.':region','Označeni položaj planine {name}: {answer}.',array('name'=>$t['name']),$t['source']);}
        }
        $q[]=self::photo_question($t,$pool,$seed.':photo');return $q;
    }
    public static function orientation($t,$pool,$seed){
        $others=array_filter($pool,function($v)use($t){return $v['id']!==$t['id'] && SND_Game::distance($t['center'],$v['center'])>10;});$order=SND_Game::ordered($others,$seed);$anchor=$order[0];$direction=SND_Game::direction($t['center'],$anchor['center']);
        return self::choice('direction','U kom smeru je mesto „{anchor}“ u odnosu na označeni položaj?',ucfirst($direction['name']),array('Sever','Severoistok','Istok','Jugoistok','Jug','Jugozapad','Zapad','Severozapad'),$seed,'Od označenog položaja ka mestu {anchor}: {answer}.',array('anchor'=>$anchor['name']),$t['source']);
    }
    public static function photo_question($t,$pool,$seed){
        $others=array_filter($pool,function($x)use($t){return $x['id']!==$t['id'] && !empty($x['photo']['image']) && $x['photo']['image']!==$t['photo']['image'];});$used=array_merge(array($t),array_slice(SND_Game::ordered($others,$seed),0,3));$used=SND_Game::ordered($used,$seed.':options');$options=array();$attribution=array();$letters=array('A','B','C','D');$correct='';
        foreach($used as $i=>$v){$id=substr(hash('sha256',$seed.':'.$v['id']),0,16);if($v['id']===$t['id']){$correct=$id;}$options[]=array('id'=>$id,'label'=>'Fotografija '.$letters[$i],'image'=>$v['photo']['image']);$attribution[]=array('label'=>'Fotografija '.$letters[$i],'credit'=>$v['photo']['credit'],'license'=>$v['photo']['license'],'license_url'=>$v['photo']['license_url']??'','source'=>$v['photo']['source_url']);}
        $title=array('city'=>'Koja fotografija prikazuje ovo naselje?','river'=>'Koja fotografija prikazuje ovu reku?','mountain'=>'Koja fotografija prikazuje ovu planinu?')[$t['kind']];
        return array('type'=>'explore_photo','title'=>$title,'title_key'=>$title,'correct'=>$correct,'correct_label'=>$t['name'],'options'=>$options,'explanation'=>'Fotografija prikazuje: {name}.','explanation_key'=>'Fotografija prikazuje: {name}.','vars'=>array('name'=>$t['name']),'source'=>$t['photo']['source_url'],'image_source'=>$t['photo']['source_url'],'attribution'=>$attribution);
    }
    public static function translate($q,$t){
        if($q['type']==='explore_peak'){$q['title_key']='Koji je referentni vrh planine u pregledu PSS za Srbiju?';}
        $vars=$q['vars']??array();if(isset($vars['name'])){$vars['name']=self::name($t);}foreach($vars as $key=>$value){$vars[$key]=is_numeric($value)?SND_I18n::number($value):SND_I18n::text($value);}
        $answer='';foreach($q['options'] as &$o){
            if(!empty($o['image']) && strpos($o['image'],'photos/')===0){$o['image']=SND_Data::image_url($o['image']);}
            if(isset($o['value']) && is_numeric($o['value'])){$o['label']=SND_I18n::t('Oko {value}',array('value'=>SND_I18n::number($o['value']))).($o['unit']?' '.$o['unit']:'');}
            else{$o['label']=SND_I18n::text($o['label']);}
            if($o['id']===$q['correct']){$answer=$o['label'];}unset($o['value'],$o['unit']);
        }unset($o);$vars['answer']=$answer;$q['title']=SND_I18n::t($q['title_key'],$vars);$q['explanation']=SND_I18n::t($q['explanation_key'],$vars);if(!empty($q['attribution'])){foreach($q['attribution'] as &$credit){$credit['label']=SND_I18n::text($credit['label']);}unset($credit);}
        unset($q['vars'],$q['title_key'],$q['explanation_key'],$q['approximate']);return $q;
    }
    public static function name($t){return $t['labels'][SND_I18n::lang()]??$t['name'];}
    public static function display_name($t){$name=self::name($t);return !empty($t['display_name'])?$name.' · '.SND_I18n::t($t['district_name']):$name;}
    public static function map_state($s){
        $t=$s['challenge']['target'];$out=array('kind'=>$t['kind'],'target'=>array('point'=>$t['point'],'path'=>$t['map_path']),'attempts'=>array());
        foreach($s['guesses'] as $i=>$g){$row=$s['challenge']['catalog'][$g['id']];$out['attempts'][]=array('number'=>$i+1,'name'=>self::display_name($row),'point'=>$row['point'],'path'=>$row['map_path'],'correct'=>$g['correct']);}
        if($t['kind']==='river'){$out['context']=self::data()['river_context']??array();}
        return $out;
    }
    public static function validate($data){
        if(!is_array($data) || (int)($data['schema']??0)!==1 || !is_array($data['items']??null) || count($data['items'])>5000){return new WP_Error('snd_explore_schema','Očekivan je katalog sa schema: 1 i najviše 5000 stavki.');}
        $shipped=json_decode(file_get_contents(SND_DIR.'data/explore.json'),true);$known=array_column($shipped['items'],null,'id');
        $out=array('schema'=>1,'items'=>array(),'sources'=>array(),'river_context'=>array(),'projection'=>self::data()['projection']??array());$ids=array();$ready=array_fill_keys(SND_Kinds::EXPLORE,0);
        foreach($data['items'] as $t){
            if(!is_array($t) || !is_string($t['id']??null) || !preg_match('/^(city|river|mountain)-[a-z0-9-]{1,60}$/D',$t['id']) || isset($ids[$t['id']]) || !in_array($t['kind']??'',SND_Kinds::EXPLORE,true)){return new WP_Error('snd_explore_id','Neispravan ili ponovljen identifikator.');}
            $r=array('id'=>$t['id'],'kind'=>$t['kind']);$ids[$t['id']]=true;
            foreach(array('name','display_name','region','district_id','district_name','peak','peak_scope','mouth','sea','area_scope','river_name','forms_river') as $k){$r[$k]=sanitize_text_field($t[$k]??'');}if(!$r['name']){return new WP_Error('snd_explore_name','Nedostaje naziv.');}
            foreach(array('center'=>array(40,48,17,25),'point'=>array(-50,650,-50,850)) as $k=>$bounds){$a=$t[$k]??null;if(!is_array($a)||count($a)!==2||!is_numeric($a[0])||!is_numeric($a[1])||$a[0]<$bounds[0]||$a[0]>$bounds[1]||$a[1]<$bounds[2]||$a[1]>$bounds[3]){return new WP_Error('snd_explore_point','Neispravne koordinate: '.$r['id']);}$r[$k]=array((float)$a[0],(float)$a[1]);}
            foreach(array('path','map_path') as $k){$v=$t[$k]??'';if(!is_string($v)||strlen($v)>150000||($v!==''&&!preg_match('/^[MmLlHhVvCcSsQqTtAaZz0-9eE.,\s+\-]+$/D',$v))){return new WP_Error('snd_explore_shape','Neispravna SVG putanja.');}$r[$k]=$v;}
            foreach(array('source','facts_source','population_source','basin_source','area_source','river_source') as $k){$r[$k]=esc_url_raw($t[$k]??'',array('https'));}
            if(!$r['source']){return new WP_Error('snd_explore_source','Svaka stavka zahteva HTTPS izvor.');}
            foreach(array('population','year','height','length_km','area_km2') as $k){$r[$k]=max(0,(float)($t[$k]??0));}
            $r['labels']=array();foreach(array('sr','en','de','ru') as $lang){$r['labels'][$lang]=sanitize_text_field($t['labels'][$lang]??$r['name']);}
            $r['aliases']=array_map('sanitize_text_field',array_slice((array)($t['aliases']??array()),0,20));$r['photo']=array();
            if(!empty($t['photo'])){$p=$t['photo'];if((!preg_match('~^photos/v4/[a-f0-9]{16,64}\.(jpg|png|webp)$~D',$p['image']??'') && !(is_string($p['image']??null) && strpos($p['image'],'https://')===0 && esc_url_raw($p['image'],array('https')))) || empty($p['credit'])||empty($p['license'])||empty($p['source_url'])){return new WP_Error('snd_explore_photo','Fotografija zahteva lokalnu putanju, autora, licencu i izvor.');}foreach(array('image','credit','license') as $k){$r['photo'][$k]=sanitize_text_field($p[$k]);}$r['photo']['source_url']=esc_url_raw($p['source_url'],array('https'));$r['photo']['license_url']=esc_url_raw($p['license_url']??'',array('https'));}
            if(!isset($t['review_status']) && isset($known[$r['id']]) && $known[$r['id']]['kind']===$r['kind'] && SND_Text::key($known[$r['id']]['name'])===SND_Text::key($r['name'])){foreach(array('review_status','verification_source','reviewed_on','review_note') as $key){$t[$key]=$known[$r['id']][$key]??'';}}
            $r['review_status']=($t['review_status']??'pending')==='verified'?'verified':'pending';$r['verification_source']=esc_url_raw($t['verification_source']??'',array('https'));$r['reviewed_on']=sanitize_text_field($t['reviewed_on']??'');$r['review_note']=sanitize_text_field($t['review_note']??'');
            if($r['review_status']==='verified' && !$r['verification_source']){return new WP_Error('snd_explore_review','Potvrđen naziv zahteva nezavisan HTTPS izvor.');}
            $r['disabled']=!empty($t['disabled']);$r['playable']=!empty($t['playable'])&&!$r['disabled'] && self::reviewed($r);
            if($r['playable']){if(empty($r['photo'])||($r['kind']==='river'&&(!$r['mouth']||!$r['sea']||!$r['length_km']))||($r['kind']==='mountain'&&(!$r['peak']||!$r['height']))){return new WP_Error('snd_explore_ready','Dnevni zadatak nema sve podatke: '.$r['id']);}$ready[$r['kind']]++;}
            $out['items'][]=$r;
        }
        foreach($ready as $kind=>$n){if($n<4){return new WP_Error('snd_explore_pool','Potrebna su najmanje četiri kompletna dnevna zadatka: '.$kind);}}
        foreach(array_slice((array)($data['river_context']??array()),0,120) as $path){if(is_string($path)&&strlen($path)<150000&&preg_match('/^[MmLlHhVvCcSsQqTtAaZz0-9eE.,\s+\-]+$/D',$path)){$out['river_context'][]=$path;}}
        foreach(array_slice((array)($data['sources']??array()),0,20) as $source){$out['sources'][]=array('label'=>sanitize_text_field($source['label']??''),'url'=>esc_url_raw($source['url']??'',array('https')),'license'=>sanitize_text_field($source['license']??''));}return $out;
    }
}
