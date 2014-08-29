<?php

/*
  Controller name: User
  Controller description: User Registration, Authentication, User Info, User Meta, FB Login, BuddyPress xProfile Fields methods
  Controller Author: Ali Qureshi
  Controller Author Twitter: @parorrey
  Controller Author Website: parorrey.com
  
*/
class JSON_API_User_Controller {

  /**
     * Returns an Array with registered userid & valid cookie
     * @param String username: username to register
     * @param String email: email address for user registration
	 * @param String user_pass: user_pass to be set (optional)
     * @param String display_name: display_name for user
     */   

public function register(){

	global $json_api;	  

   if (!$json_api->query->username) {
			$json_api->error("You must include 'username' var in your request. ");
		}
	else $username = sanitize_user( $json_api->query->username );
	
 
  if (!$json_api->query->email) {
			$json_api->error("You must include 'email' var in your request. ");
		}
	else $email = sanitize_email( $json_api->query->email );

 if (!$json_api->query->nonce) {
			$json_api->error("You must include 'nonce' var in your request. Use the 'get_nonce' Core API method. ");
		}
 else $nonce =  sanitize_text_field( $json_api->query->nonce ) ;
 
 if (!$json_api->query->display_name) {
			$json_api->error("You must include 'display_name' var in your request. ");
		}
	else $display_name = sanitize_text_field( $json_api->query->display_name );

$user_pass = sanitize_text_field( $_REQUEST['user_pass'] );

//Add usernames we don't want used

$invalid_usernames = array( 'admin' );

//Do username validation

$nonce_id = $json_api->get_nonce_id('user', 'register');

 if( !wp_verify_nonce($json_api->query->nonce, $nonce_id) ) {

    $json_api->error("Invalid access, unverifiable 'nonce' value. Use the 'get_nonce' Core API method. ");
        }

 else {

	if ( !validate_username( $username ) || in_array( $username, $invalid_usernames ) ) {

  $json_api->error("Username is invalid.");
  
        }

    elseif ( username_exists( $username ) ) {

    $json_api->error("Username already exists.");

           }			

	else{


	if ( !is_email( $email ) ) {
   	 $json_api->error("E-mail address is invalid.");
             }
    elseif (email_exists($email)) {

	 $json_api->error("E-mail address is already in use.");

          }			

else {

	//Everything has been validated, proceed with creating the user

//Create the user

if( !isset($_REQUEST['user_pass']) ) {
	 $user_pass = wp_generate_password();
	 $_REQUEST['user_pass'] = $user_pass;
}

 $_REQUEST['user_login'] = $username;
 $_REQUEST['user_email'] = $email;

$allowed_params = array('user_login', 'user_email', 'user_pass', 'display_name', 'user_nicename', 'user_url', 'nickname', 'first_name',
                         'last_name', 'description', 'rich_editing', 'user_registered', 'role', 'jabber', 'aim', 'yim',
						 'comment_shortcuts', 'admin_color', 'use_ssl', 'show_admin_bar_front'
                   );


foreach($_REQUEST as $field => $value){
		
	if( in_array($field, $allowed_params) ) $user[$field] = trim(sanitize_text_field($value));
	
    }

$user_id = wp_insert_user( $user );

/*Send e-mail to admin and new user - 
You could create your own e-mail instead of using this function*/

if($user_id) wp_new_user_notification( $user_id, $user_pass );	  

			}
		} 
   }

	
	$expiration = time() + apply_filters('auth_cookie_expiration', 1209600, $user_id, true);

	$cookie = wp_generate_auth_cookie($user_id, $expiration, 'logged_in');

 return array( 
          "cookie" => $cookie,	
		  "user_id" => $user_id	
		  ); 		  

  }   

public function get_avatar(){	  

	  	global $json_api;

if (function_exists('bp_is_active')) {	

    if (!$json_api->query->user_id) {
			$json_api->error("You must include 'user_id' var in your request. ");
		}
		
	  if (!$json_api->query->type) {
			$json_api->error("You must include 'type' var in your request. possible values 'full' or 'thumb' ");
		}

		
$avatar	= bp_core_fetch_avatar ( array( 'item_id' => $json_api->query->user_id, 'type' => $json_api->query->type, 'html'=>false ));

        return array('avatar'=>$avatar);	
   } else {
	  
	  $json_api->error("You must install and activate BuddyPress plugin to use this method.");
	  
	  }
  
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

public function validate_auth_cookie() {

		global $json_api;

		if (!$json_api->query->cookie) {

			$json_api->error("You must include a 'cookie' authentication cookie. Use the `create_auth_cookie` method.");

		}		

    	$valid = wp_validate_auth_cookie($json_api->query->cookie, 'logged_in') ? true : false;

		return array(

			"valid" => $valid

		);

	}

public function generate_auth_cookie() {

		global $json_api;


		$nonce_id = $json_api->get_nonce_id('user', 'generate_auth_cookie');

		if (!wp_verify_nonce($json_api->query->nonce, $nonce_id)) {

			$json_api->error("Your 'nonce' value was incorrect. Use the 'get_nonce' API method.");

		}


		if (!$json_api->query->username) {

			$json_api->error("You must include a 'username' var in your request.");

		}


		if (!$json_api->query->password) {

			$json_api->error("You must include a 'password' var in your request.");
		}else $password	= 	$json_api->query->password;
//d($password);

 $user = wp_authenticate($json_api->query->username, $password);




    	if (is_wp_error($user)) {

    		$json_api->error("Invalid username and/or password.", 'error', '401');

    		remove_action('wp_login_failed', $json_api->query->username);

    	}



    	$expiration = time() + apply_filters('auth_cookie_expiration', 1209600, $user->ID, true);

    	$cookie = wp_generate_auth_cookie($user->ID, $expiration, 'logged_in');


		preg_match('|src="(.+?)"|', get_avatar( $user->ID, 32 ), $avatar);		

  	

		return array(

			"cookie" => $cookie,

			"user" => array(

				"id" => $user->ID,

				"username" => $user->user_login,

				"nicename" => $user->user_nicename,

				"email" => $user->user_email,

				"url" => $user->user_url,

				"registered" => $user->user_registered,

				"displayname" => $user->display_name,

				"firstname" => $user->user_firstname,

				"lastname" => $user->last_name,

				"nickname" => $user->nickname,

				"description" => $user->user_description,

				"capabilities" => $user->wp_capabilities,

				"avatar" => $avatar[1]

			),

		);

	}

public function get_currentuserinfo() {

		global $json_api;

		if (!$json_api->query->cookie) {

			$json_api->error("You must include a 'cookie' var in your request. Use the `generate_auth_cookie` Auth API method.");

		}
		
		$user_id = wp_validate_auth_cookie($json_api->query->cookie, 'logged_in');
		

		if (!$user_id) {
			$json_api->error("Invalid authentication cookie. Use the `generate_auth_cookie` method.");
		}

		$user = get_userdata($user_id);

        preg_match('|src="(.+?)"|', get_avatar( $user->ID, 32 ), $avatar);

		

		return array(

			"user" => array(

				"id" => $user->ID,

				"username" => $user->user_login,

				"nicename" => $user->user_nicename,

				"email" => $user->user_email,

				"url" => $user->user_url,

				"registered" => $user->user_registered,

				"displayname" => $user->display_name,

				"firstname" => $user->user_firstname,

				"lastname" => $user->last_name,

				"nickname" => $user->nickname,

				"description" => $user->user_description,

				"capabilities" => $user->wp_capabilities,

				"avatar" => $avatar[1]

			)

		);

	}	
	  
public function get_user_meta() {
	 
	  global $json_api;
	  
	  if (!$json_api->query->user_id) {
			$json_api->error("You must include a 'user_id' var in your request.");
		}
		else $user_id = $json_api->query->user_id;
	
 $meta_key = sanitize_text_field($json_api->query->meta_key);	
  
		  
		if($meta_key) $data[$meta_key] = get_user_meta(  $user_id, $meta_key);
		else $data = get_user_meta(  $user_id);
//d($data);
	   return $data;
	    
	  
	  }
	  
public function update_user_meta() {
	 
	  global $json_api;
	  
	   if (!$json_api->query->cookie) {
			$json_api->error("You must include a 'cookie' var in your request. Use the `generate_auth_cookie` method.");
		}

		$user_id = wp_validate_auth_cookie($json_api->query->cookie, 'logged_in');

	if (!$user_id) 	$json_api->error("Invalid cookie. Use the `generate_auth_cookie` method.");
		
		
   if (!$json_api->query->meta_key) $json_api->error("You must include a 'meta_key' var in your request.");
		
		else $meta_key = $json_api->query->meta_key;	
  
   if (!$json_api->query->meta_value) {
			$json_api->error("You must include a 'meta_value' var in your request.");
		}
		else $meta_value = sanitize_text_field($json_api->query->meta_value);
  
		  
		$data['updated'] = update_user_meta(  $user_id, $meta_key, $meta_value);
		

	   return $data;	    
	  
	  }
	  
public function delete_user_meta() {
	 
	  global $json_api;
	  
	   if (!$json_api->query->cookie) {
			$json_api->error("You must include a 'cookie' var in your request. Use the `generate_auth_cookie` method.");
		}

		$user_id = wp_validate_auth_cookie($json_api->query->cookie, 'logged_in');

	if (!$user_id) 	$json_api->error("Invalid cookie. Use the `generate_auth_cookie` method.");
		
		
   if (!$json_api->query->meta_key) $json_api->error("You must include a 'meta_key' var in your request.");
		
		else $meta_key = $json_api->query->meta_key;	
  
   if (!$json_api->query->meta_value) {
			$json_api->error("You must include a 'meta_value' var in your request.");
		}
		else $meta_value = sanitize_text_field($json_api->query->meta_value);
  

		$data['deleted'] = delete_user_meta(  $user_id, $meta_key, $meta_value);
		
	   return $data;	    
	  
	  }
	  
public function xprofile() {
	 
	  global $json_api;
	  
if (function_exists('bp_is_active')) {	

	  if (!$json_api->query->user_id) {
			$json_api->error("You must include a 'user_id' var in your request.");
		}
		else $user_id = $json_api->query->user_id;
	
		
   if (!$json_api->query->field) {
			$json_api->error("You must include a 'field' var in your request. Use 'field=default' for all default fields.");
		}
	  elseif ($json_api->query->field=='default') {
			$field_label='First Name, Last Name, Bio';/*you should add your own field labels here for quick viewing*/
		}	
		else $field_label = sanitize_text_field($json_api->query->field);	
  
  
  $fields = explode(",", $field_label);
  
  if(is_array($fields)){
	  
	  foreach($fields as $k){
		  
		  $fields_data[$k] = xprofile_get_field_data( $k, $user_id );
		  
		  }
	
	   return $fields_data;
	    
	  
	  }
	
   }
   
  else {
	  
	  $json_api->error("You must install and activate BuddyPress plugin to use this method.");
	  
	  }

  }

public function xprofile_update() {
	 
	  global $json_api;	

if (function_exists('bp_is_active')) {	
	
	  if (!$json_api->query->cookie) {
			$json_api->error("You must include a 'cookie' var in your request. Use the `generate_auth_cookie` method.");
		}

		$user_id = wp_validate_auth_cookie($json_api->query->cookie, 'logged_in');
//	echo '$user_id: '.$user_id;	
	
		if (!$user_id) {
			$json_api->error("Invalid cookie. Use the `generate_auth_cookie` method.");
		}


foreach($_REQUEST as $field => $value){
		
	if($field=='cookie') continue;
	
	$field_label = str_replace('_',' ',$field);
	
	if( strpos($value,',') !== false ) {
		$values = explode(",", $value);
	   $values = array_map('trim',$values);
	   }
	else $values = trim($value);
	//echo 'field-values: '.$field.'=>'.$value;
	//d($values);
  
  $result[$field_label]['updated'] = xprofile_set_field_data( $field_label,  $user_id, $values, $is_required = true ); 
  
}

	 return $result;
   }
   
  else {
	  
	  $json_api->error("You must install and activate BuddyPress plugin to use this method.");
	  
	  }

  }  
  
public function fb_connect(){
	  
	    global $json_api;
		
		if ($json_api->query->fields) {

			$fields = $json_api->query->fields;

		}else $fields = 'id,name,first_name,last_name,email';
		
		if ($json_api->query->ssl) {
			 $enable_ssl = $json_api->query->ssl;
		}else $enable_ssl = true;
	
	if (!$json_api->query->access_token) {
			$json_api->error("You must include a 'access_token' variable. Get the valid access_token for this app from Facebook API.");
		}else{
			
$url='https://graph.facebook.com/me/?fields='.$fields.'&access_token='.$json_api->query->access_token;
	
	//  Initiate curl
$ch = curl_init();
// Enable SSL verification
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $enable_ssl);
// Will return the response, if false it print the response
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Set the url
curl_setopt($ch, CURLOPT_URL,$url);
// Execute
$result=curl_exec($ch);
// Closing
curl_close($ch);

	$result = json_decode($result, true);
	
   if(isset($result["email"])){
          
            $user_email = $result["email"];
           	$email_exists = email_exists($user_email);
			
			if($email_exists) {
				$user = get_user_by( 'email', $user_email );
			  $user_id = $user->ID;
			  $user_name = $user->user_login;
			 }
			
         
		   
		    if ( !$user_id && $email_exists == false ) {
				
			  $user_name = strtolower($result['first_name'].'.'.$result['last_name']);
               				
				while(username_exists($user_name)){		        
				$i++;
				$user_name = strtolower($result['first_name'].'.'.$result['last_name']).'.'.$i;			     
	
					}
				
			 $random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
      		   $userdata = array(
                           'user_login'    => $user_name,
						   'user_email'    => $user_email,
                           'user_pass'  => $random_password,
						   'display_name'  => $result["name"],
						   'first_name'  => $result['first_name'],
						   'last_name'  => $result['last_name']
                                     );

                   $user_id = wp_insert_user( $userdata ) ;				   
				 if($user_id) $user_account = 'user registered.';
				 
            } else {
				
				 if($user_id) $user_account = 'user logged in.';
				}
			
			 $expiration = time() + apply_filters('auth_cookie_expiration', 1209600, $user_id, true);
    	     $cookie = wp_generate_auth_cookie($user_id, $expiration, 'logged_in');
        
		$response['msg'] = $user_account;
		$response['wp_user_id'] = $user_id;
		$response['cookie'] = $cookie;
		$response['user_login'] = $user_name;	
		
		}
		else {
			$response['msg'] = "Your 'access_token' did not return email of the user. Without 'email' user can't be logged in or registered. Get user email extended permission while joining the Facebook app.";

			}
	
	}	

return $response;
	  
	  }
 
 }
 
 