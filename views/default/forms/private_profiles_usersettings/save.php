<?php

$user = $vars['user'];

$custom_access_setting = elgg_get_plugin_setting('custom_access_setting', 'private_profiles');

if (!$custom_access_setting) {
	$custom_access_setting = 'yes';
}

if ($custom_access_setting == 'no') {
?>
<div class='mbm'>
	<?php
		echo elgg_echo('private_profiles:no_custom_access_setting') . ' ';
		$default_access_setting = elgg_get_plugin_setting('default_access_setting', 'private_profiles');
		if (!$default_access_setting) {
			$default_access_setting = 'no';
		}
		if ($default_access_setting == 'yes') {
			echo elgg_echo('private_profiles:default_access_setting:current_yes');
		} else if ($default_access_setting == 'members') {
			echo elgg_echo('private_profiles:default_access_setting:current_members');
		} else if ($default_access_setting == 'friends') {
			echo elgg_echo('private_profiles:default_access_setting:current_friends');
		} else {
			echo elgg_echo('private_profiles:default_access_setting:current_no');
		}

	?>
</div>
<div class='mbm'>
	<?php
		echo elgg_echo('private_profiles:no_custom_messages_setting') . ' ';
		$default_messages_setting = elgg_get_plugin_setting('default_messages_setting', 'private_profiles');
		if (!$default_messages_setting) {
			$default_messages_setting = 'friends';
		}
		if ($default_messages_setting == 'yes') {
			echo elgg_echo('private_profiles:default_messages_setting:current_yes');
		} else if ($default_messages_setting == 'no') {
			echo elgg_echo('private_profiles:default_messages_setting:current_no');
		} else {
			echo elgg_echo('private_profiles:default_messages_setting:current_friends');
		}
	?>
</div>
<?php
} else {
	$user_access_setting = elgg_get_plugin_user_setting('user_access_setting', $user->getGUID(), 'private_profiles');
	$user_messages_setting = elgg_get_plugin_user_setting('user_messages_setting', $user->getGUID(), 'private_profiles');

	$default_access_setting = elgg_get_plugin_setting('default_access_setting', 'private_profiles');
	if (!$default_access_setting) {
		$default_access_setting = 'no';
	}
	$default_messages_setting = elgg_get_plugin_setting('default_messages_setting', 'private_profiles');
	if (!$default_messages_setting) {
		$default_messages_setting = 'friends';
	}

	if (!$user_access_setting) {
		$user_access_setting = $default_access_setting;
	}
	if (!$user_messages_setting) {
		$user_messages_setting = $default_messages_setting;
	}
?>
<div class='mbm'>
	<?php
		echo elgg_echo('private_profiles:user_access_setting') . '<br>';
		echo elgg_view('input/select', array(
			'name' => 'params[user_access_setting]',
			'options_values' => array(
				'yes' => elgg_echo('private_profiles:user_access_setting_yes'),
				'members' => elgg_echo('private_profiles:user_access_setting_members'),
				'friends' => elgg_echo('private_profiles:user_access_setting_friends'),
				'no' => elgg_echo('private_profiles:user_access_setting_no'),
			),
			'value' => $user_access_setting,
		));
	?>
</div>
<div class='mbm'>
	<?php
		echo elgg_echo('private_profiles:user_messages_setting') . '<br>';
		echo elgg_view('input/select', array(
			'name' => 'params[user_messages_setting]',
			'options_values' => array(
				'yes' => elgg_echo('private_profiles:user_messages_setting_yes'),
				'friends' => elgg_echo('private_profiles:user_messages_setting_friends'),
				'no' => elgg_echo('private_profiles:user_messages_setting_no'),
			),
			'value' => $user_messages_setting,
		));
	?>
</div>
<div class="elgg-foot">
	<?php
		echo elgg_view('input/hidden', array('name' => 'guid', 'value' => $user->guid));
		echo elgg_view('input/submit', array('value' => elgg_echo('save')));
	?>
</div>
<?php
}
