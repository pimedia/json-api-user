<?php

/*
  Controller name: User
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
   
public function update_user(){
	global $json_api;
	  
	$uid = $_REQUEST['userid'];
	$nonce_id = $json_api->get_nonce_id('user', 'update_user');


	//All non empty variables will be added to the associative array that will be passed to the wordpress object

	$user_pass = $_REQUEST['user_pass'];
	$user_login = $_REQUEST['user_login'];
	$user_nicename = $_REQUEST['user_nicename'];
	$user_url = $_REQUEST['user_url'];
	$user_email = $_REQUEST['user_email'];
	$display_name = $_REQUEST['display_name'];
	$nickname = $_REQUEST['nickname'];
	$first_name = $_REQUEST['first_name'];
	$last_name = $_REQUEST['last_name'];
	$description = $_REQUEST['description'];
	$user_registered = $_REQUEST['user_registered'];
	$role = $_REQUEST['role'];
	$jabber = $_REQUEST['jabber'];
	$aim = $_REQUEST['aim'];
	$yim = $_REQUEST['yim'];

	if(empty($_REQUEST['nonce'])) {
     $msg = "You must include a 'nonce' value. Use the 'get_nonce' Core API method.";
    }
	elseif( !wp_verify_nonce($json_api->query->nonce, $nonce_id) ) {
            $msg = "Invalid access, unverifiable 'nonce' value.";
    }elseif(empty($uid)) {
            $msg = "userid cannot be empty";
    }

    //To check that atleast one meta/data is requested to be updated
    if(!empty($user_pass) || !empty($user_login) || !empty($user_nicename) || !empty($user_url) || !empty($user_email)
    	|| !empty($display_name) || !empty($nickname) || !empty($first_name) || !empty($last_name) || !empty($description)
    	|| !empty($user_registered) || !empty($role) || !empty($jabber) || !empty($aim) || !empty($yim)){

    	$user_updates = array('ID' => $uid);

    	if(!empty($user_pass)){
    		$user_updates['user_pass'] = $user_pass;
    	}
    	if(!empty($user_login)){
    		$user_updates['user_login'] = $user_login;
    	}
    	if(!empty($user_nicename)){
    		$user_updates['user_nicename'] = $user_nicename;
    	}
    	if(!empty($user_url)){
    		$user_updates['user_url'] = $user_url;
    	}
    	if(!empty($user_email)){
    		$user_updates['user_email'] = $user_email;
    	}
    	if(!empty($display_name)){
    		$user_updates['display_name'] = $display_name;
    	}
    	if(!empty($nickname)){
    		$user_updates['nickname'] = $nickname;
    	}
    	if(!empty($first_name)){
    		$user_updates['first_name'] = $first_name;
    	}
    	if(!empty($last_name)){
    		$user_updates['last_name'] = $last_name;
    	}
    	if(!empty($description)){
    		$user_updates['description'] = $description;
    	}
    	if(!empty($first_name)){
    		$user_updates['user_registered'] = $user_registered;
    	}
    	if(!empty($first_name)){
    		$user_updates['role'] = $role;
    	}
    	if(!empty($first_name)){
    		$user_updates['jabber'] = $jabber;
    	}
    	if(!empty($first_name)){
    		$user_updates['aim'] = $aim;
    	}
    	if(!empty($first_name)){
    		$user_updates['yim'] = $yim;
    	}

    	//call wp_update_user

    	$response_user_id = wp_update_user($user_updates) ;

    	if(is_wp_error($response_user_id)){
    		$code = "1";
    		$msg = "WordPress error encountered.";
    	}else{
    		$code = "0";
    		$msg = "Updates were successfull.";
    	}
    }else{
    	    $code = "-1";
    		$msg = "you have not requested to update any user meta/data";
    }

 return array(
	      "code" => $code,
	      "msg" => $msg,
	      "user_id" => $response_user_id
	         ); 

	}
   
}
