<?php
require SITE_DIR. '/lib/auth-service/auth-service.class.php';

$auth_provider = 'facebook';
$auth_service  = new AuthService(array(
	'service_provider'          => $auth_provider,
	'app_id'                    => get_external_app_data($auth_provider)['key'],
	'app_secret'                => get_external_app_data($auth_provider)['secret'],
	'access_token_session_name' => 'facebook_access_token',
	'permissions'               => get_external_app_data($auth_provider)['permissions'],
	'auth_callback_url'         => SITE_URL. '/user-auth/facebook/',
));

if( isset($_GET['code']) )
{
	//successful authentication/authorization
	//the $_GET['code'] is used internally by the $auth_service object (in the call to get_user_data())
	
	$user_data   = $auth_service->get_user_data();
	$firstname   = $user_data['firstname'];
	$lastname    = $user_data['lastname'];
	$email       = $user_data['email'];
	$sex         = $user_data['gender'];
	$profile_url = $user_data['profile_url'];
	$pass        = generate_user_social_password($email, $auth_provider);
	
	if( !UserModel::user_exists($email) )
	{
		$signup_data = UserAuth::register_user(array( 
			'signup-type'     => 'third-party-authorization', 
			'auth-provider'   => $auth_provider, 
			'email'           => $email, 
			'password'        => $pass,
			'firstname'       => $firstname, 
			'lastname'        => $lastname,
			'google-plus-url' => $profile_url
		));
	}
	
	$login_data = UserAuth::login_user(array(
		'loginType'               => 'third-party-authorization',
		'authProvider'            => $auth_provider,
		'userLogin'               => $email, 
		'userPassword'            => $pass, 
		'rememberUser'            => true, 
		'unverifiedAccountError'  => 'Invalid account details. Please try again',
		'emptyLoginFieldError'    => '', 
		'emptyPasswordFieldError' => '',
		'unverifiedAccountError'  => ''
	));
	
	$json_data =  json_encode($login_data, true);
	
	if( isset($json_data['error']) )
	{
		$status = 'error';
		$message = $json_data['message'];
	}
	else
	{
		$status = 'success';
		$message = '';
	}
	
	$js_str = ''.
	'<script>'.
	'var opener = (typeof window.opener !== "undefined") ? window.opener : null;'.
	'if( opener && (typeof opener.handleThirdPartyAuth === "function") )'.
	'{'.
		'opener.handleThirdPartyAuth({"provider":"'. $auth_provider. '", "status":"'. $status. '", "message":"'. $message. '"});'.
	'}'.
	'self.close();'.
	'</script>';
	
	echo $js_str;
	
	exit;
}

header("Location: ". $auth_service->get_auth_url() ); 
