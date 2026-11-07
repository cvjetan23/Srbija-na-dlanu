<?php
$_SERVER['HTTP_HOST'] = parse_url(getenv('WP_TEST_URL') ?: 'http://snd.test', PHP_URL_HOST); $_SERVER['REQUEST_METHOD'] = 'GET'; $_SERVER['REQUEST_URI'] = '/'; $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$wp_root=getenv('WP_ROOT');
if(!$wp_root || !is_file(rtrim($wp_root,'/').'/wp-load.php')){fwrite(STDERR,"Set WP_ROOT to an isolated test WordPress.\n");exit(1);}
require rtrim($wp_root,'/').'/wp-load.php';
if(!in_array(wp_get_environment_type(),array('local','development'),true)){fwrite(STDERR,"Refusing to run on production/staging.\n");exit(1);} 
require_once ABSPATH . 'wp-admin/includes/plugin.php';
activate_plugin('srbija-na-dlanu/srbija-na-dlanu.php');
if (!class_exists('SND_Game')) { require WP_PLUGIN_DIR . '/srbija-na-dlanu/srbija-na-dlanu.php'; }
$assertions = 0;
function check($condition, $message) { global $assertions; $assertions++; if (!$condition) { throw new RuntimeException($message); } }
function api($path, $body = null) {
    $r = new WP_REST_Request($body === null ? 'GET' : 'POST', '/srbija-na-dlanu/v1/' . $path);
    if ($body !== null) { $r->set_header('Content-Type', 'application/json'); $r->set_body(wp_json_encode($body)); }
    return rest_do_request($r);
}
function move($game, $action, $value = null) { return api('action', array('token' => $game['token'], 'revision' => $game['state']['revision'], 'action' => $action, 'value' => $value)); }

check(is_plugin_active('srbija-na-dlanu/srbija-na-dlanu.php'), 'Installed plugin active');
check((bool) wp_next_scheduled('snd_cleanup'), 'Native WP cleanup scheduled');
check(SND_URL === plugins_url('srbija-na-dlanu/'), 'Correct installed asset URL');
$data = SND_Data::get();
$valid = SND_Data::validate(SND_Data::editable());
check(!is_wp_error($valid), is_wp_error($valid) ? $valid->get_error_message() : 'Seed schema');
check(count(SND_Data::pool('district')) === 30, '30 district-level territories');
check(count(SND_Data::pool('municipality')) === 190, '190 municipalities/cities');
$kim = array_filter($data['territories'], function ($t) { return $t['region'] === 'Kosovo i Metohija'; });
check(count($kim) === 34, '5 districts + 29 municipalities in Kosovo and Metohija');
foreach ($kim as $t) check($t['population'] === 0, 'No fabricated census counts');
check($data['territories']['m-uzice']['population'] === 69997, 'Population of whole city Uzice, not urban municipality');
check($data['territories']['m-nis']['population'] === 249501, 'Whole Nis population');
check($data['territories']['m-pozarevac']['population'] === 68648, 'Whole Pozarevac population');
check($data['territories']['m-vranje']['population'] === 74381, 'Whole Vranje population');
$total = 0;
foreach (SND_Data::pool('district') as $district) {
    $population = 0;
    foreach (SND_Data::pool('municipality') as $municipality) { if ($municipality['district_id'] === $district['id']) { $population += $municipality['population']; } }
    check($population === $district['population'], 'Municipalities sum to official district population: ' . $district['id']);
    $total += $population;
}
check($total === 6647003, 'Whole 2022 census population reconciles without duplicate city municipalities');
foreach ($data['territories'] as $t) { if (!empty($t['landmark']['image'])) { check(is_file(SND_DIR . 'assets/' . $t['landmark']['image']), 'Packaged photo exists: ' . $t['id']); } }
check(SND_Game::direction(array(44,20),array(45,20))['name'] === 'sever', 'Direction from guess to target');
check(SND_Game::direction(array(44,20),array(44,21))['name'] === 'istok', 'East bearing');
check(SND_Game::distance(array(44,20),array(44,20)) === 0.0, 'Zero distance');
check(abs(SND_Game::distance(array(44,20),array(45,20))-111.195) < .01, 'Great-circle distance');
foreach ($data['territories'] as $t) {
    $questions = SND_Game::questions($t, SND_Data::pool($t['kind']), SND_Data::pool('district'), 'test:' . $t['id']);
    check(count($questions) === 4, 'Four questions for ' . $t['id']);
    foreach ($questions as $q) {
        check(count($q['options']) === 4, 'Four choices for ' . $t['id'] . ':' . $q['type']);
        check(count(array_unique(array_column($q['options'], 'label'))) === 4, 'Distinct choices');
        check(count(array_filter($q['options'], function ($o) use ($q) { return $o['id'] === $q['correct']; })) === 1, 'Exactly one correct answer');
    }
}
$evil = SND_Data::editable(); $evil['territories'][0]['path'] = '"/><script>alert(1)</script>';
check(is_wp_error(SND_Data::validate($evil)), 'Reject SVG/HTML injection');
$evil = SND_Data::editable(); $evil['territories'][0]['center'] = array(99,20);
check(is_wp_error(SND_Data::validate($evil)), 'Reject invalid center');
$evil = SND_Data::editable(); $evil['territories'][1]['id'] = $evil['territories'][0]['id'];
check(is_wp_error(SND_Data::validate($evil)), 'Reject duplicate IDs');

$bootstrap = api('bootstrap'); check($bootstrap->get_status() === 200, 'Bootstrap endpoint');
check($bootstrap->get_data()['counts']['district'] === 30, 'Public coverage');
check($bootstrap->get_headers()['Cache-Control'] === 'no-store, no-cache, must-revalidate, max-age=0', 'No cache for dynamic game');
check(api('start', array('kind'=>'bad','mode'=>'daily'))->get_status() === 400, 'Reject unknown mode');
check(api('resume', array('token'=>'../../bad'))->get_status() === 404, 'Reject malformed token');

$game = api('start', array('kind'=>'district','mode'=>'daily'))->get_data();
$other = api('start', array('kind'=>'district','mode'=>'daily'))->get_data();
check($game['state']['shape'] === $other['state']['shape'], 'Same daily outline for everyone');
check(!isset(get_transient('snd_session_' . $game['token'])['challenge']), 'Daily session reuses shared challenge instead of duplicating geometry');
check(!isset($game['state']['territory'], $game['state']['question']), 'No initial answer or future question');
check(move($game,'hint')->get_status() === 409, 'No early hint');
check(move($game,'answer','fake')->get_status() === 409, 'Cannot jump to quiz');
check(move($game,'guess','not-an-id')->get_status() === 400, 'Reject invalid territory without spending turn');
$internal = SND_Game::load($game['token']); $targetId = $internal['challenge']['target']['id'];
$wrong = array_values(array_filter($game['state']['catalog'], function ($t) use ($targetId) { return $t['id'] !== $targetId; }));
$previous = $game;
$game = move($game,'guess',$wrong[0]['id'])->get_data();
check(count($game['state']['guesses']) === 1, 'One guess consumed');
check(move($previous,'guess',$wrong[1]['id'])->get_status() === 409, 'Reject stale/concurrent state');
check(move($game,'guess',$wrong[0]['id'])->get_status() === 400, 'Reject repeated guess');
for ($i=1; $i<3; $i++) $game = move($game,'guess',$wrong[$i]['id'])->get_data();
$game = move($game,'hint')->get_data(); check($game['state']['hint'] && !empty($game['state']['hint_text']), 'Hint after third miss');
$game = move($game,'guess',$targetId)->get_data();
check($game['state']['solved'] && $game['state']['stage'] === 'reveal', 'Correct guess reveals answer');
check($game['state']['territory']['id'] === $targetId, 'Correct revealed name');
$game = move($game,'next')->get_data();
check($game['state']['stage'] === 'quiz' && !isset($game['state']['question']['correct']), 'Hide correct quiz choice before answer');
for ($i=0; $i<4; $i++) {
    $internal = SND_Game::load($game['token']); $correct = $internal['challenge']['questions'][$i]['correct'];
    check(move($game,'answer','fake')->get_status() === 400, 'Reject forged choice');
    $game = move($game,'answer',$correct)->get_data();
    check($game['state']['question']['answer']['correct'] === true, 'Score on server');
    check(move($game,'answer',$correct)->get_status() === 409, 'No repeated answer');
    $game = move($game,'next')->get_data();
}
check($game['state']['stage'] === 'complete' && $game['state']['score'] === 4, 'Complete round-trip');
check(count($game['state']['review']) === 4, 'Learning review');
check(api('resume',array('token'=>$game['token']))->get_data()['state'] === $game['state'], 'Resume completed daily game');
check(move($game,'guess',$targetId)->get_status() === 409, 'Completed game locked');

$practice = api('start',array('kind'=>'municipality','mode'=>'practice'))->get_data();
$internal = SND_Game::load($practice['token']); $targetId = $internal['challenge']['target']['id'];
$wrong = array_values(array_filter($practice['state']['catalog'],function($t)use($targetId){return $t['id'] !== $targetId;}));
for($i=0;$i<6;$i++) $practice = move($practice,'guess',$wrong[$i]['id'])->get_data();
check($practice['state']['stage']==='reveal' && !$practice['state']['solved'], 'Six misses reveal territory');
$practice = move($practice,'next')->get_data();
$practice = move($practice,'skip')->get_data();
check($practice['state']['question']['answer']['skipped'], 'Skipping teaches correct answer');
$practice = move($practice,'finish')->get_data();
check($practice['state']['stage']==='complete' && $practice['state']['score']===0, 'Finish with skipped questions');

$daily = SND_Game::challenge('district','daily');
$overrides = get_option('snd_overrides',array());
update_option('snd_overrides',array($daily['target']['id']=>array('name'=>'Temporary test rename')),false); SND_Data::reset_cache();
check(SND_Game::challenge('district','daily') === $daily, 'Daily content frozen through admin edits');
update_option('snd_overrides',$overrides,false); SND_Data::reset_cache();
$expired = SND_Game::load($other['token']); $expired['challenge']['date']='2020-01-01';
set_transient('snd_session_'.$other['token'],$expired,3600);
check(api('resume',array('token'=>$other['token']))->get_status()===410, 'New day invalidates previous daily session');

wp_set_current_user(0);
check(!current_user_can('manage_options'),'Visitor cannot edit content');
$shortcode = do_shortcode('[srbija_na_dlanu]');
check(strpos($shortcode,'data-api=') !== false && strpos($shortcode,'snd-game') !== false,'WordPress shortcode mounts game');
check(wp_script_is('snd-game','enqueued') && wp_style_is('snd-game','enqueued'),'Scoped assets enqueued');
echo 'PASS: '.$assertions.' assertions; WordPress '.get_bloginfo('version').'; PHP '.PHP_VERSION.'; database '.$GLOBALS['wpdb']->db_version().'; 220 playable territories plus the legacy city record.' . PHP_EOL;
