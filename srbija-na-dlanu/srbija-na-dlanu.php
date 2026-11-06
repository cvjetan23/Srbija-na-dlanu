<?php
/**
 * Plugin Name: Srbija na dlanu
 * Description: Dnevna geografska igra i vežbanje za okruge, opštine i gradove. Shortcode: [srbija_na_dlanu]
 * Version: 2.0.0
 * Requires at least: 6.2
 * Requires PHP: 7.4
 * Author: Srbija na dlanu
 * License: GPL-2.0-or-later
 * Text Domain: srbija-na-dlanu
 */

defined('ABSPATH') || exit;
define('SND_VERSION', '2.0.0');
define('SND_DIR', plugin_dir_path(__FILE__));
define('SND_URL', plugin_dir_url(__FILE__));

require_once SND_DIR . 'includes/class-text.php';
require_once SND_DIR . 'includes/class-lock.php';
require_once SND_DIR . 'includes/class-data.php';
require_once SND_DIR . 'includes/class-flags.php';
require_once SND_DIR . 'includes/class-game.php';
require_once SND_DIR . 'includes/class-progress.php';
require_once SND_DIR . 'includes/class-accounts.php';
require_once SND_DIR . 'includes/class-site.php';
require_once SND_DIR . 'includes/class-api.php';
require_once SND_DIR . 'includes/class-admin.php';

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
add_action('wp_enqueue_scripts',array('SND_Site','trim_theme_assets'),999);
add_filter('show_admin_bar',function($show){return SND_Site::view()?false:$show;});
add_action('wp_ajax_snd_session',array('SND_Progress','ajax_session'));
add_action('wp_ajax_nopriv_snd_session',array('SND_Progress','ajax_session'));
function snd_enqueue_game_assets() {
    wp_enqueue_style('snd-game', SND_URL . 'assets/game.css', array(), SND_VERSION);
    wp_enqueue_script('snd-game', SND_URL . 'assets/game.js', array(), SND_VERSION, true);
}

function snd_enqueue_page_assets() {
    if (!is_singular()) { return; }
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
register_deactivation_hook(__FILE__, function () { wp_clear_scheduled_hook('snd_cleanup'); });
add_action('snd_cleanup', array('SND_Game', 'cleanup'));
