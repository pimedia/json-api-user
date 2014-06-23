=== JSON API User ===

Donate link:

Tags: json api, RESTful user registration, RESTful Facebook Login, RESTful User Meta and BuddyPress xProfile

Contributors: parorrey

Stable tag: 1.1

Requires at least: 3.0.1

Tested up to: 3.9.1

License: GPLv2 or later

License URI: http://www.gnu.org/licenses/gpl-2.0.html

Extends the JSON API Plugin


==Description==


JSON API User is a plugin that extends the JSON API Plugin with a new Controller to allow RESTful user registration, password reset, RESTful Facebook Login, RESTful User Meta and BuddyPress xProfile methods. This plugin is for WordPress/Mobile app developers who also want to use WordPress as mobile app backend. 


Features include:

* Generate Auth Cookie for user authentication
* Validate Auth Cookie
* RESTful User Registration
* RESTful Facebook Login/Registration with valid access_token
* RESTful User Meta update
* RESTful BuddyPress xProfile fields update
* Get User Meta and xProfile fields
* Password Reset
* Get Avatar
* Get User Info

The plugin was created for mobile apps integration with the web app using WordPress as backend for all the data. WordPress helped in putting together the web app quickly and then Mobile iOS and Android apps were integrated via this plugin. There were some app specific customized methods which are not included but rest have been made generic for community usage. 

My other JSON API Auth plugin has also been integrated with this from version 1.1. since most methods required user authentication via cookie for data update. You can also write your own methods by copying the existing methods if you need any user related features.

Hope this will help some.

==Installation==

First you have to install the JSON API for WordPress Plugin (http://wordpress.org/extend/plugins/json-api/installation/).

To install JSON API User just follow these steps:

* Upload the folder "json-api-user" to your WordPress plugin folder (/wp-content/plugins)
* Activate the plugin through the 'Plugins' menu in WordPress or by using the link provided by the plugin installer
* Activate the controller through the JSON API menu found in the WordPress admin center (Settings -> JSON API)

==Screenshots==


==Changelog==

= 1.1 =

* Added fb_connect method. It needs valid 'access_token' var.

* Added validate_auth_cookie method. It needs 'cookie' var.

* Added generate_auth_cookie method. It needs 'nonce' var.

* Added delete_user_meta method. It needs 'cookie' and 'meta_key' var and 'meta_value' to delete.

* Added update_user_meta method. It needs 'cookie' and 'meta_key' var and 'meta_value' to update.

* Added get_user_meta method. It needs 'user_id'. 'meta_key' var is optional.

* Added xprofile method. It needs 'user_id' and any profile 'field' var.

* Added xprofile_update method. It needs 'cookie' and any profile 'field' var and 'value'.


= 1.0 =

* Added retrieve_password method. It needs user_login var.

* Added get_avatar method. It needs user_id var.

* Added get_userinfo method. It needs user_id var.



= 0.1 =

* Initial release.

==Upgrade Notice==

==Documentation==

* There are following methods available: register, get_avatar, get_userinfo, retrieve_password, validate_auth_cookie, generate_auth_cookie, get_currentuserinfo, get_user_meta, update_user_meta, delete_user_meta, xprofile, xprofile_update, fb_connect

* nonce can be created by calling if you are registering user. http://localhost/api/get_nonce/?controller=user&method=register

* You can then use 'nonce' value to register user.

= Method: fb_connect =

It needs valid 'access_token' var.

http://localhost/api/user/fb_connect/?access_token=CAACEdEose0cBADLKmcHWOZCnW4RGU8emG

= Method: validate_auth_cookie =

It needs 'cookie' var.

http://localhost/api/user/validate_auth_cookie/?cookie=admin|43089754375034fjwfn39u8

= Method: generate_auth_cookie =

It needs 'nonce' var.

First get the nonce: http://localhost/api/get_nonce/?controller=user&method=generate_auth_cookie

Then generate cookie: http://localhost/api/user/generate_auth_cookie/?nonce=375034fjwfn39u8&user_id=john&passsword=PASSWORD-HERE

= Method: delete_user_meta =

It needs 'cookie' and 'meta_key' var and 'meta_value' to delete.

http://localhost/api/user/delete_user_meta/?cookie=COOKIE-HERE&meta_key=KEY-HERE&meta_value=VALUE-HERE

= Method: update_user_meta =

It needs 'cookie' and 'meta_key' var and 'meta_value' to update.

http://localhost/api/user/update_user_meta/?cookie=COOKIE-HERE&meta_key=KEY-HERE&meta_value=VALUE-HERE

= Method: get_user_meta =

It needs 'user_id'. 'meta_key' var is optional.

http://localhost/api/user/get_user_meta/?user_id=USERID-HERE&meta_key=KEY-HERE


= Method: xprofile =

It needs 'user_id' and any profile 'field' var.

http://localhost/api/user/xprofile/?user_id=USERID-HERE&field=FIELD-LABEL-HERE

= Method: xprofile_update =

It needs 'cookie' and any profile 'field' var and 'value'.

http://localhost/api/user/xprofile_update/?cookie=COOKIE-HERE&field=FIELD-LABEL-HERE&value=VALUE=HERE

= Method: retrieve_password =

It needs user_login var.

http://localhost/api/user/retrieve_password/?user_login=john

= Method: get_avatar =

It needs user_id var.

http://localhost/api/user/get_avatar/?user_id=1

= Method: get_userinfo =

It needs user_id var.

http://localhost/api/user/get_userinfo/?user_id=1

Please check here https://github.com/pimedia/json-api-user