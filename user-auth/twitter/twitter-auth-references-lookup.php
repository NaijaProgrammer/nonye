<?php

//credits: https://www.loginradius.com/engineering/integrating-twitter-social-login/
/*
include SITE_DIR. '/lib/twitteroauth/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;
$twitter_data = get_external_app_data('twitter');
define('CONSUMER_KEY',    $twitter_data['key']);
define('CONSUMER_SECRET', $twitter_data['secret']);
define('OAUTH_CALLBACK',  SITE_URL. '/user-auth/index.php');

$connection    = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);

$request_token = $connection->oauth('oauth/request_token', array('oauth_callback'=>OAUTH_CALLBACK));
var_dump($request_token); exit;

$url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));

$connection   = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $request_token['oauth_token'], $request_token['oauth_token_secret']);
$access_token = $connection->oauth("oauth/access_token", array("oauth_verifier" => $connection['oauth_verifier']));
*/


//credits: http://stackoverflow.com/a/21290729/1743192
$token = '905589421-c11ZO2JwA4S2b8dIpQqHYED3AvcxnbWYaK8f0ySR'; //get_external_app_data('twitter')['access_token'];
$token_secret = 'Ra1srBgAXBiYGvYh9uTDxuIU0xCwcE3coj6QXnXg5pwx8'; //get_external_app_data('twitter')['access_token_secret'];
$consumer_key = 'gKoIe8Cr43Cn4KK7ql6ejbizj'; //get_external_app_data('twitter')['key'];
$consumer_secret = 'lJlmeLnfj4K4ueFQ23ZjIYu7yPzO5LIRDHS5n60DNf94tVnoAS'; //get_external_app_data('twitter')['secret'];

$host = 'api.twitter.com';
$method = 'POST';
$path = '/oauth/request_token'; // api call path

$oauth = array(
	'oauth_callback' => SITE_URL. '/user-auth/index.php',
    'oauth_consumer_key' => $consumer_key,
	'oauth_nonce' => (string)mt_rand(), // a stronger nonce is recommended
	'oauth_signature_method' => 'HMAC-SHA1',
    //'oauth_token' => $token,
    'oauth_timestamp' => time(),
    'oauth_version' => '1.0'
);

$oauth = array_map("rawurlencode", $oauth); // must be encoded before sorting
$arr   = $oauth;

asort($arr); // secondary sort (value)
ksort($arr); // primary sort (key)

// http_build_query automatically encodes, but our parameters
// are already encoded, and must be by this point, so we undo
// the encoding step
$querystring = urldecode(http_build_query($arr, '', '&'));
$url = "https://$host$path";

// mash everything together for the text to hash
$base_string = $method."&".rawurlencode($url)."&".rawurlencode($querystring);

// same with the key
//$key = rawurlencode($consumer_secret)."&".rawurlencode($token_secret);
$key = rawurlencode($consumer_secret)."&";

// generate the hash
$signature = rawurlencode(base64_encode(hash_hmac('sha1', $base_string, $key, true)));
$url=str_replace("&amp;","&",$url); //Patch by @Frewuill

$oauth['oauth_signature'] = $signature; // don't want to abandon all that work!
ksort($oauth); // probably not necessary, but twitter's demo does it

// also not necessary, but twitter's demo does this too
function add_quotes($str) { return '"'.$str.'"'; }
$oauth = array_map("add_quotes", $oauth);

// this is the full value of the Authorization line
//$auth = "OAuth " . urldecode(http_build_query($oauth, '', ', '));

//$auth = 'OAuth oauth_consumer_key="gKoIe8Cr43Cn4KK7ql6ejbizj", oauth_nonce="8a472fa87e85a7701d476a7e0ee69c6c", oauth_signature="W2Tr9fgmXQ3iXRL9VMSTUuILSek%3D", oauth_signature_method="HMAC-SHA1", oauth_timestamp="1471017409", oauth_token="905589421-c11ZO2JwA4S2b8dIpQqHYED3AvcxnbWYaK8f0ySR", oauth_version="1.0"';

$auth = 'OAuth oauth_consumer_key="gKoIe8Cr43Cn4KK7ql6ejbizj", oauth_nonce="8a472fa87e85a7701d476a7e0ee69c6c", oauth_signature="W2Tr9fgmXQ3iXRL9VMSTUuILSek%3D", oauth_signature_method="HMAC-SHA1", oauth_timestamp="1471017409", oauth_token="905589421-c11ZO2JwA4S2b8dIpQqHYED3AvcxnbWYaK8f0ySR", oauth_version="1.0"';
/*
echo $auth; 
echo '<br>';
echo $url;
exit;
*/

// if you're doing post, you need to skip the GET building above
// and instead supply query parameters to CURLOPT_POSTFIELDS
$options = array(
//CURLOPT_CAINFO => SITE_DIR. '/_secret/cacert.pem',
CURLOPT_POST=>true,
CURLOPT_HTTPHEADER => array("Accept: */*", "Authorization: $auth"),
//CURLOPT_POSTFIELDS => array( 'oauth_callback' => SITE_URL. '/user-auth/index.php' ), //$postfields,
//CURLOPT_HEADER => true,
CURLOPT_URL => $url,
CURLOPT_RETURNTRANSFER => true,
CURLOPT_SSL_VERIFYPEER => false
CURLOPT_VERBOSE => true,
//CURLOPT_SSL_VERIFYHOST=>2
);

// do our business
/*
$ch = curl_init();
curl_setopt_array($ch, $options);
$json = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);
var_dump($info);
*/
$json = make_remote_request('https://api.twitter.com/oauth/request_token', $options);

var_dump($json);
$twitter_data = json_decode($json);
exit;


/*
//https://dev.twitter.com/web/sign-in/implementing
$response = make_remote_request(
	//'https://api.twitter.com/oauth/request_token?oauth_callback='. urlencode(SITE_URL. '/user-auth/index.php').'&oauth_consumer_key='. get_external_app_data('twitter')['key'], 
	'https://api.twitter.com/oauth/request_token', 
	array(
		CURLOPT_CAINFO => SITE_DIR. '/_secret/cacert.pem',
		CURLOPT_POST=>1, 
		CURLOPT_HEADER => true,
		CURLOPT_HTTPHEADER => [
								'Accept: * /*', 
								'Authorization: OAuth oauth_callback='. urlencode(SITE_URL. '/user-auth/index.php').'&oauth_consumer_key='. get_external_app_data('twitter')['key'], 
							],
		//CURLOPT_USERAGENT => '',
		CURLOPT_SSL_VERIFYPEER=>true,
		CURLOPT_SSL_VERIFYHOST=>2
		)
);

var_dump($response); exit;
*/
/*
* $response members:
oauth_token=string
oauth_token_secret=string
oauth_callback_confirmed=boolean
*/

if($response['oauth_callback_confirmed'] == true)
{
	echo 'https://api.twitter.com/oauth/authenticate?oauth_token='. $response['oauth_token'];
}


/*
*
* oauth_token=NPcudxy0yU5T3tBzho7iCotZ3cnetKwcTIRlX0iwRl0&
* oauth_verifier=uw7NjWHT6OJ1MpJOXsHfNxoAhPKpgI8BlYDhxEjIBY HTTP/1.1
*/


$response = make_remote_request('https://api.twitter.com/oauth/access_token?oauth_verifier='.$oauth_verifier, array(CURL_OPT_POST=>1, CURLOPT_SSL_VERIFYPEER=>0,CURLOPT_SSL_VERIFYHOST=>0));

/*
* $response_members
* oauth_token=7588892-kagSNqWge8gB1WwE3plnFsJHAZVfxWD7Vb57p0b4&
oauth_token_secret=PbKfYqSryyeKDWz4ebtY3o5ogNLG11WJuZBc9fQrQo
*/

https://api.twitter.com/1.1/account/verify_credentials.json?include_email=true