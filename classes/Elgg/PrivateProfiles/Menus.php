<?php

namespace Elgg\PrivateProfiles;

use ElggMenuItem;

class Menus {

	/**
	 * Setup page menu
	 *
	 */
	public static function setupPageMenu(\Elgg\Hook $hook) {
		if (!elgg_in_context('settings')) {
			return;
		}

		$user = elgg_get_page_owner_entity();
		if (!$user || !$user->canEdit()) {
			return;
		}

		$menu = $hook->getValue();
		$menu[] = ElggMenuItem::factory([
			'name' => 'private_profiles_usersettings',
			'text' => elgg_echo('private_profiles:usersettings'),
			'href' => "settings/privacy/{$user->username}",
		]);

		return $menu;
	}
}