Private Profiles for Elgg 1.9 - 1.11
====================================

Latest Version: 1.9.6  
Released: 2015-06-20  
Contact: iionly@gmx.de  
License: GNU General Public License version 2  
Copyright: (C) iionly 2014-2015


Description
-----------

This plugin allows to restrict the access to profile pages and to restrict usage of private messages. There are side-wide plugin settings available and optionally users can configure the restrictions on their own (default is to allow users to configure their own preferences).

Access to profile pages can be limited to the user's own profile page (default), profile pages of users who made the user their friend, restrict accessing profile pages if the user is logged-in or the profile page access can be made unrestricted.

The sending of private messages to other users can be disabled, can be limited to users who made a user their friend (default) or there are no restrictions regarding who a user can send a private message to. If a user has not yet configured any personal preferences the side-wide settings are used instead.

Independent of the plugin settings an admin can still access all profile pages in any case and can send and receive private messages to and from all users.

Keep in mind that the limitation of access to profile pages and sending of private messages with the "Friends only" option selected does NOT mean that a user can bypass these limitations by simply friending another user. It's the OTHER user who has to friend the user first before the "Friends only" condition is fullfilled. Best would be to also use the "Friend request" plugin to have real two-way friend relationships where friend requests can be accepted or denied right away to make it less confusing for a user to understand who he is friends with.



Installation
------------

1. If you have a previous version of the Private Profiles plugin installed, disable the plugin, then remove the plugin's folder from the mod directory before installing the new version,
2. copy the private_profiles folder into the mod folder of your site,
3. enable the plugin in the admin section of your site,
4. check the plugin settings page and change the settings if necessary.


Changelog
---------

1.9.6

* Switched from using 'pagesetup' event to using 'register', 'menu' plugin hook for adding of usersettings sidebar menu entry.

1.9.5

* Version 1.8.5 updated for Elgg 1.9.

1.8.5

* Added option (both in site-wide and user-specific settings) to allow access to profile pages also to anonymous visitors (i.e. people not logged-in),
* Fixed occurance of fatal error if someone tries to access a non-existing profile page (invalid username). Displaying of an error message instead,
* Consistent order of dropdown menu options and options text in site-wide and user-specific settings.

1.8.4.1

* Fixed: privacy setting "Friends" not correctly handled for message option in hover menu and sending of messages.

1.9.4

* Version 1.8.4 updated for Elgg 1.9.

1.8.4

* Fixed: a user's privacy settings for who should be allowed to send messages not saved.

1.9.3

* Version 1.8.3 updated for Elgg 1.9.

1.8.3

* Fixed: fatal error if messages sending is set to friends only and a logged-out user opens the hover menu.

1.9.2

* Version 1.8.2 updated for Elgg 1.9.

1.8.2

* Fixed: give access to profile pages if plugin setting or user setting does not restrict access,
* Added option to restrict usage of private messages both via site-wide and user-specific setting,
* Added "Friends only" as additional option for restricting access to profile pages and the new private messages sending restrictions,
* Moved user settings from "Configure your tools" to its own page ("Privacy") in the usersettings section.

1.9.1

* Version 1.8.1 updated for Elgg 1.9.

1.8.1

* Added plugin setting to allow / not allow access to profile pages of other users,
* Added plugin setting to allow / not allow each user to set the access restriction to their profile page on their own and the added the corresponding user setting.

1.8.0

* Initial release for Elgg 1.8.
