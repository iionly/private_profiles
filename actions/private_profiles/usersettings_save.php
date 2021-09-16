<?php

$plugin = elgg_get_plugin_from_id('private_profiles');

$current_user = elgg_get_logged_in_user_entity();
$guid = (int) get_input('guid', 0);
if (!$plugin || !$guid || !($user = get_entity($guid))) {
	return elgg_error_response(elgg_echo('plugins:usersettings:save:fail', ['private_profiles']));
}
if (($user->guid != $current_user->guid) && !$current_user->isAdmin()) {
	return elgg_error_response(elgg_echo('plugins:usersettings:save:fail', ['private_profiles']));
}

$params = (array) get_input('params');

$plugin = elgg_get_plugin_from_id('private_profiles');
$plugin_name = $plugin->getManifest()->getName();

$plugin_name = $plugin->getDisplayName();

$result = false;

foreach ($params as $k => $v) {
	$result = $plugin->setUserSetting($k, $v, $user->guid);
	if (!$result) {
		return elgg_error_response(elgg_echo('plugins:usersettings:save:fail', [$plugin_name]));
	}
}

return elgg_ok_response('', elgg_echo('plugins:usersettings:save:ok', [$plugin_name]));
