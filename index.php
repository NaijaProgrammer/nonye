<?php
if(!file_exists('config.php'))
{
	header('location:install/');
	exit;
}

require 'config.php';

$request    = new PathModel();
$controller = $request->controller;
$controller = empty($controller) ? 'index' : $controller;

$controller_name = $controller. '-controller';
$controller_file = $controller_name. '.class.php';

if(file_exists(CONTROLLERS_DIR. '/'. $controller_file))
{  
	$controller_class = explode('-', $controller_name);
	$controller_class = implode(' ', $controller_class);
	$controller_class = UCWORDS($controller_class);
	$controller_class = explode(' ', $controller_class);
	$controller_class = implode('', $controller_class);
		
	require_once(CONTROLLERS_DIR. '/'. $controller_file);
	$controller_object = new $controller_class();
	$controller_object->execute();
}
	
else
{
	Page::get_instance( array('theme'=>get_current_theme()) )->load_404_page();
}