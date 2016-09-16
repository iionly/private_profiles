<?php

namespace Elgg\PrivateProfiles;

class Access {
	
	const ACCESS_PUBLIC = 'yes';
	const ACCESS_PRIVATE = 'no';
	const ACCESS_FRIENDS = 'friends';
	const ACESSS_LOGGED_IN = 'members';
	
	/**
	 * Check if the viewer has permissions to access user profile
	 * 
	 * @param ElggUser $user   Profile owner
	 * @param ElggUser $viewer Viewer (default to logged in user)
	 */
	public static function hasAccessToProfile(ElggUser $user, ElggUser $viewer = null) {
		if (!isset($viewer)) {
			$viewer = elgg_get_logged_in_user_entity();
		}

		if (elgg_check_access_overrides($viewer->guid)) {
			return true;
		}

		$access_setting = self::getAccessSetting($user);

		switch ($access_setting) {
			case self::ACCESS_PRIVATE :
			default :
				return $user->guid == $viewer->guid;

			case self::ACCESS_PUBLIC :
				return true;

			case self::ACCESS_LOGGED_IN :
				return ($viewer);

			case self::ACCESS_FRIENDS :
				return $viewer && $viewer->isFriendOf($user->guid);
		}
	}

	/**
	 * Get profile access setting for the user
	 *
	 * @param ElggUser $user Profile owner
	 * @return string
	 */
	public static function getAccessSetting(ElggUser $user) {

		$access_setting = elgg_get_plugin_setting('default_access_setting', 'private_profiles', self::ACCESS_PRIVATE);

		$custom_access_setting = elgg_get_plugin_setting('custom_access_setting', 'private_profiles', 'yes');
		if ($custom_access_setting != 'yes') {
			// Users are not allowed to customize their own settings
			return $access_setting;
		}

		$user_access_setting = elgg_get_plugin_user_setting('user_access_setting', $user->guid, 'private_profiles');
		if ($user_access_setting) {
			$access_setting = $user_access_setting;
		}

		return $access_setting;
	}

}
