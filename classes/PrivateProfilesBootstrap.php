<?php

/**
 * Private Profiles plugin
 * 
 * @package private_profiles
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author iionly
 * @website https://github.com/iionly
 */

use Elgg\DefaultPluginBootstrap;

use Elgg\PrivateProfiles\Access;
use Elgg\PrivateProfiles\Menus;
use Elgg\PrivateProfiles\Router;

class PrivateProfilesBootstrap extends DefaultPluginBootstrap {

	public function boot() {
		elgg_register_plugin_hook_handler('route:rewrite', 'settings', [Router::class, 'rewriteSettingsRoute']);
		elgg_register_plugin_hook_handler('route:rewrite', 'profile', [Router::class, 'routeProfile'], 100);
	}

	public function init() {
		elgg_register_plugin_hook_handler('register', 'menu:page', [Menus::class, 'setupPageMenu']);

		// Messages
		elgg_register_plugin_hook_handler('register', 'menu:user_hover', 'setupUserHoverMenu', 501);
		elgg_register_plugin_hook_handler('register', 'menu:title', 'setupUserHoverMenu', 501);
		elgg_register_plugin_hook_handler('action:validate', 'messages/send', [Access::class, 'interceptPrivateMessage']);

		// Public activity/members page
		elgg_register_plugin_hook_handler('get_sql', 'access', [Access::class, 'applyActivityPrivacy']);
	}
}
