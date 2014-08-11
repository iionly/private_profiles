<?php
/**
 * Private Profiles plugin per user settings
 *
 * @package private_profiles
 */

$plugin = $vars["entity"];

$custom_access_setting = $plugin->custom_access_setting;

if (!$custom_access_setting) {
	$custom_access_setting = 'yes';
}

if ($custom_access_setting == 'no') {
?>
<div>
	<?php
	echo elgg_echo('private_profiles:no_custom_access_setting') . ' ';
	$default_access_setting = $plugin->default_access_setting;
	if (!$default_access_setting) {
		$default_access_setting = 'no';
	}
	if ($default_access_setting == 'no') {
		echo elgg_echo('private_profiles:default_access_setting:current_no');
	} else {
		echo elgg_echo('private_profiles:default_access_setting:current_yes');
	}
?>
</div>
<?php
} else {

	$user_access_setting = $plugin->getUserSetting("user_access_setting", elgg_get_page_owner_guid());

	if (!$user_access_setting) {
		$user_access_setting = 'no';
	}
?>
<div>
	<?php

		echo elgg_echo('private_profiles:user_access_setting') . ' ';
		echo elgg_view('input/dropdown', array(
			'name' => 'params[user_access_setting]',
			'options_values' => array(
				'no' => elgg_echo('option:no'),
				'yes' => elgg_echo('option:yes'),
			),
			'value' => $user_access_setting,
		));
	?>
</div>
<?php
}
