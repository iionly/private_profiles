<?php

use Elgg\PrivateProfiles\Access;

$user = elgg_extract('user', $vars);
if (!$user || !$user->canEdit()) {
	return;
}

$fields = [];

$custom_access_setting = elgg_get_plugin_setting('custom_access_setting', 'private_profiles');

$default_access_setting = elgg_get_plugin_setting('default_access_setting', 'private_profiles');
$default_messages_setting = elgg_get_plugin_setting('default_messages_setting', 'private_profiles');

if ($custom_access_setting == 'no') {

	$access_setting_info = elgg_echo('private_profiles:no_custom_access_setting') . ' ';
	$access_setting_info .= elgg_echo("private_profiles:default_access_setting:current_{$default_access_setting}");

	echo elgg_format_element('p', [], $access_setting_info);

	$messages_setting_info = elgg_echo('private_profiles:no_custom_messages_setting') . ' ';
	$messages_setting_info .= elgg_echo("private_profiles:default_messages_setting:current_{$default_messages_setting}");

	echo elgg_format_element('p', [], $messages_setting_info);
} else {
	$user_access_setting = elgg_get_plugin_user_setting('user_access_setting', $user->guid, 'private_profiles', $default_access_setting);
	$fields[] = elgg_view_field([
		'#type' => 'radio',
		'name' => 'params[user_access_setting]',
		'options' => array_flip([
			Access::ACCESS_PUBLIC => elgg_echo('private_profiles:user_access_setting_yes'),
			Access::ACCESS_LOGGED_IN => elgg_echo('private_profiles:user_access_setting_members'),
			Access::ACCESS_FRIENDS => elgg_echo('private_profiles:user_access_setting_friends'),
			Access::ACCESS_PRIVATE => elgg_echo('private_profiles:user_access_setting_no'),
		]),
		'value' => $user_access_setting,
		'#label' => elgg_echo('private_profiles:user_access_setting'),
	]);

	$user_messages_setting = elgg_get_plugin_user_setting('user_messages_setting', $user->guid, 'private_profiles', $default_messages_setting);
	$fields[] = elgg_view_field([
		'#type' => 'radio',
		'name' => 'params[user_messages_setting]',
		'options' => array_flip([
			Access::ACCESS_PUBLIC => elgg_echo('private_profiles:user_messages_setting_yes'),
			Access::ACCESS_FRIENDS => elgg_echo('private_profiles:user_messages_setting_friends'),
			Access::ACCESS_PRIVATE => elgg_echo('private_profiles:user_messages_setting_no'),
		]),
		'value' => $user_messages_setting,
		'#label' => elgg_echo('private_profiles:user_messages_setting'),
	]);
}

if (!elgg_get_config('walled_garden') && !elgg_is_active_plugin('loginrequired')) {
	$user_activity_setting = elgg_get_plugin_user_setting('user_activity_setting', $user->guid, 'private_profiles', Access::ACCESS_PUBLIC);
	$fields[] = elgg_view_field([
		'#type' => 'radio',
		'name' => 'params[user_activity_setting]',
		'options' => array_flip([
			Access::ACCESS_PUBLIC => elgg_echo('private_profiles:user_activity_setting_yes'),
			Access::ACCESS_LOGGED_IN => elgg_echo('private_profiles:user_activity_setting_members'),
		]),
		'value' => $user_activity_setting,
		'#label' => elgg_echo('private_profiles:user_activity_setting'),
	]);
}

if (!empty($fields)) {
	echo implode('', $fields);

	echo elgg_view_field([
		'#type' => 'hidden',
		'name' => 'guid',
		'value' => $user->guid,
	]);

	echo elgg_view_field([
		'#type' => 'submit',
		'value' => elgg_echo('save'),
		'class' => 'elgg-foot',
	]);
}
