<?php

elgg_gatekeeper();

$username = elgg_extract('username', $vars);
if ($username) {
	$user = get_user_by_username($username);
} else {
	$user = elgg_get_logged_in_user_entity();
}

if (!$user || !$user->canEdit()) {
	forward('settings/user');
}

elgg_set_context('settings');

elgg_set_page_owner_guid($user->guid);

$title = elgg_echo('private_profiles:usersettings');

elgg_push_breadcrumb(elgg_echo('settings'), "settings/user/$user->username");
elgg_push_breadcrumb($title);

$content = elgg_view_form('private_profiles/usersettings_save', [], [
	'user' => $user,
]);

$params = array(
	'content' => $content,
	'title' => $title,
	'filter' => '',
);

$layout = elgg_view_layout('default', $params);

echo elgg_view_page($title, $layout);
