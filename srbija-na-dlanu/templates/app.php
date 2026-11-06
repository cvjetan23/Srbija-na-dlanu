<?php
defined('ABSPATH') || exit;
?><!doctype html>
<html lang="sr-Latn" dir="ltr">
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class('snd-app-body'); ?>>
<?php wp_body_open(); ?>
<div class="snd-app">
<?php SND_Site::header(); ?>
<main id="snd-content" class="snd-app-content snd-view-<?php echo esc_attr(SND_Site::view()); ?>">
<?php SND_Site::content(); ?>
</main>
<footer class="snd-app-footer"><span>Srbija na dlanu</span><a href="<?php echo esc_url(SND_Site::url('library')); ?>">Baza znanja</a></footer>
</div>
<?php wp_footer(); ?>
</body>
</html>
