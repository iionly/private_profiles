<?php

/**
 * Private Profiles plugin for Elgg 1.8 and newer
 * @package private_profiles
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author iionly
 * @website https://github.com/iionly
 *
 */

elgg_register_event_handler('init', 'system', 'private_profiles_init');

function private_profiles_init() {
	elgg_register_plugin_hook_handler('route', 'profile', 'private_profiles_router');
	elgg_register_plugin_hook_handler('register', 'menu:user_hover', 'private_profiles_user_hover_menu');

	elgg_register_page_handler('private_profiles', 'private_profiles_page_handler');

	elgg_register_plugin_hook_handler('register', 'menu:page', 'private_profiles_usersettings_page');

	elgg_register_plugin_hook_handler('action', 'messages/send', 'private_profiles_pm_intercept');

	elgg_register_action('private_profiles_usersettings/save', elgg_get_plugins_path() . 'private_profiles/actions/save.php');
}

function private_profiles_page_handler($page) {
	gatekeeper();
	$current_user = elgg_get_logged_in_user_entity();

	if (!isset($page[0])) {
		$page[0] = 'usersettings';
	}
	if (!isset($page[1])) {
		forward("private_profiles/{$page[0]}/{$current_user->username}");
	}

	$user = get_user_by_username($page[1]);
	if (($user->guid != $current_user->guid) && !$current_user->isAdmin()) {
		forward();
	}

	switch ($page[0]) {
		case 'usersettings':
			require elgg_get_plugins_path() . 'private_profiles/index.php';
			break;
		default:
			return false;
	}
	return true;
}

function private_profiles_router($hook, $type, $result, $params) {
	if (elgg_is_admin_logged_in()) {
		return $result;
	}

	$custom_access_setting = elgg_get_plugin_setting('custom_access_setting', 'private_profiles');
	if (!$custom_access_setting) {
		$custom_access_setting = 'yes';
	}

	if ($custom_access_setting == 'no') {

		$default_access_setting = elgg_get_plugin_setting('default_access_setting', 'private_profiles');
		if (!$default_access_setting) {
			$default_access_setting = 'no';
		}

		// Access allowed by default to everyone?
		if ($default_access_setting == 'yes') {
			return $result;
		}

		$page = $result['segments'];
		$page = array_pad($page, 4, "");

		if (isset($page[0])) {
			$username = $page[0];
			$user = get_user_by_username($username);
			if ($user) {
				if ($logged_in_user_guid = elgg_get_logged_in_user_guid()) {
					if ($default_access_setting == 'members') {
						return $result;
					} else if ($default_access_setting == 'friends') {
						if (($logged_in_user_guid == $user->getGUID()) || (elgg_get_logged_in_user_entity()->isFriendOf($user->getGUID()))) {
							return $result;
						}
					} else if ($default_access_setting == 'no') {
						if ($logged_in_user_guid == $user->getGUID()) {
							return $result;
						}
					}
				}
			} else {
				register_error(elgg_echo('private_profiles:invalid_username'));
				forward(REFERER);
				return false;
			}
		}

	} else {

		$page = $result['segments'];
		$page = array_pad($page, 4, "");

		if (isset($page[0])) {
			$username = $page[0];
			$user = get_user_by_username($username);
			if ($user) {
				// Does the user who owns the profile page allows other users to visit the page?
				$user_access_setting = elgg_get_plugin_user_setting('user_access_setting', $user->getGUID(), 'private_profiles');
				if (!$user_access_setting) {
					$default_access_setting = elgg_get_plugin_setting('default_access_setting', 'private_profiles');
					if (!$default_access_setting) {
						$default_access_setting = 'no';
					}
					$user_access_setting = $default_access_setting;
				}

				// Access allowed by user to everyone?
				if ($user_access_setting == 'yes') {
					return $result;
				}

				if ($logged_in_user_guid = elgg_get_logged_in_user_guid()) {
					if ($user_access_setting == 'members') {
						return $result;
					} else if ($user_access_setting == 'friends') {
						if (($logged_in_user_guid == $user->getGUID()) || (elgg_get_logged_in_user_entity()->isFriendOf($user->getGUID()))) {
							return $result;
						}
					} else if ($user_access_setting == 'no') {
						if ($logged_in_user_guid == $user->getGUID()) {
							return $result;
						}
					}
				}
			} else {
				register_error(elgg_echo('private_profiles:invalid_username'));
				forward(REFERER);
				return false;
			}
		}
	}

	// either no one logged in or no valid profile username or logged in user is trying to view another user's profile
	register_error(elgg_echo('private_profiles:access_denied'));
	forward(REFERER);
	return false;
}

function private_profiles_user_hover_menu($hook, $type, $menu, $params) {
	$user = $params['entity'];
	if (elgg_is_admin_logged_in() || $user->isAdmin()) {
		return $menu;
	}
	$logged_in_user = elgg_get_logged_in_user_entity();

	$custom_access_setting = elgg_get_plugin_setting('custom_access_setting', 'private_profiles');
	if (!$custom_access_setting) {
		$custom_access_setting = 'yes';
	}

	if ($custom_access_setting == 'no') {
		$default_messages_setting = elgg_get_plugin_setting('default_messages_setting', 'private_profiles');
		if (!$default_messages_setting) {
			$default_messages_setting = 'friends';
		}
		
		if ($default_messages_setting == 'yes') {
			return $menu;
		} else if ((($default_messages_setting == 'friends') && ($logged_in_user && !$logged_in_user->isFriendOf($user->getGUID()))) || ($default_messages_setting == 'no')) {
			foreach ($menu as $key => $item) {
				switch ($item->getName()) {
					case 'send':
						unset($menu[$key]);
						break;
				}
			}
		}

	} else {
		$user_messages_setting = elgg_get_plugin_user_setting('user_messages_setting', $user->getGUID(), 'private_profiles');
		if (!$user_messages_setting) {
			$default_messages_setting = elgg_get_plugin_setting('default_messages_setting', 'private_profiles');
			if (!$default_messages_setting) {
				$default_messages_setting = 'friends';
			}
			$user_messages_setting = $default_messages_setting;
		}

		if ($user_messages_setting == 'yes') {
			return $menu;
		} else if ((($user_messages_setting == 'friends') && ($logged_in_user && !$logged_in_user->isFriendOf($user->getGUID()))) || ($user_messages_setting == 'no')) {
			foreach ($menu as $key => $item) {
				switch ($item->getName()) {
					case 'send':
						unset($menu[$key]);
						break;
				}
			}
		}
	}

	return $menu;
}

function private_profiles_usersettings_page($hook, $type, $return, $params) {
	if (elgg_get_context() == "settings" && elgg_get_logged_in_user_guid()) {

		$user = elgg_get_page_owner_entity();
		if (!$user) {
			$user = elgg_get_logged_in_user_entity();
		}

		$item = new ElggMenuItem('private_profiles_usersettings', elgg_echo('private_profiles:usersettings'), "private_profiles/usersettings/{$user->username}");
		$return[] = $item;
	}

	return $return;
}

function private_profiles_pm_intercept($hook, $type, $result, $params) {
	$subject = strip_tags(get_input('subject'));
	$body = get_input('body');
	$recipient_username = get_input('recipient_username');
	$original_msg_guid = (int)get_input('original_guid');
	elgg_make_sticky_form('messages');

	$user = get_user_by_username($recipient_username);
	if (!$user || elgg_is_admin_logged_in() || $user->isAdmin()) {
		return $result;
	}
	$logged_in_user = elgg_get_logged_in_user_entity();

	$custom_access_setting = elgg_get_plugin_setting('custom_access_setting', 'private_profiles');
	if (!$custom_access_setting) {
		$custom_access_setting = 'yes';
	}

	if ($custom_access_setting == 'no') {
		$default_messages_setting = elgg_get_plugin_setting('default_messages_setting', 'private_profiles');
		if (!$default_messages_setting) {
			$default_messages_setting = 'friends';
		}
		
		if (($default_messages_setting == 'yes') || (($default_messages_setting == 'friends') && ($logged_in_user && $logged_in_user->isFriendOf($user->getGUID())))) {
			return $result;
		}
	} else {
		$user_messages_setting = elgg_get_plugin_user_setting('user_messages_setting', $user->getGUID(), 'private_profiles');
		if (!$user_messages_setting) {
			$default_messages_setting = elgg_get_plugin_setting('default_messages_setting', 'private_profiles');
			if (!$default_messages_setting) {
				$default_messages_setting = 'friends';
			}
			$user_messages_setting = $default_messages_setting;
		}

		if (($user_messages_setting == 'yes') || (($user_messages_setting == 'friends') && ($logged_in_user && $logged_in_user->isFriendOf($user->getGUID())))) {
			return $result;
		}
	}
	register_error(elgg_echo('private_profiles:sending_denied'));
	forward("messages/compose");
	return false;
}
