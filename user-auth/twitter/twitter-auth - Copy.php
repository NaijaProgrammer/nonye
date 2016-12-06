<?php
/*
* $data members:
* consumer_key
* consumer_secret
* token_secret
* extra_oauth_data array(oauth_callback, oauth_token)
* 
* Official docs: https://dev.twitter.com/web/sign-in/implementing
* credits: http://stackoverflow.com/a/21290729/1743192, http://stackoverflow.com/a/21430461/1743192, https://blog.jacobemerick.com/web-development/working-with-twitters-api-via-php-oauth/
*
*/
function build_twitter_auth_str($data)
{
	$consumer_key    = isset($data['consumer_key'])    ? $data['consumer_key']    : get_external_app_data('twitter')['key'];
	$consumer_secret = isset($data['consumer_secret']) ? $data['consumer_secret'] : get_external_app_data('twitter')['secret'];

	$host = 'api.twitter.com';
	$method = isset($data['request_method']) ? $data['request_method'] : 'POST';
	$path = $data['api_call_path']; // '/oauth/request_token'; // api call path

	$oauth = array(
		//'oauth_callback'         => SITE_URL. '/user-auth/index.php',
		'oauth_consumer_key'     => $consumer_key,
		'oauth_nonce'            => (string)mt_rand(), // a stronger nonce is recommended
		'oauth_signature_method' => 'HMAC-SHA1',
		'oauth_timestamp'        => time(),
		'oauth_version'          => '1.0'
	);
	
	if(isset($data['extra_oauth_data']))
	{ 
		foreach( $data['extra_oauth_data'] AS $key => $value )
		{
			$oauth[$key] = $value;
		}
	}
	
	$arr = array();
	
	foreach($oauth AS $key => $value)
	{
		$encoded_key = rawurlencode($key);
		$encoded_val = rawurlencode($value);
		$arr[$encoded_key] = $encoded_val;
	}

	ksort($arr);

	// http_build_query automatically encodes, but our parameters
	// are already encoded, and must be by this point, so we undo
	// the encoding step
	$querystring = urldecode(http_build_query($arr, '', '&'));
	$url = "https://$host$path";

	// mash everything together for the text to hash
	$base_string = strtoupper($method). "&". rawurlencode($url). "&". rawurlencode($querystring);

	// same with the key
	
	if(isset($data['token_secret']))
	{
		$token_secret = $data['token_secret'];
		$key = rawurlencode($consumer_secret). "&". rawurlencode($token_secret);
	}
	else
	{
		$key = rawurlencode($consumer_secret)."&";
	}

	// generate the hash
	$signature = rawurlencode(base64_encode(hash_hmac('sha1', $base_string, $key, true)));
	//$url=str_replace("&amp;","&",$url); //Patch by @Frewuill

	$oauth['oauth_signature'] = $signature; // don't want to abandon all that work!
	ksort($oauth); // probably not necessary, but twitter's demo does it

	// also not necessary, but twitter's demo does this too
	if(!function_exists('add_quotes'))
	{
		function add_quotes($str) { return '"'.$str.'"'; }
	}

	if(isset($oauth['oauth_callback']))
	{
		$oauth['oauth_callback'] = urlencode($oauth['oauth_callback']);
	}

	$oauth = array_map("add_quotes", $oauth);

	// this is the full value of the Authorization line
	$auth_str = "OAuth " . urldecode(http_build_query($oauth, '', ', '));
	
	return $auth_str;
}

if(isset($_SESSION['twtr_access_token']))
{
	$oauth_token      = $_SESSION['twtr_access_token'];
	$token_secret     = $_SESSION['twtr_access_token_secret'];
	$extra_oauth_data = array('oauth_token'=>$oauth_token);
	$auth_str  = build_twitter_auth_str(array('api_call_path'=>'/1.1/account/verify_credentials.json', 'request_method'=>'GET', 'extra_oauth_data'=>$extra_oauth_data, 'token_secret'=>$token_secret));
			
	$options   = array( CURLOPT_HTTPHEADER=>array("Accept: */*", "Authorization: $auth_str"), CURLOPT_RETURNTRANSFER=>true, CURLOPT_SSL_VERIFYPEER => false );
	$user_data = make_remote_request('https://api.twitter.com/1.1/account/verify_credentials.json', $options);
	
	$user_data = json_decode($user_data, true);
	var_dump($user_data);
	
	/* name, screen_name */
	exit;
}

if( isset($_GET['oauth_token']) && isset($_GET['oauth_verifier']) )
{
	//user redirected from twitter's login to here
	
	$extra_oauth_data = array('oauth_callback'=>SITE_URL. '/user-auth/index.php', 'oauth_token'=>$_GET['oauth_token']);
	$auth_str = build_twitter_auth_str(array('api_call_path'=>'/oauth/access_token', 'extra_oauth_data'=>$extra_oauth_data));
	$options = array( CURLOPT_POST=>1, CURLOPT_HTTPHEADER=>array("Accept: */*", "Authorization: $auth_str"), CURLOPT_RETURNTRANSFER=>true, CURLOPT_SSL_VERIFYPEER => false );
	
	//'oauth_token=905589421-c11ZO2JwA4S2b8dIpQqHYED3AvcxnbWYaK8f0ySR&oauth_token_secret=Ra1srBgAXBiYGvYh9uTDxuIU0xCwcE3coj6QXnXg5pwx8&user_id=905589421&screen_name=Michael05907608&x_auth_expires=0'
	$response    = make_remote_request('https://api.twitter.com/oauth/access_token?oauth_verifier='. $_GET['oauth_verifier'], $options);
	$access_data = explode('&', $response);
	
	if(is_array($access_data))
	{
		$oauth_token_string        = $access_data[0]; 
		$oauth_token_secret_string = $access_data[1]; 
		$user_id_string            = isset( $access_data[2] ) ? $access_data[2] : 'user_id=0'; 
		$user_screen_name_string   = isset( $access_data[3] ) ? $access_data[3] : 'screen_name=""';
		
		$oauth_token_array        = explode('=', $oauth_token_string);
		$oauth_token_secret_array = explode('=', $oauth_token_secret_string);
		$user_id_array            = explode('=', $user_id_string);
		$user_screen_name_array   = explode('=', $user_screen_name_string);
		
		$$oauth_token_array[0]        = $oauth_token_array[1];
		$$oauth_token_secret_array[0] = $oauth_token_secret_array[1];
		$$user_id_array[0]            = $user_id_array[1];
		$$user_screen_name_array[0]   = $user_screen_name_array[1];
		
		/*
		echo $response. '<br>';
		echo 'token : '. $oauth_token. '<br>';
		echo 'secret : '. $oauth_token_secret. '<br>';
		echo 'callback confirmed : '. $oauth_callback_confirmed; exit;
		*/
		
		if( !empty($oauth_token) && !isset($_SESSION['twtr_access_token']) )
		{
			$_SESSION['twtr_access_token'] = $oauth_token;
		}
		if( !empty($oauth_token_secret) && !isset($_SESSION['twtr_access_token_secret']) )
		{
			$_SESSION['twtr_access_token_secret'] = $oauth_token_secret;
		}
	}

	//https://api.twitter.com/1.1/account/verify_credentials.json?include_email=true
	exit;
}

/*
$consumer_key    = get_external_app_data('twitter')['key'];
$consumer_secret = get_external_app_data('twitter')['secret'];

$host = 'api.twitter.com';
$method = 'POST';
$path = '/oauth/request_token'; // api call path

$oauth = array(
	'oauth_callback'         => SITE_URL. '/user-auth/index.php',
    'oauth_consumer_key'     => $consumer_key,
	'oauth_nonce'            => (string)mt_rand(), // a stronger nonce is recommended
	'oauth_signature_method' => 'HMAC-SHA1',
    'oauth_timestamp'        => time(),
    'oauth_version'          => '1.0'
);

$arr = array();
foreach($oauth AS $key => $value)
{
	$encoded_key = rawurlencode($key);
	$encoded_val = rawurlencode($value);
	$arr[$encoded_key] = $encoded_val;
}

ksort($arr);

// http_build_query automatically encodes, but our parameters
// are already encoded, and must be by this point, so we undo
// the encoding step
$querystring = urldecode(http_build_query($arr, '', '&'));
$url = "https://$host$path";

// mash everything together for the text to hash
$base_string = strtoupper($method). "&". rawurlencode($url). "&". rawurlencode($querystring);

// same with the key
//$key = rawurlencode($consumer_secret)."&".rawurlencode($token_secret);
$key = rawurlencode($consumer_secret)."&";

// generate the hash
$signature = rawurlencode(base64_encode(hash_hmac('sha1', $base_string, $key, true)));
//$url=str_replace("&amp;","&",$url); //Patch by @Frewuill

$oauth['oauth_signature'] = $signature; // don't want to abandon all that work!
ksort($oauth); // probably not necessary, but twitter's demo does it

// also not necessary, but twitter's demo does this too
function add_quotes($str) { return '"'.$str.'"'; }

$oauth['oauth_callback'] = urlencode($oauth['oauth_callback']);

$oauth = array_map("add_quotes", $oauth);

// this is the full value of the Authorization line
$auth_str = "OAuth " . urldecode(http_build_query($oauth, '', ', '));
*/


$extra_oauth_data = array('oauth_callback'=>SITE_URL. '/user-auth/index.php');
$auth_str = build_twitter_auth_str(array('api_call_path'=>'/oauth/request_token', 'extra_oauth_data'=>$extra_oauth_data));
$options = array( CURLOPT_POST=>1, CURLOPT_HTTPHEADER=>array("Accept: */*", "Authorization: $auth_str"), CURLOPT_RETURNTRANSFER=>true, CURLOPT_SSL_VERIFYPEER => false );

//'oauth_token=ajp6kgAAAAAAwcWFAAABVoFexio&oauth_token_secret=9cowbvFxDMbto1stasEvs9y6FnuRonkx&oauth_callback_confirmed=true'
$response     = make_remote_request('https://api.twitter.com/oauth/request_token', $options);
$twitter_data = explode('&', $response);

if(is_array($twitter_data))
{
	$oauth_token_string               = $twitter_data[0]; //'oauth_token=ajp6kgAAAAAAwcWFAAABVoFexio'
	$oauth_token_secret_string        = $twitter_data[1]; //'oauth_token_secret=9cowbvFxDMbto1stasEvs9y6FnuRonkx'
	$oauth_callback_confirmed_string  = $twitter_data[2]; //'oauth_callback_confirmed=true'
	
	$oauth_token_array              = explode('=', $oauth_token_string);
	$oauth_token_secret_array       = explode('=', $oauth_token_secret_string);
	$oauth_callback_confirmed_array = explode('=', $oauth_callback_confirmed_string);
	
	$$oauth_token_array[0]              = $oauth_token_array[1];
	$$oauth_token_secret_array[0]       = $oauth_token_secret_array[1];
	$$oauth_callback_confirmed_array[0] = (boolean) $oauth_callback_confirmed_array[1];
	
	/*
	echo $response. '<br>';
	echo 'token : '. $oauth_token. '<br>';
	echo 'secret : '. $oauth_token_secret. '<br>';
	echo 'callback confirmed : '. $oauth_callback_confirmed; exit;
	*/
	
	if($oauth_callback_confirmed)
	{
		header("Location: https://api.twitter.com/oauth/authenticate?oauth_token=". $oauth_token);
		exit;
	}
}