<?php
$docroot = $_SERVER['DOCUMENT_ROOT'];
$p = dirname(dirname(dirname(__FILE__))); //this gets the path to the constants.php file, irrespective of whatever page it is included in, this way, we get the path to the application folder consistently
$rep = str_replace("\\", "/", $p);
//$cut = substr($rep, strlen($docroot)-1, strlen($rep));//development version, i.e, localhost:81
$cut = substr($rep, strlen($docroot), strlen($rep)); //use in production envinronment, i.e, out of localhost:81

echo($docroot . "\r\n". $rep); exit;

$ahp = 'http://'. $_SERVER['HTTP_HOST'] . $cut . '/';
$adp = $rep. '/';

define("NL", "<br>\n");
define("APP_NAME", "Chat Central");
define("APP_CREATOR_LINK", "'<a href=\"http://www.appstreet.com\">appstreet.com</a>'");
define("APP_HTTP_PATH", $ahp); //http path
define("APP_DOC_PATH", $adp); //host server directory structure path


function get_last_char($string){

 return substr($string, strlen($string)-1);

}
?>