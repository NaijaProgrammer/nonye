<?php
class UserAuth
{
	/*
	* required $data members:
	* email string
	* password string
	*/
	public static function register_user($data)
	{  
		$decoded_data = self::_decode_supplied_user_data_($data);
		$signup_data  = array();
		
		foreach($decoded_data AS $key => $value)
		{
			if(Util::is_scalar($value))
			{
				$signup_data[$key] = is_string($value) ? trim($value) : $value;
			}
		}
		
		extract($signup_data);
		
		$validate = Validator::validate(array(
			array( 'error_condition'=>empty($email), 'error_message'=>'Fill in the email field'),
			array( 'error_condition'=>!is_valid_email($email), 'error_message'=>'Enter a valid email' ),
			array( 'error_condition'=>UserModel::user_exists($email), 'error_message'=>'The email you are trying to use is not available' ),
			array( 'error_condition'=>empty($password), 'error_message'=>'Enter your password' )
		));
		
		if($validate['error'])
		{
			$response_data = array('error'=>true, 'message'=>DataSanitizer::escape_output_string($validate['status_message']));
		}
			
		else
		{
			$signup_data['login'] = $email;
			$registrant_id        = UserModel::process_user_registration($signup_data);
			
			if($registrant_id)
			{
				$site_name = get_app_setting('site-name');
				assign_role_to_user($registrant_id, 'User');
				
				if(empty($username))
				{
					$username = generate_username($registrant_id);
				}
				
				$mail_subject = 'Welcome to '. $site_name;
				$mail_message = get_mail_message('registration-success-message', array('username'=>$username)); //get_app_setting('registration-success-message'); //get_registration_success_message($signup_data);
				
				send_email(array(
					'to'      => $email,
					'from'    => $site_name. ' <'. get_app_setting('registration-success-mail-sender'). '>',
					'subject' => $mail_subject,
					'message' => generate_html_mail( array('title'=>$mail_subject, 'message'=>$mail_message) )
				));
				
				$response_data = array('success'=>true, 'userID'=>$registrant_id, 'userEmail'=>$email, 'userLogin'=>$email);
				
				if( is_development_server() )
				{
					$response_data['message'] = escape_output_string($mail_message);
				}
			}
			
			else
			{
				$response_data = array('error'=>true, 'status_message'=>'There was a problem processing your request. <br/>Please try again.');
			}
		}
		
		return $response_data;
	}
	
	public static function login_user($data)
	{
		$decoded_data = self::_decode_supplied_user_data_($data);
		
		extract($decoded_data);
		
		$login = UserModel::login_user(array(
			"user_login"                   => $userLogin, 
			"user_password"                => $userPassword, 
			"remember_user"                => $rememberUser, 
			"redirect_on_successful_login" => false, 
			"login_landing_page"           => "",
            "empty_login_field_error"      => $emptyLoginFieldError, 
			"empty_password_field_error"   => $emptyPasswordFieldError,
			"unverified_account_error"     => $unverifiedAccountError
		));
		
		$response_data = (is_array($login) && $login['login_error']) ? array('error'=>true, 'message'=>$login['login_error_message']) : array('success'=>true);
		
		return $response_data;
	}
	
	public static function process_password_recovery($user_email)
	{   
		$validate = Validator::validate(array(
			array('error_condition'=>UserModel::user_is_logged_in(), 'error_message'=>'You are already logged in'),
			array('error_condition'=>!is_valid_email($user_email), 'error_message'=>'Invalid email entered'),
			array('error_condition'=>!email_exists($user_email), 'error_message'=>'No such email exists in our records')
		));
		
		if($validate['error'])
		{
			$response_data = array('error'=>true, 'message'=>$validate['status_message']);
		}
		
		else
		{
			$message = get_mail_message('password-recovery-mail', array(
				'user_email'         => $user_email,
				'password_reset_url' => generate_url( array('controller'=>'users', 'action'=>'password-reset') ),
				'nonce'              => UserModel::generate_password_recovery_nonce($user_email)
			));
			
			/*
			$nonce = UserModel::generate_password_recovery_nonce($user_email);
			$message =  get_app_setting('password-recovery-mail'); //get_password_recovery_mail_message($user_email);
			$message = str_ireplace( '{{user_email}}', $user_email, $message);
			$message = str_ireplace( '{{site_name}}', get_site_name(), $message);
			$message = str_ireplace( '{{password_reset_url}}', generate_url( array('controller'=>'users', 'action'=>'password-reset') ), $message );
			$message = str_ireplace( '{{nonce}}', $nonce, $message );
			*/
			
			$subject = 'Password Reset Instructions';
			
			send_email(array(
				'to'      => $user_email,
				'from'    => get_site_name().  ' <'. get_app_setting('password-recovery-mail-sender'). '>',
				'subject' => $subject,
				'message' => generate_html_mail( array('title'=>$subject, 'message'=>$message) )
			));
			
			if( is_development_server() )
			{
				$response_data = array( 'success'=>true, 'message'=>escape_output_string($message) );
			}
			else
			{
				$response_data = array( 'success'=>true );
			}
		}
		
		return $response_data;
	}

	/*
	* $data members:
	* string user_pnonce the user nonce as previously generated - when the user filled in the forgot password form - and stored in the database 
	* (this helps verify that the request is legit)
	* string new_password the new password to reset user's password to
	* NOTE:
	* The user_pnonce is a stringified array holding the associated email and the true user nonce.
	* This is unstringified and the array unpacked to retrieve the associated values
	*/
	public static function process_password_reset($data)
	{
		$validate = Validator::validate(array(
			array( 'error_condition'=>!isset($data['user_pnonce']), 'error_message'=>'Invalid operation requested', 'error_type'=>'invalidOperation' ),
			array( 'error_condition'=>!UserModel::verify_password_recovery_nonce( $data['user_pnonce'] ), 'error_message'=>'Operation has expired', 'error_type'=>'nonceExpiry'),
			array( 'error_condition'=>empty($data['new_password']), 'error_message'=>'The password field is empty', 'error_type'=>'emptyPasswordField'),
		));
		
		if($validate['error'])
		{
			$response_data = array('error'=>true, 'message'=>$validate['error_message']);
			if( isset($validate['error_type']) )
			{
				$response_data['errorType'] = $validate['error_type'];
			}
		}
		
		else
		{
			$stringified_nonce = $data['user_pnonce'];
			$new_password      = $data['new_password'];

			$data_array = UserModel::extract_password_recovery_nonce_data($stringified_nonce);
			$user_email = $data_array['user_email'];
			$user_data  = get_users_by( array('email'=>$user_email) );
			
			$user_id    = $user_data['id']; //UserModel::get_user_id($user_email);
			update_user_data($user_id, array('password'=>$new_password,  'pnonce'=>'null') );

			$response_data = array('success'=>true);
		}
		
		return $response_data;
	}
	
	private static function _decode_supplied_user_data_($data)
	{
		/*
		* When coming from an Ajax request, the data is JSON-encoded
		* But when coming from the 3rd party auth services, our data is an array
		*/
		$decoded_data = is_string($data) ? json_decode($data, true) : $data;
		return $decoded_data;
	}
}