<?php
defined('ABSPATH') || exit;

final class SND_Rate {
    public static function client_id() {
        $value=$_COOKIE['snd_client']??'';
        if(!is_string($value) || !preg_match('/^[a-f0-9]{32}$/D',$value)){
            $value=bin2hex(random_bytes(16));$_COOKIE['snd_client']=$value;
            if(!headers_sent()){setcookie('snd_client',$value,array('expires'=>time()+30*DAY_IN_SECONDS,'path'=>wp_parse_url(home_url('/'),PHP_URL_PATH)?:'/','secure'=>is_ssl(),'httponly'=>true,'samesite'=>'Lax'));}
        }
        return $value;
    }
    public static function allow($bucket,$maximum,$window,$identity,$ip_maximum) {
        $ip=(string)($_SERVER['REMOTE_ADDR']??'unknown');$period=(int)floor(time()/$window);
        $buckets=array('ip:'.$ip=>$ip_maximum,'actor:'.$identity=>$maximum);
        foreach($buckets as $actor=>$limit){
            $key='snd_rate_'.substr(hash_hmac('sha256',$bucket.':'.$actor.':'.$period,wp_salt('nonce')),0,40);
            $n=(int)get_transient($key);if($n>=$limit){return false;}
            set_transient($key,$n+1,$window+MINUTE_IN_SECONDS);
        }
        return true;
    }
    public static function actor() {return get_current_user_id()?'user:'.get_current_user_id():'guest:'.self::client_id();}
}
