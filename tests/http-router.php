<?php
// Start only with PHP's development server and a dedicated WP_ROOT.
$root=getenv('WP_ROOT');$path=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
if(!$root || !is_file($root.'/wp-load.php')){http_response_code(500);exit;}
if($path!=='/' && is_file($root.$path)){return false;}require $root.'/index.php';
