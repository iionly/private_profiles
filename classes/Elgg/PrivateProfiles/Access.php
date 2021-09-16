<?php

namespace Elgg\PrivateProfiles;

use ElggUser;

class Access {

	const ACCESS_PUBLIC = 'yes';
	const ACCESS_PRIVATE = 'no';
	const ACCESS_FRIENDS = 'friends';
	const ACCESS_LOGGED_IN = 'members';

	/**
	 * As elgg_check_access_overrides() was removed in Elgg 3
	 * we re-implement it here
	 * 
	 * @param int $user_guid The user to check against.
	 * @return bool
	 */
	public static function privateprofiles_check_access_overrides($user_guid = 0) {
		if (!$user_guid || $user_guid <= 0) {
			$is_admin = false;
		} else {
			$user = get_user($user_guid);
			$is_admin = $user->isAdmin();
		}

		return ($is_admin || elgg_get_ignore_access());
	}

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

		if (self::privateprofiles_check_access_overrides($viewer->guid)) {
			return true;
		}

		if ($user->canEdit($viewer->guid)) {
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

	/**
	 * Check if the sender is allowed to send a private message to the recipient
	 *
	 * @param ElggUser $recipient Recipient
	 * @param ElggUser $sender    Sender (default to logged in user)
	 */
	public static function canSendPrivateMessage(ElggUser $recipient, ElggUser $sender = null) {
		if (!isset($sender)) {
			$sender = elgg_get_logged_in_user_entity();
		}

		if (!$sender) {
			// Non-logged in users can't send messages
			return false;
		}

		if (self::privateprofiles_check_access_overrides($sender->guid)) {
			return true;
		}

		if ($recipient->canEdit($sender->guid)) {
			return true;
		}

		$messages_setting = self::getMessagesSetting($recipient);

		switch ($messages_setting) {
			case self::ACCESS_PRIVATE :
			default :
				return $recipient->guid == $sender->guid;

			case self::ACCESS_PUBLIC :
			case self::ACCESS_LOGGED_IN :
				return ($sender);

			case self::ACCESS_FRIENDS :
				return $sender && $sender->isFriendOf($recipient->guid);
		}
	}

	/**
	 * Get message setting for the user
	 *
	 * @param ElggUser $user Profile owner
	 * @return string
	 */
	public static function getMessagesSetting(ElggUser $user) {

		$message_setting = elgg_get_plugin_setting('default_messages_setting', 'private_profiles', self::ACCESS_PRIVATE);

		$custom_setting = elgg_get_plugin_setting('custom_access_setting', 'private_profiles', 'yes');
		if ($custom_setting != 'yes') {
			// Users are not allowed to customize their own settings
			return $message_setting;
		}

		$user_message_setting = elgg_get_plugin_user_setting('user_messages_setting', $user->guid, 'private_profiles');
		if ($user_message_setting) {
			$message_setting = $user_message_setting;
		}

		return $message_setting;
	}

	/**
	 * Intercept a message being sent to a user without sufficient permissions
	 *
	 */
	public static function interceptPrivateMessage(\Elgg\Hook $hook) {

		$recipients = get_input('recipients');
		$original_msg_guid = (int) get_input('original_guid');

		if ($original_msg_guid) {
			// Allow users to respond to messages they have received
			return;
		}

		$error = false;
		foreach ($recipients as $guid) {
			$recipient = get_user($guid);
			if (!$recipient) {
				continue;
			}
			if (!self::canSendPrivateMessage($recipient)) {
				$error = true;
				break;
			}
		}

		if ($error) {
			register_error(elgg_echo('private_profiles:sending_denied'));

			// forward to referrer or else action code sends to front page
			forward(REFERER);

			return false;
		}
		
		return;
	}

	/**
	 * Hide user activity and membership listing according to settings
	 *
	 * @return array
	 */
	public static function applyActivityPrivacy(\Elgg\Hook $hook) {

		if (elgg_in_context('action')) {
			// let actions such as /login run without hinderance
			return;
		}

		$user_guid = $hook->getParam('user_guid');
		if ($user_guid) {
			// activity privacy setting only applies to logged out users
			return;
		}

		if ($hook->getParam('ignore_access')) {
			return;
		}

		$dbprefix = elgg_get_config('dbprefix');
		$table_alias = $hook->getParam('table_alias') ? $hook->getParam('table_alias') . '.' : '';

		$guid_column = $hook->getParam('guid_column', 'guid');
		$owner_guid_column = $hook->getParam('owner_guid_column', 'owner_guid');
		
		$return = $hook->getValue();

		// Exclude entities owned by users who have chosen to keep their activity to members only
		$value = self::ACCESS_LOGGED_IN;
		$return['ands'][] = "NOT EXISTS (SELECT 1 FROM {$dbprefix}private_settings WHERE entity_guid IN ({$table_alias}{$guid_column}, {$table_alias}{$owner_guid_column}) AND name='plugin:user_setting:private_profiles:user_activity_setting' AND value='$value')";

		return $return;
	}

}
