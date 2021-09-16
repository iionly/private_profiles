<?php

namespace Elgg\PrivateProfiles;

class Router {

	/**
	 * Route /profile pages
	 *
	 */
	public static function routeProfile(\Elgg\Hook $hook) {
		$return = $hook->getValue();
		if (!is_array($return)) {
			return;
		}

		$segments = (array) elgg_extract('segments', $return, []);

		$username = array_shift($segments);
		$user = get_user_by_username($username);

		if (!$user) {
			register_error(elgg_echo('private_profiles:invalid_username'));
			forward(REFERER);
			return false;
		}

		if (!Access::hasAccessToProfile($user)) {
			register_error(elgg_echo('private_profiles:access_denied'));
			forward(REFERER);
			return false;
		}
	}

	/**
	 * Route /settings/privacy pages
	 *
	 */
	public static function rewriteSettingsRoute(\Elgg\Hook $hook) {
		$return = $hook->getValue();
		if (!is_array($return)) {
			return;
		}

		$identifier = elgg_extract('identifier', $return);
		$segments = (array) elgg_extract('segments', $return, []);

		$page = array_shift($segments);
		$username = array_shift($segments);

		if ($page == 'privacy') {
			return [
				'identifier' => 'private_profiles',
				'segments' => [
					'usersettings',
					$username,
				],
			];
		}
	}

	/**
	 * Handle /private_profiles page
	 *
	 * @param array $segments URL segments
	 * @return bool
	 */
	public function handlePrivateProfiles($segments) {

		$page = array_shift($segments);
		$username = array_shift($segments);

		echo elgg_view_resource("private_profiles/$page", [
			'username' => $username,
		]);
		
		return true;
	}

}
