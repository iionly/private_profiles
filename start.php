<?php

/**
 * Private Profiles plugin for Elgg 2.2 and newer
 * 
 * @package private_profiles
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author iionly
 * @website https://github.com/iionly
 */

use Elgg\PrivateProfiles\Access;
use Elgg\PrivateProfiles\Menus;
use Elgg\PrivateProfiles\Router;

elgg_register_event_handler('init', 'system', 'private_profiles_init');
elgg_register_plugin_hook_handler('route:rewrite', 'settings', [Router::class, 'rewriteSettingsRoute']);

/**
 * Initialize
 * @return void
 */
function private_profiles_init() {

	// Profile
	elgg_register_plugin_hook_handler('route', 'profile', [Router::class, 'routeProfile'], 100);
	elgg_register_page_handler('private_profiles', [Router::class, 'handlePrivateProfiles']);

	elgg_register_plugin_hook_handler('register', 'menu:page', [Menus::class, 'setupPageMenu']);

	elgg_register_action('private_profiles_usersettings/save', __DIR__ . '/actions/save.php');

	// Messages
	if (elgg_is_active_plugin('messages')) {
		elgg_register_plugin_hook_handler('register', 'menu:user_hover', [Menus::class, 'setupUserHoverMenu']);
		elgg_register_plugin_hook_handler('action', 'messages/send', [Access::class, 'interceptPrivateMessage']);
	}

	// Public activity/members page
	elgg_register_plugin_hook_handler('get_sql', 'access', [Access::class, 'applyActivityPrivacy']);
}