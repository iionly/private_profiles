<?php

namespace Elgg\PrivateProfiles;

class Router {

	/**
	 * Route /profile pages
	 * 
	 * @param string $hook   "route"
	 * @param string $type   "profile"
	 * @param mixed  $return Route
	 * @param array  $params Hook params
	 * @return mixed
	 */
	public static function routeProfile($hook, $type, $return, $params) {
		if (!is_array($return)) {
			return;
		}

		$segments = (array) elgg_extract('segments', $return, []);

		$username = array_shift($segments);
		$user = get_user_by_username($username);

		if (!$user) {
			register_error(elgg_echo('private_profiles:invalid_username'));
			forward(REFERRER, '404');
		}

		if (!Access::hasAccessToProfile($user)) {
			register_error(elgg_echo('private_profiles:access_denied'));
			forward(REFERRER, '403');
		}
	}

	/**
	 * Route /settings/privacy pages
	 *
	 * @param string $hook   "route"
	 * @param string $type   "settings"
	 * @param mixed  $return Route
	 * @param array  $params Hook params
	 * @return mixed
	 */
	public static function rewriteSettingsRoute($hook, $type, $return, $params) {
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
	 * Handle /profile_profiles page
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
