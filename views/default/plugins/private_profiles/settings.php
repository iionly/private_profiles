<?php

/**
 * Private Profiles plugin settings
 *
 * @package private_profiles
 */
use Elgg\PrivateProfiles\Access;

$entity = elgg_extract('entity', $vars);

echo elgg_view_field([
	'#type' => 'radio',
	'name' => 'params[default_access_setting]',
	'options' => array_flip([
		Access::ACCESS_PUBLIC => elgg_echo('private_profiles:default_access_yes'),
		Access::ACCESS_LOGGED_IN => elgg_echo('private_profiles:default_access_members'),
		Access::ACCESS_FRIENDS => elgg_echo('private_profiles:default_access_friends'),
		Access::ACCESS_PRIVATE => elgg_echo('private_profiles:default_access_no'),
	]),
	'value' => $entity->default_access_setting,
	'#label' => elgg_echo('private_profiles:default_access_setting:label'),
	'#help' => elgg_echo('private_profiles:default_access_setting'),
]);

echo elgg_view_field([
	'#type' => 'radio',
	'name' => 'params[default_messages_setting]',
	'options' => array_flip([
		Access::ACCESS_PUBLIC => elgg_echo('private_profiles:default_messages_yes'),
		Access::ACCESS_FRIENDS => elgg_echo('private_profiles:default_messages_friends'),
		Access::ACCESS_PRIVATE => elgg_echo('private_profiles:default_messages_no'),
	]),
	'value' => $entity->default_messages_setting,
	'#label' => elgg_echo('private_profiles:default_messages_setting:label'),
	'#help' => elgg_echo('private_profiles:default_messages_setting'),
]);

echo elgg_view_field([
	'#type' => 'radio',
	'name' => 'params[custom_access_setting]',
	'options' => array_flip([
		'yes' => elgg_echo('option:yes'),
		'no' => elgg_echo('option:no'),
	]),
	'value' => $entity->custom_access_setting,
	'#label' => elgg_echo('private_profiles:custom_access_setting:label'),
	'#help' => elgg_echo('private_profiles:custom_access_setting'),
]);
