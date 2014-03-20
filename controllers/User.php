<?php

/*
  Controller name: User
  Controller description: User Registration and User Info controller for JSON API
  Controller Author: Ali Qureshi
  Controller Author Twitter: @parorrey
  Controller Author Website: parorrey.com

*/

class JSON_API_User_Controller {

  /**
     * Returns an Array with registered userid
     * @param String username: username to register
     * @param String email: email address for user registration
	 * @param String password: password to be set (optional)
     * @param String displayname: displayname for user
     */
   
public function register(){
	global $json_api;
	  
$username = sanitize_user( $_REQUEST['username'] );
$email = sanitize_email( $_REQUEST['email'] );
$password = sanitize_text_field( $_REQUEST['password'] );
$displayname = sanitize_text_field( $_REQUEST['display_name'] );

//Add usernames we don't want used
$invalid_usernames = array( 'admin' );
//Do username validation

$nonce_id = $json_api->get_nonce_id('user', 'register');

     if (!$username) {
		$msg = "You must include a 'username' var in your request.";
		}
	elseif (empty($_REQUEST['nonce'])) {
     $msg = "You must include a 'nonce' value. Use the 'get_nonce' Core API method.";
    }
	elseif( !wp_verify_nonce($json_api->query->nonce, $nonce_id) ) {
            $msg = "Invalid access, unverifiable 'nonce' value.";
        }
	elseif ( !$displayname ) {
  $msg = "You must include a 'display_name' var in your request.";
           }
    else {
			
	if ( !validate_username( $username ) || in_array( $username, $invalid_usernames ) ) {
  $msg = 'Username is invalid.';
        }
    elseif ( username_exists( $username ) ) {
  $msg = 'Username already exists.';
           }
				
			
	else{
		
	 if (!$email) {	
	 $msg = "You must include a 'email' var in your request with valid email address."; 
	 
	 }	
	else {
			
	if ( !is_email( $email ) ) {
    $msg = "E-mail address is invalid.";
             }
    elseif (email_exists($email)) {
    $msg = "E-mail address is already in use.";
          }			
		else {
			
			//Everything has been validated, proceed with creating the user
//Create the user

if( empty($password) ) $user_pass = wp_generate_password();
else $user_pass = $password;

$user = array(
    'user_login' => $username,
    'user_pass' => $user_pass,
    'display_name' => $displayname,
    'user_email' => $email
    );
$user_id = wp_insert_user( $user );

/*Send e-mail to admin and new user - 
You could create your own e-mail instead of using this function*/
wp_new_user_notification( $user_id, $user_pass );
	  
	 if($user_id) $msg = 'Success';
	  	
			}	
	    
		} 
		  
	 } 		
   }
		
 return array(
	      "msg" => $msg,		
		  "user_id" => $user_id	
		  ); 
		  
	  }
   
   
  public function get_avatar(){	  
	  	global $json_api;

    if (!$json_api->query->user_id) {
			$json_api->error("You must include 'user_id' var in your request. ");
		}

		preg_match('|src="(.+?)"|', get_avatar( $json_api->query->user_id, 32 ), $avatar);

      
        return $avatar;		   
	  
	  } 
	  
 public function get_userinfo(){	  
	  	global $json_api;

    if (!$json_api->query->user_id) {
			$json_api->error("You must include 'user_id' var in your request. ");
		}

		$user = get_userdata($json_api->query->user_id);

        preg_match('|src="(.+?)"|', get_avatar( $user->ID, 32 ), $avatar);		

		return array(

				"id" => $user->ID,
				"username" => $user->user_login,
				"nicename" => $user->user_nicename,
				"email" => $user->user_email,
				"url" => $user->user_url,
				"displayname" => $user->display_name,
				"firstname" => $user->user_firstname,
				"lastname" => $user->last_name,
				"nickname" => $user->nickname,
				"avatar" => $avatar[1]
		   );	   
	  
	  }   
   
public function retrieve_password(){
    global $wpdb, $json_api, $wp_hasher;

  
   if (!$json_api->query->user_login) {
			$json_api->error("You must include 'user_login' var in your request. ");
		}

    $user_login = $json_api->query->user_login;

  if ( strpos( $user_login, '@' ) ) {
        $user_data = get_user_by( 'email', trim( $user_login ) );
        if ( empty( $user_data ) )
        
	 $json_api->error("Your email address not found! ");
		
    } else {
        $login = trim($user_login);
        $user_data = get_user_by('login', $login);
    }

    // redefining user_login ensures we return the right case in the email
    $user_login = $user_data->user_login;
    $user_email = $user_data->user_email;

    do_action('retrieve_password', $user_login);

    $allow = apply_filters('allow_password_reset', true, $user_data->ID);

    if ( ! $allow )  $json_api->error("password reset not allowed! ");        
    elseif ( is_wp_error($allow) )  $json_api->error("An error occured! ");
  

    $key = wp_generate_password( 20, false );
    do_action( 'retrieve_password_key', $user_login, $key );

    if ( empty( $wp_hasher ) ) {
        require_once ABSPATH . 'wp-includes/class-phpass.php';
        $wp_hasher = new PasswordHash( 8, true );
    }
    $hashed = $wp_hasher->HashPassword( $key );
    $wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user_login ) );

    $message = __('Someone requested that the password be reset for the following account:') . "\r\n\r\n";
    $message .= network_home_url( '/' ) . "\r\n\r\n";
    $message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
    $message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
    $message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
    $message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";

    if ( is_multisite() )
        $blogname = $GLOBALS['current_site']->site_name;
    else
        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    $title = sprintf( __('[%s] Password Reset'), $blogname );

    $title = apply_filters('retrieve_password_title', $title);
    $message = apply_filters('retrieve_password_message', $message, $key);

    if ( $message && !wp_mail($user_email, $title, $message) )
       $json_api->error("The e-mail could not be sent. Possible reason: your host may have disabled the mail() function...");
	else    
  
   return array(
    "msg" => 'Link for password reset has been emailed to you. Please check your email.',
		  );	    
     } 
   
}