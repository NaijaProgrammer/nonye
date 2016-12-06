<?php
//credits: http://stackoverflow.com/a/21290729/1743192, http://stackoverflow.com/a/21430461/1743192, https://blog.jacobemerick.com/web-development/working-with-twitters-api-via-php-oauth/
$consumer_key = get_external_app_data('twitter')['key'];
$consumer_secret = get_external_app_data('twitter')['secret'];

$host = 'api.twitter.com';
$method = 'POST';
$path = '/oauth/request_token'; // api call path

$oauth = array(
	'oauth_callback' => SITE_URL. '/user-auth/index.php',
    'oauth_consumer_key' => $consumer_key,
	'oauth_nonce' => (string)mt_rand(), // a stronger nonce is recommended
	'oauth_signature_method' => 'HMAC-SHA1',
    'oauth_timestamp' => time(),
    'oauth_version' => '1.0'
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
$auth = "OAuth " . urldecode(http_build_query($oauth, '', ', '));

/*
echo $auth; 
echo '<br>';
echo $url;
exit;
*/

$options = array(
CURLOPT_POST=>true,
CURLOPT_HTTPHEADER => array("Accept: */*", "Authorization: $auth"),
//CURLOPT_HEADER => true,
//CURLOPT_URL => $url,
CURLOPT_RETURNTRANSFER => true,
CURLOPT_SSL_VERIFYPEER => false,
);

// do our business
/*
$ch = curl_init();
curl_setopt_array($ch, $options);
$json = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);
var_dump($info);
var_dump($json);
exit;
*/
$json = make_remote_request('https://api.twitter.com/oauth/request_token', $options);

var_dump($json);
$twitter_data = json_decode($json);
exit;