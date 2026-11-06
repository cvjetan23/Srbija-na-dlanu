<?php
/**
 * Plugin Name: Srbija na dlanu
 * Description: Dnevna geografska igra i vežbanje za okruge, opštine i gradove. Shortcode: [srbija_na_dlanu]
 * Version: 1.0.1
 * Requires at least: 6.2
 * Requires PHP: 7.4
 * Author: Srbija na dlanu
 * License: GPL-2.0-or-later
 * Text Domain: srbija-na-dlanu
 */

defined('ABSPATH') || exit;
define('SND_VERSION', '1.0.1');
define('SND_DIR', plugin_dir_path(__FILE__));
define('SND_URL', plugin_dir_url(__FILE__));

require_once SND_DIR . 'includes/class-data.php';
require_once SND_DIR . 'includes/class-game.php';
require_once SND_DIR . 'includes/class-api.php';
require_once SND_DIR . 'includes/class-admin.php';

add_action('rest_api_init', array('SND_API', 'register'));
add_action('admin_menu', array('SND_Admin', 'menu'));
add_action('admin_post_snd_save', array('SND_Admin', 'save'));
add_action('admin_post_snd_import', array('SND_Admin', 'import'));
add_action('admin_post_snd_export', array('SND_Admin', 'export'));
add_action('admin_enqueue_scripts', array('SND_Admin', 'assets'));
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

add_shortcode('srbija_na_dlanu', function () {
    snd_enqueue_game_assets();
    $id = wp_unique_id('snd-');
    return '<div id="' . esc_attr($id) . '" class="snd-game" lang="sr-Latn" data-api="' . esc_url(rest_url('srbija-na-dlanu/v1/')) . '" data-assets="' . esc_url(SND_URL . 'assets/') . '"><div class="snd-loading" role="status">Učitavamo tvoje putovanje…</div><noscript>Za igranje uključi JavaScript u pregledaču.</noscript></div>';
});

register_activation_hook(__FILE__, function () {
    if (!wp_next_scheduled('snd_cleanup')) {
        wp_schedule_event(time() + HOUR_IN_SECONDS, 'daily', 'snd_cleanup');
    }
});
register_deactivation_hook(__FILE__, function () { wp_clear_scheduled_hook('snd_cleanup'); });
add_action('snd_cleanup', array('SND_Game', 'cleanup'));
