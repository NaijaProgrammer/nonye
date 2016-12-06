<?php
require dirname(__DIR__). '/config.php';
//$_SESSION = array();  unset($_SESSION);  session_destroy(); var_dump($_SESSION); exit;

$request = new PathModel();
$request_parts = $request->get_parts();
var_dump($request);
var_dump($request_parts); exit;
if(!isset($_GET['provider']))
{
	die('No provider specified');
}
else
{
	include __DIR__. '/'. strtolower($_GET['provider']). '-auth.php';
}
//include __DIR__. '/twitter-auth-working.php';
//include __DIR__. '/twitter-auth-references.php';