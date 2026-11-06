<?php
// Run only against an isolated local/development WordPress installation.
$wp_root = getenv('WP_ROOT');
if (!$wp_root || !is_file(rtrim($wp_root, '/\\') . '/wp-load.php')) {
    fwrite(STDERR, "Set WP_ROOT to an isolated WordPress test installation.\n"); exit(1);
}
$_SERVER['HTTP_HOST'] = parse_url(getenv('WP_TEST_URL') ?: 'http://snd.test', PHP_URL_HOST);
$_SERVER['REQUEST_METHOD'] = 'GET'; $_SERVER['REQUEST_URI'] = '/'; $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
require rtrim($wp_root, '/\\') . '/wp-load.php';
if (!in_array(wp_get_environment_type(), array('local', 'development'), true)) {
    fwrite(STDERR, "Refusing to run on a production/staging WordPress site.\n"); exit(1);
}
if (!function_exists('snd_print_late_styles')) {
    fwrite(STDERR, "Activate the current Srbija na dlanu plugin before running this test.\n"); exit(1);
}
$count = 0;
function assert_asset($condition, $message) { global $count; $count++; if (!$condition) { throw new RuntimeException($message); } }
function clear_game_assets() {
    wp_dequeue_style('snd-game'); wp_dequeue_script('snd-game');
    wp_styles()->done = array_diff(wp_styles()->done, array('snd-game'));
}
function page_query($id) {
    $GLOBALS['wp_query'] = new WP_Query(array('page_id' => $id));
    $GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];
    $GLOBALS['post'] = get_post($id);
}

try {
$page = wp_insert_post(array('post_type'=>'page','post_status'=>'publish','post_title'=>'Asset regression test','post_content'=>'Content is stored in Elementor metadata.'));
update_post_meta($page, '_elementor_data', wp_slash(wp_json_encode(array(array('elType'=>'widget','widgetType'=>'text-editor','settings'=>array('editor'=>'[srbija_na_dlanu]'))))));
page_query($page); clear_game_assets();
do_action('wp_enqueue_scripts');
assert_asset(wp_style_is('snd-game','enqueued'), 'CSS must be enqueued early when shortcode exists only in Elementor metadata');
assert_asset(wp_script_is('snd-game','enqueued'), 'JS must be enqueued for the same Elementor page');

clear_game_assets();
$GLOBALS['wp_actions']['wp_head'] = 1;
// Elementor may render and discard shortcode output while collecting its assets.
$discarded = do_shortcode('[srbija_na_dlanu]');
assert_asset(!wp_style_is('snd-game','done'), 'A discarded shortcode render must not mark CSS as printed');
assert_asset(strpos($discarded,'<link') === false, 'Shortcode must not contain stylesheet tags that text widgets can strip');
ob_start(); snd_print_late_styles(); $footer = ob_get_clean();
assert_asset(strpos($footer,'game.css?ver=' . SND_VERSION) !== false, 'Late CSS must be printed by the actual footer hook');
assert_asset(wp_style_is('snd-game','done'), 'CSS marked done only after it is printed');
ob_start(); snd_print_late_styles(); $second = ob_get_clean();
assert_asset($second === '', 'Repeated footer pass must not duplicate CSS');

clear_game_assets();
delete_post_meta($page,'_elementor_data');
wp_update_post(array('ID'=>$page,'post_content'=>'[srbija_na_dlanu]'));
page_query($page); do_action('wp_enqueue_scripts');
assert_asset(wp_style_is('snd-game','enqueued'), 'Ordinary WordPress shortcode page still enqueues CSS');

clear_game_assets();
wp_update_post(array('ID'=>$page,'post_content'=>'An unrelated page.'));
page_query($page); do_action('wp_enqueue_scripts');
assert_asset(!wp_style_is('snd-game','enqueued'), 'Unrelated pages do not receive the game stylesheet');
assert_asset(!wp_script_is('snd-game','enqueued'), 'Unrelated pages do not receive the game script');
} finally { if (!empty($page) && !is_wp_error($page)) { wp_delete_post($page, true); } }
echo 'PASS: '.$count.' asset regressions on WordPress '.get_bloginfo('version').PHP_EOL;
