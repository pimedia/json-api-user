<?php

/*

  Plugin Name: JSON API User

  Plugin URI: http://www.parorrey.com/solutions/json-api-user/

  Description: Extends the JSON API for RESTful user registration, authentication, password reset, Facebook Login, user meta and BuddyPress Profile related functions. A Pro version is also available.

  Version: 3.6.0

  Author: Ali Qureshi

  Author URI: http://www.parorrey.com/

  License: GPLv3

 */

define('JAU_VERSION', '3.6.0');

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

define('JSON_API_USER_HOME', dirname(__FILE__));



if (!(is_plugin_active('json-api/json-api.php') || is_plugin_active('json-api-master/json-api.php'))) {

    add_action('admin_notices', 'pim_draw_notice_json_api');

    return;

}

function jaup_action_links( $links ) {
	
	$links[] = '<a href="'. esc_url( get_admin_url(null, '/options-general.php?page=json-api') ) .'">JSON API</a>';
  
  $links[] = '<a href="https://www.parorrey.com/solutions/json-api-user-plus/" target="_blank">' . __( 'Upgrade to User Plus', 'json-api-user' ) . '</a>';

 return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'jaup_action_links', 10, 1 );


add_filter('json_api_controllers', 'pimJsonApiController');

add_filter('json_api_user_controller_path', 'setUserControllerPath');

add_action('init', 'json_api_user_checkAuthCookie', 100);

load_plugin_textdomain('json-api-user', false, basename(dirname(__FILE__)) . '/languages');



function pim_draw_notice_json_api() {

    echo '<div id="message" class="error fade"><p style="line-height: 150%">';

    _e('<strong>JSON API User</strong></a> requires the JSON API plugin to be activated. Please <a href="https://github.com/PI-Media/json-api">install / activate JSON API</a> first from <a href="https://github.com/PI-Media/json-api" target="_blank">GitHub repository here</a>. <br>You can also upgrade to a pro verison <a href="http://www.parorrey.com/solutions/json-api-user-plus/" target="_blank">JSON API User Plus</a>', 'json-api-user');

    echo '</p></div>';

}



function pimJsonApiController($aControllers) {

    $aControllers[] = 'User';

    return $aControllers;

}



function setUserControllerPath($sDefaultPath) {

    return dirname(__FILE__) . '/controllers/User.php';

}

function json_api_user_checkAuthCookie($sDefaultPath) {
    global $json_api;

    if ($json_api->query->cookie) {
      $user_id = wp_validate_auth_cookie($json_api->query->cookie, 'logged_in');
      if ($user_id) {
        $user = get_userdata($user_id);

        wp_set_current_user($user->ID, $user->user_login);
      }
    }
}