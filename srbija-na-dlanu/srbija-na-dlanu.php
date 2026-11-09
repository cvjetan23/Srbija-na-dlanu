<?php
/**
 * Plugin Name: Srbija na dlanu
 * Description: Dnevna geografska igra i vežbanje za okruge, opštine i gradove. Shortcode: [srbija_na_dlanu]
 * Version: 4.1.0
 * Requires at least: 6.2
 * Requires PHP: 7.4
 * Author: Srbija na dlanu
 * License: GPL-2.0-or-later
 * Text Domain: srbija-na-dlanu
 */

defined('ABSPATH') || exit;
define('SND_VERSION', '4.1.0');
define('SND_DIR', plugin_dir_path(__FILE__));
define('SND_URL', plugin_dir_url(__FILE__));

require_once SND_DIR . 'includes/class-text.php';
require_once SND_DIR . 'includes/class-lock.php';
require_once SND_DIR . 'includes/class-rate.php';
require_once SND_DIR . 'includes/class-kinds.php';
require_once SND_DIR . 'includes/class-explore.php';
require_once SND_DIR . 'includes/class-explore-admin.php';
require_once SND_DIR . 'includes/class-catalogue.php';
require_once SND_DIR . 'includes/class-data.php';
require_once SND_DIR . 'includes/class-settings.php';
require_once SND_DIR . 'includes/class-scoring.php';
require_once SND_DIR . 'includes/class-ranking.php';
require_once SND_DIR . 'includes/class-i18n.php';
require_once SND_DIR . 'includes/class-flags.php';
require_once SND_DIR . 'includes/class-game.php';
require_once SND_DIR . 'includes/class-progress.php';
require_once SND_DIR . 'includes/class-accounts.php';
require_once SND_DIR . 'includes/class-site.php';
require_once SND_DIR . 'includes/class-atlas.php';
require_once SND_DIR . 'includes/class-api.php';
require_once SND_DIR . 'includes/class-admin.php';
require_once SND_DIR . 'includes/class-analytics.php';
require_once SND_DIR . 'includes/class-control.php';

add_action('rest_api_init', array('SND_API', 'register'));
add_action('admin_menu', array('SND_Admin', 'menu'));
add_action('admin_post_snd_save', array('SND_Admin', 'save'));
add_action('admin_post_snd_import', array('SND_Admin', 'import'));
add_action('admin_post_snd_export', array('SND_Admin', 'export'));
add_action('admin_post_snd_save_flag', array('SND_Admin', 'save_flag'));
add_action('admin_post_snd_settings', array('SND_Admin', 'settings'));
add_action('admin_enqueue_scripts', array('SND_Admin', 'assets'));
add_action('admin_init',array('SND_Site','maybe_install'));
add_filter('template_include',array('SND_Site','template'),99);
add_action('template_redirect',array('SND_Site','prepare'),0);
add_action('wp_enqueue_scripts',array('SND_Site','assets'),5);
add_action('wp_enqueue_scripts',array('SND_Site','trim_theme_assets'),PHP_INT_MAX);
add_action('wp_print_styles',array('SND_Site','trim_theme_assets'),0);
add_action('wp_print_scripts',array('SND_Site','trim_theme_assets'),0);
add_action('wp_print_footer_scripts',array('SND_Site','trim_theme_assets'),0);
add_filter('pre_get_document_title',array('SND_Site','document_title'),PHP_INT_MAX);
add_filter('slim_seo_meta_title',array('SND_Site','document_title'),PHP_INT_MAX);
add_filter('slim_seo_meta_description',array('SND_Site','description'),PHP_INT_MAX);
add_filter('slim_seo_canonical_url',array('SND_Site','canonical_filter'),PHP_INT_MAX);
add_filter('slim_seo_open_graph_title',array('SND_Site','document_title'),PHP_INT_MAX);
add_filter('slim_seo_open_graph_description',array('SND_Site','description'),PHP_INT_MAX);
add_filter('slim_seo_open_graph_url',array('SND_Site','canonical_filter'),PHP_INT_MAX);
add_filter('slim_seo_open_graph_locale',array('SND_Site','locale_filter'),PHP_INT_MAX);
add_filter('slim_seo_open_graph_site_name',array('SND_Site','brand_filter'),PHP_INT_MAX);
add_filter('slim_seo_schema_graph',array('SND_Site','schema'),PHP_INT_MAX);
add_action('wp_head',array('SND_Site','metadata'),3);
add_filter('show_admin_bar',function($show){return SND_Site::view()?false:$show;});
add_action('wp_ajax_snd_session',array('SND_Progress','ajax_session'));
add_action('wp_ajax_nopriv_snd_session',array('SND_Progress','ajax_session'));
add_action('wp_ajax_snd_game',array('SND_API','ajax'));
add_action('wp_ajax_nopriv_snd_game',array('SND_API','ajax'));
function snd_enqueue_game_assets() {
    SND_I18n::enqueue();
    wp_enqueue_style('snd-ranking',SND_URL.'assets/ranking.css',array(),SND_VERSION);
    wp_enqueue_style('snd-game', SND_URL . 'assets/game.css', array('snd-ranking'), SND_VERSION);
    wp_enqueue_script('snd-game', SND_URL . 'assets/game.js', array('snd-i18n'), SND_VERSION, true);
}

function snd_enqueue_page_assets() {
    if (!is_singular() || SND_Site::view()) { return; }
    $post = get_post(get_queried_object_id());
    if (!$post) { return; }
    $has_game = has_shortcode($post->post_content, 'srbija_na_dlanu');
    if (!$has_game) {
        // Elementor keeps text and shortcode widgets outside post_content.
        $elementor_data = get_post_meta($post->ID, '_elementor_data', true);
        if (is_array($elementor_data)) { $elementor_data = wp_json_encode($elementor_data); }
        $has_game = is_string($elementor_data) && has_shortcode($elementor_data, 'srbija_na_dlanu');
    }
    if ($has_game) { snd_enqueue_game_assets(); }
}
add_action('wp_enqueue_scripts', 'snd_enqueue_page_assets', 5);

function snd_print_late_styles() {
    // Print late CSS outside shortcode output: builders can discard pre-renders.
    if (wp_style_is('snd-game', 'enqueued') && !wp_style_is('snd-game', 'done')) {
        wp_print_styles(array('snd-game'));
    }
}
add_action('wp_footer', 'snd_print_late_styles', 5);

add_shortcode('srbija_na_dlanu',function($atts){return SND_Site::game_root(shortcode_atts(array('kind'=>''),$atts,'srbija_na_dlanu'));});

register_activation_hook(__FILE__, function () {
    SND_Site::install();
    if (!wp_next_scheduled('snd_cleanup')) {
        wp_schedule_event(time() + HOUR_IN_SECONDS, 'daily', 'snd_cleanup');
    }
});
register_deactivation_hook(__FILE__, function () { wp_clear_scheduled_hook('snd_cleanup'); wp_clear_scheduled_hook('snd_analytics_backfill'); });
add_action('snd_cleanup', array('SND_Game', 'cleanup'));


// Autoptimize recognizes data-noptimize before rewriting template literals.
// Keep our tested JavaScript and small styles intact without changing the site.
function snd_preserve_asset_tag($tag,$handle) {
    if(!in_array($handle,array('snd-game','snd-app','snd-library','snd-i18n','snd-ui','snd-themes','snd-atlas','snd-motion','snd-ranking'),true)){return $tag;}
    return '<!--noptimize-->'.preg_replace('/<(script|link)\b/','<$1 data-noptimize="1"',$tag,1).'<!--/noptimize-->';
}
add_filter('script_loader_tag','snd_preserve_asset_tag',10,2);
add_filter('style_loader_tag','snd_preserve_asset_tag',10,2);

add_action('admin_menu',array('SND_Control','menu'),20);
add_action('admin_enqueue_scripts',array('SND_Control','assets'));
add_action('admin_post_snd_control_save',array('SND_Control','save'));
add_action('admin_post_snd_translation_save',array('SND_Control','save_translations'));
add_action('admin_post_snd_analytics_export',array('SND_Control','export'));
add_action('admin_post_snd_analytics_clear',array('SND_Control','clear'));
add_action('admin_post_snd_ranking_export',array('SND_Ranking','export'));
add_action('template_redirect',array('SND_I18n','prepare'),-1);
add_action('wp_head',array('SND_I18n','head'),0);
add_filter('rest_post_dispatch',array('SND_I18n','rest'),20,3);
add_filter('rest_post_dispatch',array('SND_Analytics','rest_errors'),30,3);
add_action('snd_cleanup',array('SND_Analytics','clean'));
add_action('snd_cleanup',array('SND_Ranking','clean'));
add_action('snd_analytics_backfill',array('SND_Analytics','backfill'));
add_action('deleted_user',array('SND_Analytics','delete_user'));
add_action('deleted_user',array('SND_Ranking','delete_user'));
add_filter('wp_privacy_personal_data_exporters',array('SND_Analytics','privacy_exporters'));
add_filter('wp_privacy_personal_data_erasers',array('SND_Analytics','privacy_erasers'));
add_action('snd_player_registered',function($id){SND_Analytics::event('registration',array('view'=>'login'),$id);});
add_action('wp_login',function($login,$user){if(SND_Site::view()==='login'){SND_Analytics::event('login',array('view'=>'login'),$user->ID);}},10,2);
