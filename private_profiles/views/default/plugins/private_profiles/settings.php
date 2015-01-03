<?php
/**
 * Private Profiles plugin settings
 *
 * @package private_profiles
 */

$default_access_setting = $vars['entity']->default_access_setting;
$default_messages_setting = $vars['entity']->default_messages_setting;
$custom_access_setting = $vars['entity']->custom_access_setting;

// If not configured yet the access to profile pages is not permitted
if (!$default_access_setting) {
	$default_access_setting = 'no';
}

// If not configured yet sending private messages is restricted to friends only
if (!$default_messages_setting) {
	$default_messages_setting = 'friends';
}

// If not configured yet the community members are allowed to configure the access to their profile pages for themselves
if (!$custom_access_setting) {
	$custom_access_setting = 'yes';
}

?>
<div class='mbm'>
	<?php
		echo elgg_echo('private_profiles:default_access_setting') . ' ';
		echo elgg_view('input/select', array(
			'name' => 'params[default_access_setting]',
			'options_values' => array(
				'yes' => elgg_echo('private_profiles:default_access_yes'),
				'members' => elgg_echo('private_profiles:default_access_members'),
				'friends' => elgg_echo('private_profiles:default_access_friends'),
				'no' => elgg_echo('private_profiles:default_access_no'),
			),
			'value' => $default_access_setting,
		));
	?>
</div>
<div class='mbm'>
	<?php
		echo elgg_echo('private_profiles:default_messages_setting') . ' ';
		echo elgg_view('input/select', array(
			'name' => 'params[default_messages_setting]',
			'options_values' => array(
				'yes' => elgg_echo('private_profiles:default_messages_yes'),
				'friends' => elgg_echo('private_profiles:default_messages_friends'),
				'no' => elgg_echo('private_profiles:default_messages_no'),
			),
			'value' => $default_messages_setting,
		));
	?>
</div>
<div class='mbm'>
	<?php
		echo elgg_echo('private_profiles:custom_access_setting') . ' ';
		echo elgg_view('input/select', array(
			'name' => 'params[custom_access_setting]',
			'options_values' => array(
				'yes' => elgg_echo('option:yes'),
				'no' => elgg_echo('option:no'),
			),
			'value' => $custom_access_setting,
		));
	?>
</div>
