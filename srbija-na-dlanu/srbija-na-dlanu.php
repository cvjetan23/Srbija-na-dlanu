<?php
/**
 * Plugin Name: Srbija na dlanu
 * Description: Dnevna geografska igra i vežbanje za okruge, opštine i gradove. Shortcode: [srbija_na_dlanu]
 * Version: 1.0.0
 * Requires at least: 6.2
 * Requires PHP: 7.4
 * Author: Srbija na dlanu
 * License: GPL-2.0-or-later
 * Text Domain: srbija-na-dlanu
 */

defined('ABSPATH') || exit;
define('SND_VERSION', '1.0.0');
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
add_action('wp_enqueue_scripts', function () {
    global $post;
    if (is_singular() && $post && has_shortcode($post->post_content, 'srbija_na_dlanu')) {
        wp_enqueue_style('snd-game', SND_URL . 'assets/game.css', array(), SND_VERSION);
        wp_enqueue_script('snd-game', SND_URL . 'assets/game.js', array(), SND_VERSION, true);
    }
});
add_shortcode('srbija_na_dlanu', function () {
    wp_enqueue_style('snd-game', SND_URL . 'assets/game.css', array(), SND_VERSION);
    wp_enqueue_script('snd-game', SND_URL . 'assets/game.js', array(), SND_VERSION, true);
    $id = wp_unique_id('snd-');
    $late_style = '';
    // Builders may evaluate shortcodes after wp_head, outside the post content.
    if (did_action('wp_head') && !wp_style_is('snd-game', 'done')) { ob_start(); wp_print_styles(array('snd-game')); $late_style = ob_get_clean(); }
    return $late_style . '<div id="' . esc_attr($id) . '" class="snd-game" lang="sr-Latn" data-api="' . esc_url(rest_url('srbija-na-dlanu/v1/')) . '" data-assets="' . esc_url(SND_URL . 'assets/') . '"><div class="snd-loading" role="status">Učitavamo tvoje putovanje…</div><noscript>Za igranje uključi JavaScript u pregledaču.</noscript></div>';
});

register_activation_hook(__FILE__, function () {
    if (!wp_next_scheduled('snd_cleanup')) {
        wp_schedule_event(time() + HOUR_IN_SECONDS, 'daily', 'snd_cleanup');
    }
});
register_deactivation_hook(__FILE__, function () { wp_clear_scheduled_hook('snd_cleanup'); });
add_action('snd_cleanup', array('SND_Game', 'cleanup'));
