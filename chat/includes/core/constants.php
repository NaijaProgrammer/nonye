<?php
$docroot = $_SERVER['DOCUMENT_ROOT'];
$p = dirname(dirname(dirname(__FILE__))); //this gets the path to the app folder from the constants.php file, irrespective of whatever page it is included in, this way, we get the path to the application folder consistently
$rep = str_replace("\\", "/", $p);

if(get_last_char($docroot) == '/'){
 $cut = substr($rep, strlen($docroot)-1, strlen($rep));
}
else{
 $cut = substr($rep, strlen($docroot), strlen($rep));
}

$ahp = 'http://'. $_SERVER['HTTP_HOST'] . $cut . '/';
$adp = $rep. '/';

define("NL", "<br>\n");
define("APP_NAME", "Chat Central");
define("APP_CREATOR_LINK", "'<a href=\"http://naijaprogrammer.com\">naijaprogrammer.com</a>'");
define("APP_HTTP_PATH", $ahp); //http path
define("APP_DOC_PATH", $adp); //host server directory structure path



//returns the last character in a string
//@date: May 9, 2012
function get_last_char($string)
{
 return trim(substr($string, strlen($string)-1));
}