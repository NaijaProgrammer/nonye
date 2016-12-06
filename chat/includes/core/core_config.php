<?php 
/* This file contains core configurations and global variables */

require_once('constants.php');
require_once('bootstrap.inc');
require_once('sql_bootstrap.inc');

header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . 'GMT');
header('Pragma: no-cache');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: -1');

require_once(APP_DOC_PATH . 'includes/db/dbconfig.php'); 
$config_array['host_server']= $dbserver; 
$config_array['db_user_name'] = $dbuser; 
$config_array['db_password'] = $dbpassword; 
$config_array['database'] = $dbname; 
$connexn_id = db_connect($config_array);

if(get_magic_quotes_runtime()){
 set_magic_quotes_runtime(0);
}
if (get_magic_quotes_gpc()) {
 $_POST = strip_slashes($_POST);
 $_GET = strip_slashes($_GET);
 $_COOKIE = strip_slashes($_COOKIE);
 $_REQUEST = strip_slashes($_REQUEST);
}
$_POST = array_map('trim', $_POST);
$_POST = array_map('htmlspecialchars', $_POST);