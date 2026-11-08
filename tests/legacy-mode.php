<?php
// Existing tests deliberately exercise the retained, administrator-enabled legacy
// practice mode. Production defaults and the new daily-only mode have separate tests.
if(PHP_SAPI!=='cli' || !in_array(wp_get_environment_type(),array('local','development'),true)){exit(1);}
$snd_legacy_saved=get_option('snd_settings',null);
update_option('snd_settings',array_merge(SND_Settings::all(),array('daily_only'=>false,'practice_enabled'=>true)),false);SND_Settings::reset();
register_shutdown_function(function()use($snd_legacy_saved){if($snd_legacy_saved===null){delete_option('snd_settings');}else{update_option('snd_settings',$snd_legacy_saved,false);}SND_Settings::reset();});
