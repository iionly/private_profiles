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

		if (elgg_check_access_overrides($sender->guid)) {
			return true;
		}

		$messages_setting = self::getMessagesSetting($recipient);

		switch ($messages_setting) {
			case self::ACCESS_PRIVATE :
			default :
				return $recipient->guid == $sender->guid;

			case self::ACCESS_PUBLIC :
				return true;

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
	 * @param string $hook   "action"
	 * @param string $type   "messages/send"
	 * @param mixed  $return Proceed with action?
	 * @param array  $params Hook params
	 * @return mixed
	 */
	public static function interceptPrivateMessage($hook, $type, $return, $params) {

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
			forward(REFERRER);
		}
	}

}
