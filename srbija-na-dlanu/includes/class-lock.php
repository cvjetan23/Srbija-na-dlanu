<?php
defined('ABSPATH') || exit;

final class SND_Lock {
    // WP options have a unique name. INSERT IGNORE avoids add_option's upsert
    // when two requests pass its cached existence check simultaneously.
    public static function acquire($key) {
        global $wpdb;
        $old=$wpdb->get_var($wpdb->prepare("SELECT option_value FROM {$wpdb->options} WHERE option_name=%s",$key));
        if($old!==null && (int)$old<time()-60){
            $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name=%s AND option_value=%s",$key,$old));
        }
        $lease=time().':'.bin2hex(random_bytes(12));
        $inserted=$wpdb->query($wpdb->prepare("INSERT IGNORE INTO {$wpdb->options} (option_name,option_value,autoload) VALUES (%s,%s,'no')",$key,$lease));
        wp_cache_delete($key,'options');wp_cache_delete('notoptions','options');
        return $inserted===1?$lease:false;
    }
    public static function release($key,$lease) {
        global $wpdb;
        // An expired request must never release a newer request's lease.
        $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name=%s AND option_value=%s",$key,$lease));
        wp_cache_delete($key,'options');wp_cache_delete('notoptions','options');
    }
}
