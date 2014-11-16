<?php

$current_user = elgg_get_logged_in_user_entity();

$guid = (int) get_input('guid', 0);
if (!$guid || !($user = get_entity($guid))) {
	forward();
}
if (($user->guid != $current_user->guid) && !$current_user->isAdmin()) {
	forward();
}

$params = get_input('params');

$plugin = elgg_get_plugin_from_id('private_profiles');
$plugin_name = $plugin->getManifest()->getName();

foreach ($params as $k => $v) {
	$result = $plugin->setUserSetting($k, $v, $user->guid);

	if (!$result) {
		register_error(elgg_echo('private_profiles:usersettings:save:error', array($plugin_name)));
		forward(REFERER);
	}
}

system_message(elgg_echo('private_profiles:usersettings:save:success', array($plugin_name)));
forward(REFERER);
