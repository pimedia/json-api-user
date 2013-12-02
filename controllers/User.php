<?php

/*
  Controller name: JSON API User
  Controller description: User Registration controller for JSON APi
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
   
   
}
