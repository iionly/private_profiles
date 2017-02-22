<?php

namespace Elgg\PrivateProfiles;

use ElggMenuItem;

class Menus {

	/**
	 * Setup page menu
	 *
	 * @param string         $hook   "register"
	 * @param string         $type   "menu:page"
	 * @param ElggMenuItem[] $return Menu
	 * @param array          $params Hook params
	 * @return ElggMenuItem]
	 */
	public static function setupPageMenu($hook, $type, $return, $params) {

		if (!elgg_in_context('settings')) {
			return;
		}

		$user = elgg_get_page_owner_entity();
		if (!$user || !$user->canEdit()) {
			return;
		}

		$return[] = ElggMenuItem::factory([
			'name' => 'private_profiles_usersettings',
			'text' => elgg_echo('private_profiles:usersettings'),
			'href' => "settings/privacy/{$user->username}",
		]);

		return $return;
	}

	/**
	 * Setup user hover menu
	 *
	 * @param string         $hook   "register"
	 * @param string         $type   "menu:user_hover"
	 * @param ElggMenuItem[] $return Menu
	 * @param array          $params Hook params
	 * @return ElggMenuItem]
	 */
	public static function setupUserHoverMenu($hook, $type, $return, $params) {

		$user = elgg_extract('entity', $params);
		if (!$user) {
			return;
		}

		if (!Access::canSendPrivateMessage($user)) {
			// Remove send message item if viewer is not allowed 
			// to send messages to the user
			$unregister = [
				'send'
			];
			foreach ($return as $key => $item) {
				if (in_array($item->getName(), $unregister)) {
					unset($return[$key]);
				}
			}
		}

		return $return;
	}

}
