<?php
require dirname(__DIR__). '/config.php';

verify_request_origin($die = true, $message='Invalid request origin');
is_ajax_request() or die('Invalid request method');

define("VALID_AJAX_REQUEST", true);

$user_is_logged_in = UserModel::user_is_logged_in();
$current_user_id   = UserModel::get_current_user_id();

if( $_SERVER['REQUEST_METHOD'] == 'GET' )
{
	if( !isset($_GET['p']) )
	{
		die('Bad request');
	}

	$requested_file = $_GET['p']. '.php';
}
else if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	if( !isset($_POST['p']))
	{
		die('Bad request');
	}

	$requested_file = $_POST['p']. '.php';
}

if( file_exists($requested_file) )
{
	include __DIR__. '/'. $requested_file;
}
else
{
	echo 'Requested resource not found';
}