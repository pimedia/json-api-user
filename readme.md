=== JSON API User ===

Tags: json api, rest json api, register user via rest, wordpress restful user registration

Contributors: parorrey

Stable tag: 1.0

Requires at least: 3.0.1

Tested up to: 3.8.1

License: GPLv2 or later

License URI: http://www.gnu.org/licenses/gpl-2.0.html

Extends the JSON API Plugin


==Description==


JSON API User is a plugin that extends the JSON API Plugin with a new Controller to allow RESTful user registration, password reset and other user functions.

Features include:

* User Registration
* Password Reset
* Get Avatar
* Get User Info

==Installation==

First you have to install the JSON API for WordPress Plugin (http://wordpress.org/extend/plugins/json-api/installation/).

To install JSON API User just follow these steps:

* upload the folder "json-api-user" to your WordPress plugin folder (/wp-content/plugins)

* activate the plugin through the 'Plugins' menu in WordPress or by using the link provided by the plugin installer

* activate the controller through the JSON API menu found in the WordPress admin center (Settings -> JSON API)

== Screenshots ==


==Changelog==


= 1.0 =
* Added retrieve_password method. It needs user_login var.
* Added get_avatar method. It needs user_id var.
* Added get_userinfo method. It needs user_id var.

= 0.1 =
* Initial release.


== Upgrade Notice ==

= 0.1 =
* Initial release.

==Frequently Asked Questions==

For a full code documentation, please check here https://github.com/pimedia/json-api-user

* There are several methods available: register, retrieve_password, get_avatar, get_userinfo

* nonce can be created by calling http://your-domain/api/get_nonce/?controller=user&method=register

* You can then use 'nonce' value to register user.