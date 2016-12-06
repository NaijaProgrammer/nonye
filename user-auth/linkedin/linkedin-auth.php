<?php
//docs: 
//https://developer.linkedin.com/docs/oauth2, 
//https://developer.linkedin.com/docs/signin-with-linkedin
//https://developer.linkedin.com/docs/fields/basic-profile#!

function get_user_linkedin_data($access_token='')
{
	if(empty($access_token))
	{
		return array();
	}
	
	$options  = array( CURLOPT_SSL_VERIFYPEER => false, CURLOPT_HTTPHEADER => array("Authorization: Bearer $access_token") );
	$response = make_remote_request('https://api.linkedin.com/v1/people/~:(first-name,last-name,location,public-profile-url,email-address,picture-urls::(original))?format=json', $options);
	
	return $response;
	
}
function authenticate_linkedin_user()
{
	$user_data_str = get_user_linkedin_data($_SESSION['lnkdin_access_token']);
	/*
	'{
	  "": "orji4y@yahoo.com",
	  "": "Michael",
	  "": "O",
	  "location": {
		"country": {"code": "ng"},
		"name": "Nigeria"
	  },
	  "pictureUrls": {"_total": 0},
	  "publicProfileUrl": "https://www.linkedin.com/in/michael-o-52640444"
	}
	*/
	
	$user_data = json_decode($user_data_str, true);
	
	$email         = $user_data['emailAddress'];
	$firstname     = $user_data['firstName'];
	$lastname      = $user_data['lastName'];
	$location      = $user_data['location']['name'];
	$linkedin_url  = $user_data['publicProfileUrl'];
	$auth_provider = 'linkedin';
	$user_password = generate_user_social_password($email, $auth_provider); //Util::stringify($email. $auth_provider);
	
	if( !UserModel::user_exists($email) )
	{
		$signup_data = UserAuth::register_user(array( 
			'signup-type'   => 'third-party-authorization', 
			'auth-provider' => $auth_provider, 
			'email'         => $email, 
			'password'      => $user_password,
			'firstname'     => $firstname, 
			'lastname'      => $lastname,
			'location'      => $location,
			'linked-in-url' => $linkedin_url
		));
	}
	
	$login_data = UserAuth::login_user(array(
		'loginType'               => 'third-party-authorization',
		'authProvider'            => $auth_provider,
		'userLogin'               => $email, 
		'userPassword'            => $user_password, 
		'rememberUser'            => true, 
		'unverifiedAccountError'  => 'Invalid account details. Please try again',
		'emptyLoginFieldError'    => '', 
		'emptyPasswordFieldError' => ''
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
}

$api_key      = get_external_app_data('linkedin-login')['key'];
$api_secret   = get_external_app_data('linkedin-login')['secret'];
$redirect_uri = SITE_URL. '/user-auth/linkedin';

if(!isset($_SESSION['lnkdin_CSRF_check_state']))
{
	$_SESSION['lnkdin_CSRF_check_state'] = (string)mt_rand();
}


//STEP 3: (optional), if the user refreshes after successful STEP 2B
if(isset($_SESSION['lnkdin_access_token']))
{
	authenticate_linkedin_user();
	exit;
}

//STEP 2A:
if(isset($_GET['error']))
{
	//user refused access to our application
	$err_message = urldecode($_GET['error_description']);
	die('The following error occurred: '. $err_message. '<br>code: '. $_GET['error']);
}

//STEP 2B:
if(isset($_GET['code']) && isset($_GET['state']))
{
	//user has granted access, and has been redirected here from linkedin
	
	if( $_GET['state'] != $_SESSION['lnkdin_CSRF_check_state'] )
	{
		//possible CSRF attach
		die('Unknown request');
	}
	
	if( !isset($_SESSION['lnkdin_exchange_code']) )
	{
		$_SESSION['lnkdin_exchange_code'] = $_GET['code'];
	}
	
	//Exchange Authorization Code for an Access Token
	/*
	* in case you are sending a string, urlencode() it. 
	* Otherwise if array, it should be key=>value paired and the Content-type header is automatically set to multipart/form-data.
	* http://stackoverflow.com/a/5224940/1743192
	*/
	$postfields = array('grant_type'=>'authorization_code', 'code'=>$_SESSION['lnkdin_exchange_code'], 'redirect_uri'=>$redirect_uri, 'client_id'=>$api_key, 'client_secret'=>$api_secret);
	$options    = array( 
		CURLOPT_HTTPHEADER     => array("Content-type: application/x-www-form-urlencoded"),
		CURLOPT_POSTFIELDS     => http_build_query($postfields, '', '&'),
		CURLOPT_POST           => 1, 
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_SSL_VERIFYPEER => false
	);
	
	$response     = make_remote_request('https://www.linkedin.com/oauth/v2/accessToken', $options);
	
	$json         = json_decode($response, true);
	$access_token = $json['access_token'];
	$expiry       = $json['expires_in']; //currently defaults to 60 days, as per official docs
	
	if( !empty($access_token) && !isset($_SESSION['lnkdin_access_token']) )
	{
		$_SESSION['lnkdin_access_token'] = $access_token;
	}
	
	authenticate_linkedin_user();
	exit;
}

//STEP 1:
header('Location: https://www.linkedin.com/oauth/v2/authorization'.
	'?response_type=code'.
	'&client_id='. $api_key.
	'&redirect_uri='. $redirect_uri.
	'&state='. $_SESSION['lnkdin_CSRF_check_state'].
	'&scope=r_basicprofile%20r_emailaddress'
);
