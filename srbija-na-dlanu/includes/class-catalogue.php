<?php
defined('ABSPATH') || exit;

/** Public knowledge directory; game answers and the directory share reviewed pools. */
final class SND_Catalogue {
    public static function url($kind,$args=array()){
        if($kind!=='district'){$args=array_merge(array('tip'=>$kind),$args);}return add_query_arg($args,SND_Site::url('library'));
    }
    public static function tabs(){
        echo '<nav class="snd-explore-library-tabs" aria-label="Vrsta geografskih podataka">';$selected=$_GET['tip']??'district';
        foreach(SND_Kinds::labels() as $kind=>$label){echo '<a class="snd-app-button" data-catalogue-kind="'.esc_attr($kind).'" href="'.esc_url(self::url($kind)).'"'.($selected===$kind || ($kind==='district' && $selected==='')?' aria-current="page"':'').'>'.esc_html($label).'</a>';}
        echo '</nav>';
    }
    private static function query($key){return is_string($_GET[$key]??null)?sanitize_text_field(wp_unslash($_GET[$key])):'';}
    private static function filter($pool,$query,$region,$sea,$parent){
        return array_filter($pool,function($t)use($query,$region,$sea,$parent){
            if($region && $t['region']!==$region || $sea && ($t['sea']??'')!==$sea || $parent && ($t['parent_id']??'')!==$parent){return false;}
            $text=$t['name'].' '.implode(' ',$t['aliases']??array()).' '.implode(' ',$t['labels']??array());return !$query || strpos(SND_Text::key($text),SND_Text::key($query))!==false;
        });
    }
    private static function sorted($pool,$sort){
        uasort($pool,function($a,$b)use($sort){if($sort!=='name'){$n=(float)($b[$sort]??0)<=>(float)($a[$sort]??0);if($n){return $n;}}return strnatcasecmp(SND_Text::key($a['name']),SND_Text::key($b['name']));});return $pool;
    }
    public static function library(){
        $kind=$_GET['tip']??'';if(!in_array($kind,array('municipality','city','river','mountain'),true)){return false;}
        $all=SND_Data::pool($kind);$query=self::query('pretraga');$region=self::query('oblast');$sea=$kind==='river'?self::query('sliv'):'';$parent=$kind==='municipality'?self::query('grad'):'';
        $sort=self::query('sort');$numeric=$kind==='river'?'length_km':($kind==='mountain'?'height':'population');if(!in_array($sort,array('name',$numeric),true)){$sort='name';}
        $pool=self::sorted(self::filter($all,$query,$region,$sea,$parent),$sort);$current=SND_Explore_Admin::current();self::tabs();
        echo '<section class="snd-library-head"><h1>'.esc_html($current?SND_Explore::display_name($current):SND_I18n::t(SND_Kinds::labels()[$kind])).'</h1><p>Izaberi na mapi ili pronađi naziv u tabeli.</p></section>';
        if(!$current){self::controls($kind,$all,$query,$region,$sea,$parent,$sort,$numeric);}
        echo '<div class="snd-catalogue-map-section"><svg class="snd-explore-library-map'.($kind==='municipality'?' is-municipality':'').'" viewBox="0 0 600 800" role="group" aria-label="Mapa geografskih objekata"><g class="snd-explore-country">';foreach(SND_Data::pool('district') as $d){echo '<path d="'.esc_attr($d['map_path']).'"/>';}echo '</g>';
        $map=$pool;if($current){unset($map[$current['id']]);$map[$current['id']]=$current;}
        foreach($map as $t){$selected=$current && $current['id']===$t['id'];$url=$kind==='municipality'?SND_Atlas::url($t):self::url($kind,array('objekat'=>$t['id']));$name=$kind==='municipality'?SND_I18n::territory_name($t):SND_Explore::display_name($t);
            echo '<a href="'.esc_url($url).'" aria-label="'.esc_attr($name).'"'.($selected?' aria-current="page"':'').'><title>'.esc_html($name).'</title>'.(in_array($kind,array('river','municipality'),true)?'<path d="'.esc_attr($t['map_path']).'"/>':'<circle cx="'.esc_attr($t['point'][0]).'" cy="'.esc_attr($t['point'][1]).'" r="'.($selected?'8':'3.5').'"/>').'</a>';
        }
        echo '</svg></div>';
        if($current){echo '<p class="snd-catalogue-back"><a href="'.esc_url(self::url($kind)).'">← Nazad na spisak</a></p>';SND_Explore_Admin::detail($current);self::controls($kind,$all,$query,$region,$sea,$parent,$sort,$numeric);}
        self::table($kind,$pool);
        if($kind==='municipality'){echo '<aside class="snd-catalogue-source-note"><h2>Kako se računa broj opština?</h2><p>Spisak prati 197 opština, gradskih opština i gradova iz registra RZS. To nisu 197 samostalnih opština: Srbija ima 174 jedinice lokalne samouprave. Beograd, Niš, Požarevac, Vranje i Užice ovde su prikazani kroz 28 gradskih opština, bez ponovnog dodavanja celih gradova.</p><p>Uključeni su Kosovo i Metohija. Crtica znači da uporediv podatak iz Popisa 2022. nije dostupan.</p><a href="https://stat.gov.rs/sr-latn/oblasti/registar-prostornih-jedinica-i-gis/administrativno-teritorijalna-podela-i-nstj-nivoi-1-2-3/" target="_blank" rel="noopener noreferrer">Zvanična podela RZS ↗</a></aside>';}
        else{echo '<p class="snd-explore-table-note">U javnom katalogu su potvrđeni nazivi. Nedostajući podaci označeni su crticom; dnevni zadaci imaju kompletna pitanja i fotografije.</p>';}
        return true;
    }
    private static function controls($kind,$all,$query,$region,$sea,$parent,$sort,$numeric){
        echo '<form class="snd-catalogue-controls" method="get" action="'.esc_url(SND_Site::url('library')).'"><input type="hidden" name="tip" value="'.esc_attr($kind).'"><input type="hidden" name="lang" value="'.esc_attr(SND_I18n::lang()).'"><label class="snd-catalogue-search">Pronađi naziv<input name="pretraga" type="search" placeholder="Pronađi naziv…" value="'.esc_attr($query).'"></label>';
        if($kind==='river'){$values=array_values(array_unique(array_filter(array_column($all,'sea'))));sort($values);self::select('sliv','Morski sliv','Svi slivovi',$values,$sea);}
        elseif($kind==='municipality'){$values=array('m-beograd'=>'Beograd','m-nis'=>'Niš','m-pozarevac'=>'Požarevac','m-vranje'=>'Vranje','m-uzice'=>'Užice');self::select('grad','Gradske opštine','Sve opštine i gradovi',$values,$parent,true);}
        else{$values=array_values(array_unique(array_column($all,'region')));sort($values);self::select('oblast','Položaj','Sve oblasti',$values,$region);}
        self::select('sort','Poređaj po','',array('name'=>'Naziv A–Ž',$numeric=>$kind==='river'?'Dužina — najduže prvo':($kind==='mountain'?'Visina — najviše prvo':'Stanovništvo — najveće prvo')),$sort,true);
        echo '<button type="submit" class="snd-app-button">Prikaži</button>';if($query||$region||$sea||$parent){echo '<a class="snd-catalogue-reset" href="'.esc_url(self::url($kind)).'">Poništi filtere</a>';}echo '</form>';
    }
    private static function select($name,$label,$empty,$values,$selected,$assoc=false){
        echo '<label>'.esc_html($label).'<select name="'.esc_attr($name).'">';if($empty){echo '<option value="">'.esc_html($empty).'</option>';}
        foreach($values as $key=>$text){$value=$assoc?$key:$text;echo '<option value="'.esc_attr($value).'"'.selected($selected,$value,false).'>'.esc_html(SND_I18n::text($text)).'</option>';}echo '</select></label>';
    }
    private static function table($kind,$pool){
        $second=in_array($kind,array('city','municipality'),true)?'Okrug':($kind==='river'?'Uliva se u':'Referentni vrh');$third=in_array($kind,array('city','municipality'),true)?'Stanovništvo · 2022':($kind==='river'?'Dužina · ceo tok':'Visina');
        echo '<section class="snd-catalogue-list" id="snd-catalogue-list"><h2>'.esc_html(array('municipality'=>'Opštine, gradske opštine i gradovi','city'=>'Spisak naselja','river'=>'Spisak reka','mountain'=>'Spisak planina')[$kind]).'</h2><div class="snd-explore-table-wrap"><table class="snd-explore-table"><thead><tr><th scope="col">Naziv</th><th scope="col">'.esc_html($second).'</th><th scope="col">'.esc_html($third).'</th></tr></thead><tbody>';
        foreach($pool as $t){$url=$kind==='municipality'?SND_Atlas::url($t):self::url($kind,array('objekat'=>$t['id']));$district=SND_Data::pool('district')[$t['district_id']]??null;$second=in_array($kind,array('city','municipality'),true)?($district?SND_I18n::territory_name($district):$t['district_name']):($kind==='river'?($t['mouth']??''):($t['peak']??''));$n=in_array($kind,array('city','municipality'),true)?($t['population']??0):($kind==='river'?($t['length_km']??0):($t['height']??0));
            echo '<tr data-catalogue-item="'.esc_attr($t['id']).'"><th scope="row"><a href="'.esc_url($url).'">'.esc_html($kind==='municipality'?SND_I18n::territory_name($t):SND_Explore::display_name($t)).'</a></th><td>'.esc_html(SND_I18n::text($second?:'—')).'</td><td>'.($n?esc_html(SND_I18n::number($n).($kind==='river'?' km':($kind==='mountain'?' m':''))):'—').'</td></tr>';
        }
        if(!$pool){echo '<tr><td colspan="3" class="snd-catalogue-empty" role="status">Nema rezultata za izabrane filtere.</td></tr>';}echo '</tbody></table></div></section>';
    }
    public static function featured(){
        echo '<section class="snd-library-featured" aria-label="Istraži prirodu Srbije">';
        $groups=array('river'=>array('Reke Srbije','Istraži tok, ušće i sliv reke.',array('Dunav','Sava','Drina','Velika Morava','Tisa','Ibar')),'mountain'=>array('Planine Srbije','Pronađi planinu, njen vrh i visinu.',array('Kopaonik','Zlatibor','Tara i Zvijezda','Stara planina','Fruška gora','Šar planina')));
        foreach($groups as $kind=>$group){echo '<article><h2>'.esc_html($group[0]).'</h2><p>'.esc_html($group[1]).'</p><ul>';$pool=SND_Explore::pool($kind);foreach($group[2] as $name){foreach($pool as $t){if($t['name']===$name){echo '<li><a href="'.esc_url(self::url($kind,array('objekat'=>$t['id']))).'">'.esc_html(SND_Explore::name($t)).'</a></li>';break;}}}echo '</ul><a class="snd-app-button" href="'.esc_url(self::url($kind)).'">'.($kind==='river'?'Sve reke':'Sve planine').'<span aria-hidden="true">→</span></a></article>';}
        echo '</section>';
    }
}
