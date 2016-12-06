<?php
require_once('../includes/core/bootstrap.inc');
require_once('../includes/db/db_setup.php');

$db_server = $_POST['server_host'];
$db_name   = $_POST['db_name'];
$db_user   = $_POST['db_user'];
$db_pass   = $_POST['db_pass'];
$error_message = '';

if(empty($db_server) || empty($db_name) || empty($db_user) ){
 $error_message = "Please fill in all required fields";
 require_once('forms/installation_form.inc');
 exit;
}

$db_prepare = create_db_tables($db_server, $db_user, $db_pass, $db_name);
if($db_prepare != true){
 $error_message = $prepare;
 require_once('forms/installation_form.inc');
 exit;
}  

$fp = fopen('../includes/db/dbconfig.php', 'w');
fwrite($fp, "<?php \$dbserver = '". $db_server . "';\n \$dbname = '". $db_name. "';\n \$dbuser = '" . $db_user. "';\n \$dbpassword = '". $db_pass. "';\n ?>");
header("location: chat_config.php");
exit;
?>