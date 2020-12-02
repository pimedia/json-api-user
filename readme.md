=== JSON API User ===

Donate link: http://www.parorrey.com/solutions/json-api-user-plus/
Tags: json api, RESTful user registration, authentication, RESTful Facebook Login, RESTful User Meta and BuddyPress xProfile
Contributors: parorrey
Stable tag: 3.6.0
Requires at least: 3.0.1
Tested up to: 5.5.3
Requires PHP: 5.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Extends the JSON API Plugin to allow RESTful user registration, authentication and many other User Meta, BuddyPress functions. A Pro version is also available.

==Description==

JSON API User extends the JSON API Plugin with a new Controller to allow RESTful user registration, authentication, password reset, RESTful Facebook Login, RESTful User Meta and BuddyPress xProfile get and update methods. This plugin is for WordPress/Mobile app developers who want to use WordPress as mobile app data backend. 

JSON API Plugin, that is required, was closed on August 7, 2019 from WordPress repository. You can download <a href="https://github.com/PI-Media/json-api">JSON API Plugin</a> from https://github.com/PI-Media/json-api until it is republished and available on WordPress.

Features include:

* Generate Auth Cookie for user authentication
* Validate Auth Cookie
* RESTful User Registration
* RESTful Facebook Login/Registration with valid access_token
* RESTful BuddyPress xProfile fields update
* Get User Meta and xProfile fields
* Update User Meta and xProfile fields
* Delete User Meta
* Password Reset
* Get Avatar
* Get User Info
* Post Comment

The plugin was created for mobile apps integration with the web app using WordPress as backend for all the data. WordPress helped in putting together the web app quickly and then Mobile iOS and Android apps were integrated via this plugin. There were some app specific customized methods which are not included but rest have been made generic for community usage. 

My other JSON API Auth plugin has also been integrated with this plugin from version 1.1 because most endpoints required user authentication via cookie for data update.

<b>Pro Version - JSON API User Plus</b>

A pro version of this plugin, <a href="http://www.parorrey.com/solutions/json-api-user-plus/">JSON API User Plus</a>, is available here http://www.parorrey.com/solutions/json-api-user-plus/ that supports BuddyPress Messages component, BuddyPress avatar upload, BuddyPress Extended Profile, BuddyPress Groups, BuddyPress Friends, BuddyPress Activity, BuddyPress Notifications, BuddyPres Settings and other BuddyPress related functions to integrate BuddyPress features in your mobile app via REST api.

<a href="http://www.parorrey.com/solutions/json-api-user-plus/">JSON API User Plus</a> includes API key which protects and restricts the endpoint calls. This key can be updated from Settings > User Plus options page. Your app must include this key with every call to get the data from REST API. Please see documentation for calling endpoints examples for 'JSON API User Plus'.

JSON API User Plus features include:

* Generate Auth Cookie for user authentication
* Validate Auth Cookie
* RESTful User Registration
* RESTful Facebook Login/Registration with valid access_token
* RESTful BuddyPress xProfile fields update
* Get User Meta and xProfile fields
* Update User Meta and xProfile fields
* Delete User Meta
* Password Reset
* Get/Upload Avatar
* Get User Info
* Post Comment
* Add Post, Update Post, Delete Post
* Add/Edit/Delete Custom Post Type, Custom Fields
* Search User
* BuddyPress Activities
* BuddyPress Members
* BuddyPress Friends
* BuddyPress Notifications
* BuddyPress Settings
* & many more


== Installation ==

First you have to install the JSON API for WordPress Plugin (http://wordpress.org/extend/plugins/json-api/installation/).

To install JSON API User just follow these steps:

* Upload the folder "json-api-user" to your WordPress plugin folder (/wp-content/plugins)
* Activate the plugin through the 'Plugins' menu in WordPress or by using the link provided by the plugin installer
* Activate the controller through the JSON API menu found in the WordPress admin center (Settings -> JSON API)

== Screenshots ==

1. Call to generate_auth_cookie endpoint using Postman
2. Call to get_currentuserinfo endpoint using Postman
3. Call to validate_auth_cookie endpoint using Postman


== Frequently Asked Questions ==

* There are following methods available: register, get_avatar, get_userinfo, retrieve_password, validate_auth_cookie, generate_auth_cookie, get_currentuserinfo, get_user_meta, update_user_meta, delete_user_meta, xprofile, xprofile_update, fb_connect

* nonce can be created by calling if you are registering user. http://localhost/api/get_nonce/?controller=user&method=register

* You can then use 'nonce' value to register user.

* Always use POST method and not GET method to submit data, following url examples of GET method are only for demonstration purposes.

= Method: info =

http://localhost/api/user/info/

This returns plugin version.

= Method: register =

http://localhost/api/user/register/?username=john&email=john@domain.com&nonce=8bdfeb4e16&display_name=John&notify=both

To register user & get valid cookie for 100 seconds:
http://localhost/api/user/register/?username=john&email=john@domain.com&display_name=John&notify=both&seconds=100

Optional fields: 'user_pass', 'user_nicename', 'user_url', 'nickname', 'first_name', 'last_name', 'description', 'rich_editing', 'user_registered', 'jabber', 'aim', 'yim', 'comment_shortcuts', 'admin_color', 'use_ssl', 'show_admin_bar_front'. 

Please make sure you provide valid values that these fields expect in correct format.

To disbale registration email notification to user:

http://localhost/api/user/register/?username=john&email=john@domain.com&nonce=8bdfeb4e16&display_name=John&notify=no

To register with user provided password:

http://localhost/api/user/register/?username=john&email=john@domain.com&nonce=8bdfeb4e16&display_name=John&notify=both&user_pass=YOUR-PASSWORD

To add custom fields for user profile, send 'custom_fields' named associative array, populated with standard 'meta_key' as index and 'meta_value' as its value. For example, you need to add address and phone for any user, populate custom_fields[address] index with address value and custom_fields[phone] with hpone value. 

You can add any number of custom fields. These are stored as meta_key and meta_value in user_meta WordPress table. You can access these values in WordPress using get_user_meta() fucntion. 

You can also get these values with get_user_meta endpoint, see below this endpoint documentation.


= Method: fb_connect =

It needs valid 'access_token' var.

http://localhost/api/user/fb_connect/?access_token=CAACEdEose0cBADLKmcHWOZCnW4RGU8emG

Provide valid access_token with email extended permission. To generate test access_token, try this tool https://developers.facebook.com/tools/explorer/ and select the app from above drop down that you want to get access_token (You must have joined that app already with email permission to generate access_token) for and then select email from the fields. By default, only 'id' and 'name' are added but you need to include 'email' for user identification.

You will have to first allow extended permission for email in the app joining screen. Please note that above tool is only for testing, you generate valid access_token using the Graph API in your app. You should know Facebook Graph API to use this endpoint.

= Method: validate_auth_cookie =

It needs 'cookie' var.

http://localhost/api/user/validate_auth_cookie/?cookie=admin|43089754375034fjwfn39u8

= Method: generate_auth_cookie =

It needs `username`, `password` vars. `seconds` is optional.

First get the nonce: http://localhost/api/get_nonce/?controller=user&method=generate_auth_cookie

Then generate cookie: http://localhost/api/user/generate_auth_cookie/?username=john&password=PASSWORD-HERE

Optional 'seconds' var. It provided, generated cookie will be valid for that many seconds, otherwise default is for 14 days.

generate cookie for 1 minute: http://localhost/api/user/generate_auth_cookie/?username=john&password=PASSWORD-HERE&seconds=60

60 means 1 minute.

= Method: delete_user_meta =

It needs 'cookie' and 'meta_key' var and 'meta_value' to delete.

http://localhost/api/user/delete_user_meta/?cookie=COOKIE-HERE&meta_key=KEY-HERE&meta_value=VALUE-HERE

= Method: update_user_meta =

It needs 'cookie' and 'meta_key' var and 'meta_value' to update. You must include a 'meta_value' var in your request. If you have multiple values for any meta_key, you must send it as an array meta_value[] in POST method. 

http://localhost/api/user/update_user_meta/?cookie=COOKIE-HERE&meta_key=KEY-HERE&meta_value=VALUE-HERE

= Method: update_user_meta_vars =

It needs 'cookie' and any user meta variables. This endpoint allows you cut http requests if you have to add/update more than one user_meta field at a time.

http://localhost/api/user/update_user_meta_vars/?cookie=COOKIE-HERE&website=user-website.com&city=Chicago&country=USA

In the above endpoint call, website, city, country are meta_key for WordPress user_meta. It is different from BuddyPress xProfile fields.

If you have multiple values for any variable, you must send it as an array i.e. variable_name[] in POST method. For instance, you have skills variable with multiple values, you will send a skills[] array with its values using POST method.

= Method: get_user_meta =

It needs 'user_id'. 'meta_key' var is optional.

http://localhost/api/user/get_user_meta/?cookie=COOKIE-HERE&meta_key=KEY-HERE


= Method: xprofile =

It needs 'user_id' and any profile 'field' var.

http://localhost/api/user/xprofile/?user_id=USERID-HERE&field=FIELD-LABEL-HERE

= Method: xprofile_update =

It needs 'cookie' and any profile 'field' var and 'value'.

http://localhost/api/user/xprofile_update/?cookie=COOKIE-HERE&exact-xprofile-field-label=value

http://localhost/api/user/xprofile_update/?cookie=COOKIE-HERE&field=value&field2=value&multi-value-field=value1,value2,value3

Please make sure you provide ending comma for all those fields which have multiple values. e.g. If 'skills' xProfile field has multiple values, pass them like 
http://localhost/api/user/xprofile_update/?cookie=COOKIE-HERE&skills=PHP,MySQL, or &skills=PHP, make sure you always pass ending comma for multi-select fields to be added in array format.

= Method: retrieve_password =

It needs user_login var.

http://localhost/api/user/retrieve_password/?user_login=john

= Method: get_avatar =

It needs user_id var.

http://localhost/api/user/get_avatar/?user_id=1

= Method: get_userinfo =

It needs user_id var.

http://localhost/api/user/get_userinfo/?user_id=1

= Method: post_comment =

It needs 'cookie', 'post_id', 'content', 'comment_status' vars.

http://localhost/api/user/post_comment/?cookie=COOKIE-HERE&post_id=ID&content=Comment contents here&comment_status=1

For additional endpoints, pro version JSON API User Plus plugin details check here https://www.parorrey.com/solutions/json-api-user-plus/


== Changelog ==

= 3.6.0 =
* Updated for wordpress version.

= 3.5.0 =
* Updated update_user_meta endpoint for array variable values.
* Updated update_user_meta_vars endpoint for array variable values.   

= 3.4.0 =
* Updated for hash cookie in response to register endpoint.
* Added 'custom_fields' for user profile register endpoint, 'custom_fields' named array with 'meta_key' and 'meta_value' will be added in user profile.   

= 3.3.0 =
* Updated for hash cookie in response to generate_auth_cookie endpoint.
* Updated and included cookie, cookie name, hashed cookie and user_login info in response to registration endpoint.
* 'display_name' variable is not required anymore in 'regstration' endpoint.

= 3.2.0 =
* Updated for JSON API Plugin diretory check error and updated action links.

= 3.1.3 =
* Fixed action links