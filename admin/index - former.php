<?php
require dirname(__DIR__). '/config.php';
require __DIR__. '/admin-functions.php';
ini_set('display_errors', 1);

define('ADMIN_DIR', SITE_DIR. '/admin');
define('ADMIN_URL', SITE_URL. '/admin');
define('ADMIN_VIEWS_DIR',     ADMIN_DIR. '/themes');

$admin_theme = get_admin_current_theme();

if( !is_admin(UserModel::get_current_user_id()) )
{
	//include(ADMIN_DIR. '/authentication/admin-login.php');
	//exit;
}

$dir  = ( isset($_GET['dir'])  ? $_GET['dir']  : ''  );
$page = ( isset($_GET['page']) ? $_GET['page'] : 'home' ). '.php';

if( !empty($dir) )
{ 
	if( file_exists(ADMIN_VIEWS_DIR. '/'. $admin_theme. '/'. $dir. '/'. $page) )
	{
		include ADMIN_VIEWS_DIR. '/'. $admin_theme. '/'. $dir. '/'. $page;
	}
	
	elseif( file_exists(ADMIN_VIEWS_DIR. $admin_theme. '/'. $dir. '/index.php') )
	{ 
		include ADMIN_VIEWS_DIR. '/'. $admin_theme. '/'. $dir. '/index.php';
	}
	
	else
	{
		include ADMIN_VIEWS_DIR. '/404.php';
	}
}

else
{ 
	if( file_exists(ADMIN_VIEWS_DIR. '/'. $admin_theme. '/'. $page) )
	{  
		include ADMIN_VIEWS_DIR. '/'. $admin_theme. '/'. $page;
	}
	else
	{
		include ADMIN_VIEWS_DIR. '/404.php'; 
	}
}