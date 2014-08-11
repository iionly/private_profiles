<?php
/**
 * Private Profiles plugin settings
 *
 * @package private_profiles
 */

$default_access_setting = $vars['entity']->default_access_setting;
$custom_access_setting = $vars['entity']->custom_access_setting;

// If not configured yet the access to profile pages is not permitted
if (!$default_access_setting) {
	$default_access_setting = 'no';
}

// If not configured yet the community members are allowed to configure the access to their profile pages for themselves
if (!$custom_access_setting) {
	$custom_access_setting = 'yes';
}

?>
<div>
	<?php

		echo elgg_echo('private_profiles:default_access_setting') . ' ';
		echo elgg_view('input/dropdown', array(
			'name' => 'params[default_access_setting]',
			'options_values' => array(
				'no' => elgg_echo('option:no'),
				'yes' => elgg_echo('option:yes'),
			),
			'value' => $default_access_setting,
		));
	?>
</div>
<div>
	<?php

		echo elgg_echo('private_profiles:custom_access_setting') . ' ';
		echo elgg_view('input/dropdown', array(
			'name' => 'params[custom_access_setting]',
			'options_values' => array(
				'yes' => elgg_echo('option:yes'),
				'no' => elgg_echo('option:no'),
			),
			'value' => $custom_access_setting,
		));
	?>
</div>
