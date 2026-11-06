<?php
defined('ABSPATH') || exit;

final class SND_Atlas {
    public static function municipality() {
        if(SND_Site::view()!=='library' || ($_GET['podela']??'')==='regioni'){return null;}
        $key=isset($_GET['opstina']) && is_string($_GET['opstina'])?sanitize_key(wp_unslash($_GET['opstina'])):'';
        $pool=SND_Data::pool('municipality');return $pool[$key]??null;
    }
    public static function members($district) {
        return array_filter(SND_Data::pool('municipality'),function($t)use($district){return $t['district_id']===$district['id'];});
    }
    public static function url($territory) {
        $args=$territory['kind']==='district'?array('okrug'=>$territory['id']):array('okrug'=>$territory['district_id'],'opstina'=>$territory['id']);
        return add_query_arg($args,SND_Site::url('library'));
    }
    public static function viewbox($territories) {
        // Built-in geography uses absolute M/L/Z paths. Unknown imported path
        // commands retain the full viewport and are fitted with SVG.getBBox.
        $xs=array();$ys=array();
        foreach($territories as $t){
            $path=$t['map_path'];if(preg_match('/[a-z]/i',str_replace(array('M','L','Z','e','E'),'',$path))){return '0 0 600 800';}
            preg_match_all('/[-+]?(?:\d*\.\d+|\d+\.?\d*)(?:[eE][-+]?\d+)?/',$path,$numbers);
            if(count($numbers[0])%2){return '0 0 600 800';}
            foreach($numbers[0] as $i=>$n){if($i%2){$ys[]=(float)$n;}else{$xs[]=(float)$n;}}
        }
        if(!$xs || !$ys){return '0 0 600 800';}
        $x=min($xs);$y=min($ys);$w=max(1,max($xs)-$x);$h=max(1,max($ys)-$y);$pad=max($w,$h)*.07;
        return implode(' ',array_map(function($n){return number_format($n,3,'.','');},array($x-$pad,$y-$pad,$w+2*$pad,$h+2*$pad)));
    }
    public static function map($district=null,$selected=null) {
        $pool=$district?self::members($district):SND_Data::pool('district');$bounds=$district?array_merge(array($district),array_values($pool)):$pool;
        $id=wp_unique_id('snd-map-');$title=$district?SND_I18n::t('Mapa opština: {name}',array('name'=>SND_I18n::territory_name($district))):SND_I18n::t('Mapa okruga Srbije');
        $help=$district?'Izaberi opštinu na mapi ili u spisku ispod.':'Izaberi okrug na mapi ili u spisku ispod.';
        $html='<section class="snd-map-explorer" data-snd-atlas><div class="snd-map-intro"><span>'.esc_html($district?'Opštine i gradovi':'Okruzi Srbije').'</span><p>'.esc_html($help).'</p></div><div class="snd-map-canvas"><svg class="snd-explorer-map" viewBox="'.esc_attr(self::viewbox($bounds)).'" role="group" aria-labelledby="'.$id.'-title '.$id.'-desc" data-map-fit><title id="'.$id.'-title">'.esc_html($title).'</title><desc id="'.$id.'-desc">'.esc_html($help).'</desc><g data-map-shapes>';
        if($district){$html.='<path class="snd-explorer-outline" d="'.esc_attr($district['map_path']).'" aria-hidden="true"/>';}
        $index=0;
        foreach($pool as $t){
            $name=SND_I18n::territory_name($t);$is_selected=$selected && $t['id']===$selected['id'];$index++;
            $html.='<a class="snd-map-region'.($is_selected?' is-selected':'').'" href="'.esc_url(self::url($t)).'" data-territory="'.esc_attr($t['id']).'" data-name="'.esc_attr($name).'" aria-label="'.esc_attr($name).'"'.($is_selected?' aria-current="location"':'').'><title>'.esc_html($name).'</title><path d="'.esc_attr($t['map_path']).'" class="snd-region-tone-'.($index%4).'"/></a>';
        }
        $html.='</g></svg></div><div class="snd-map-status" aria-live="polite" aria-atomic="true"><span data-map-name>'.esc_html($selected?SND_I18n::territory_name($selected):SND_I18n::t($district?'Opštine i gradovi':'Okruzi Srbije')).'</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M6 18 18 6M6 6h12v12"/></svg></div></section>';
        return $html;
    }
    public static function municipality_detail($t,$district) {
        echo '<article class="snd-district-detail snd-municipality-detail"><nav class="snd-atlas-breadcrumbs" aria-label="Putanja"><a href="'.esc_url(SND_Site::url('library')).'">Baza znanja</a><span aria-hidden="true">/</span><a href="'.esc_url(self::url($district)).'">'.esc_html(SND_I18n::territory_name($district)).'</a></nav><h1>'.esc_html(SND_I18n::territory_name($t)).'</h1><p class="snd-atlas-subtitle">'.esc_html(SND_I18n::territory_name($district)).'</p>'.self::map($district,$t);
        echo '<section class="snd-municipality-outline"><h2>Obris teritorije</h2>'.SND_Site::silhouette($t,'snd-detail-shape').'</section><dl class="snd-facts"><div><dt>Okrug / posebna celina</dt><dd><a href="'.esc_url(self::url($district)).'">'.esc_html(SND_I18n::territory_name($district)).'</a></dd></div><div><dt>Širi položaj</dt><dd>'.esc_html(SND_I18n::region($t['region'])).'</dd></div><div><dt>Stanovništvo</dt><dd>'.(!empty($t['population'])?esc_html(SND_I18n::number($t['population'])).' <small>(Popis '.esc_html($t['year']).')</small>':'Nema uporedivog podatka iz Popisa RZS 2022.').'</dd></div></dl>';
        if($t['region']==='Kosovo i Metohija'){echo '<p class="snd-detail-note">Popis RZS 2022. nije sproveden na teritoriji Kosova i Metohije.</p>';}
        if($t['id']==='m-beograd'){echo '<p class="snd-detail-note">Grad Beograd je posebna administrativna celina. Gradske opštine nisu zasebne teritorije ove igre.</p>';}
        if(!empty($t['landmark']['fact'])){echo '<h2>Jedna zanimljivost</h2><p>'.esc_html(SND_I18n::fact($t)).'</p>';}
        $flag=SND_Flags::for_territory($t);
        if($flag){echo '<h2>'.esc_html(SND_I18n::t('Zastava mesta {place}',array('place'=>SND_I18n::place($flag['name'])))).'</h2><figure class="snd-knowledge-flag"><img src="'.esc_url(SND_Data::image_url($flag['image'])).'" alt="'.esc_attr(SND_I18n::t('Zastava mesta {place}',array('place'=>SND_I18n::place($flag['name'])))).'"><figcaption>'.esc_html($flag['credit'].' · '.$flag['license']).' · <a href="'.esc_url($flag['source']).'" target="_blank" rel="noopener noreferrer">Izvor</a></figcaption></figure>';}
        echo '<h2>Opštine i gradovi ovog okruga</h2>';self::member_list($district,$t);
        echo '<div class="snd-detail-sources"><h2>Izvori</h2><a href="'.esc_url($t['population_source']?:($t['region']==='Kosovo i Metohija'?'https://kro.mre.gov.rs/arcgis/rest/services/IMIS/MINING_PUBLIC/MapServer':'https://www.stat.gov.rs/')).'" target="_blank" rel="noopener noreferrer">Izvor podataka</a> · <a href="'.esc_url($t['region']==='Kosovo i Metohija'?'https://kro.mre.gov.rs/arcgis/rest/services/IMIS/MINING_PUBLIC/MapServer':'https://www.geoboundaries.org/').'" target="_blank" rel="noopener noreferrer">Granice teritorije</a></div><a class="snd-app-button" href="'.esc_url(SND_Site::url('municipality')).'">Igraj opštine i gradove</a></article>';
    }
    public static function member_list($district,$selected=null) {
        echo '<ul class="snd-member-list">';foreach(self::members($district) as $m){$active=$selected && $m['id']===$selected['id'];echo '<li><a data-atlas-item="'.esc_attr($m['id']).'" href="'.esc_url(self::url($m)).'"'.($active?' aria-current="page"':'').'>'.esc_html(SND_I18n::territory_name($m)).'</a></li>';}echo '</ul>';
    }
}
