<?php
defined('ABSPATH') || exit;
?><!doctype html>
<html lang="<?php echo esc_attr(SND_I18n::html_lang()); ?>" dir="ltr" data-snd-theme="<?php echo SND_I18n::theme()==='dark'?'dark':'light'; ?>" data-snd-preference="<?php echo esc_attr(SND_I18n::theme()); ?>">
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class('snd-app-body'); ?>>
<?php wp_body_open(); ?>
<div class="snd-app">
<div id="snd-page-loader" class="snd-page-loader" role="status" hidden><?php echo SND_Site::loader(); ?><span class="snd-app-sr-only"><?php echo esc_html(SND_I18n::t('Učitavanje…')); ?></span></div>
<?php ob_start();SND_Site::header();echo SND_I18n::html(ob_get_clean()); ?>
<main id="snd-content" class="snd-app-content snd-view-<?php echo esc_attr(SND_Site::view()); ?>">
<?php ob_start();SND_Site::content();echo SND_I18n::html(ob_get_clean()); ?>
</main>
<footer class="snd-app-footer"><span><?php echo esc_html(SND_Settings::get('app_name')); ?></span><a href="<?php echo esc_url(SND_Site::url('library')); ?>"><?php echo esc_html(SND_I18n::t('Baza znanja')); ?></a></footer>
</div>
<?php wp_footer(); ?>
</body>
</html>
