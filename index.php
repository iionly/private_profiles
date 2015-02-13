<?php

if (!isset($user) || !($user instanceof ElggUser)) {
	$url = 'private_profiles/usersettings/' . elgg_get_logged_in_user_entity()->username;
	forward($url);
}

elgg_set_page_owner_guid($user->guid);

// Set the context to settings
elgg_set_context('settings');

$title = elgg_echo('private_profiles:usersettings');

elgg_push_breadcrumb(elgg_echo('settings'), "settings/user/$user->username");
elgg_push_breadcrumb($title);

$body = elgg_view('private_profiles/form', array('user' => $user));

$params = array(
	'content' => $body,
	'title' => $title,
);
$body = elgg_view_layout('one_sidebar', $params);

echo elgg_view_page($title, $body);
