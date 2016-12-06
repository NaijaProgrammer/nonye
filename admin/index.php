<?php
require __DIR__. '/admin-bootstrap.php';

$admin_pages_dir = AdminPage::get_instance(array('theme'=>'default'))->get_theme_dir(). '/pages';

if( !UserModel::user_is_logged_in() )
{
	include(ADMIN_DIR. '/login.php');
	exit;
}
if( !user_can('Access Admin') )
{
	UrlManipulator::redirect(SITE_URL, $status=302, $delay=5, $message='Unauthorized access... redirecting... <br/>If you are not automatically redirected after 5 seconds, <a href="'. SITE_URL. '">click here</a>');
	exit;
}

$dir  = ( isset($_GET['dir'])  ? $_GET['dir']  : ''  );
$page = ( isset($_GET['page']) ? $_GET['page'] : 'home' ). '.php';

if( !empty($dir) )
{ 
	if( file_exists($admin_pages_dir. '/'. $dir. '/'. $page) )
	{
		include $admin_pages_dir. '/'. $dir. '/'. $page;
	}
	
	elseif( file_exists($admin_pages_dir. '/'. $dir. '/index.php') )
	{ 
		include $admin_pages_dir. '/'. $dir. '/index.php';
	}
	
	elseif( file_exists($admin_pages_dir. '/404.php') )
	{ 
		include $admin_pages_dir. '/404.php';
	}
	
	else
	{
		include ADMIN_DIR. '/404.php';
	}
}

else
{ 
	if( file_exists($admin_pages_dir. '/'. $page) )
	{  
		include $admin_pages_dir. '/'. $page;
	}
	
	elseif( file_exists($admin_pages_dir. '/404.php') )
	{ 
		include $admin_pages_dir. '/404.php';
	}
	
	else
	{
		include ADMIN_DIR. '/404.php'; 
	}
}