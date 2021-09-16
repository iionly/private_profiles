<?php

require_once(dirname(__FILE__) . '/lib/hooks.php');

return [
	'bootstrap' => \PrivateProfilesBootstrap::class,
	'actions' => [
		'private_profiles/usersettings_save' => [
			'access' => 'logged_in',
		],
	],
	'settings' => [
		'default_access_setting' => 'no',
		'default_messages_setting' => 'friends',
		'custom_access_setting' => 'yes',
	],
	'routes' => [
		'private_profiles:usersettings' => [
			'path' => '/private_profiles/usersettings/{username?}',
			'resource' => 'private_profiles/usersettings',
		],
	],
];
