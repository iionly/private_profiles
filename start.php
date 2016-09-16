<?php

/**
 * Private Profiles plugin for Elgg 2.2 and newer
 * 
 * @package private_profiles
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author iionly
 * @website https://github.com/iionly
 */

use Elgg\PrivateProfiles\Router;

elgg_register_event_handler('init', 'system', 'private_profiles_init');
elgg_register_plugin_hook_handler('route:rewrite', 'settings', [Router::class, 'rewriteSettingsRoute']);

/**
 * Initialize
 * @return void
 */
function private_profiles_init() {

	elgg_register_plugin_hook_handler('route', 'profile', [Router::class, 'routeProfile']);
	elgg_register_page_handler('private_profiles', [Router::class, 'handlePrivateProfiles']);
	
	elgg_register_plugin_hook_handler('register', 'menu:user_hover', 'private_profiles_user_hover_menu');

	elgg_register_plugin_hook_handler('register', 'menu:page', 'private_profiles_usersettings_page');

	elgg_register_plugin_hook_handler('action', 'messages/send', 'private_profiles_pm_intercept');

	elgg_register_action('private_profiles_usersettings/save', elgg_get_plugins_path() . 'private_profiles/actions/save.php');
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

		$item = new ElggMenuItem('private_profiles_usersettings', elgg_echo('private_profiles:usersettings'), "settings/privacy/{$user->username}");
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
