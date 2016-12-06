<?php
/**
 * File name: config.php
 * contains global - framework - configuration information
 * @author Michael Orji
 */

error_reporting(E_ALL);

ini_set('display_errors', 1);

if(get_magic_quotes_gpc()):

	function stripslashes_recursive($value)
	{
		$value = is_array($value) ? array_map('stripslashes_recursive', $value) : stripslashes($value);
		return $value;
	}
	$_POST    = array_map('stripslashes_recursive', $_POST);
	$_GET     = array_map('stripslashes_recursive', $_GET);
	$_REQUEST = array_map('stripslashes_recursive', $_REQUEST);
	$_COOKIE  = array_map('stripslashes_recursive', $_COOKIE);

endif;

require 'a/pcl/ini.php';

$current_script_paths = UrlInspector::get_path(dirname(__FILE__));

/**
 * database server name
 */
define('DB_SERVER', 'localhost');

/**
 * database user name
 */
define('DB_USER', 'root');

/**
 * database user password
 */
define('DB_PASS', '');

/**
 * database name
 */
define('DB_NAME', 'forum_software');

/**
 * database tables prefix
 */
define('TABLES_PREFIX', 'fs_');

define('SITE_DIR', rtrim($current_script_paths['dir_path'], '/'));

define('SITE_URL', rtrim($current_script_paths['http_path'], '/'));

define('INCLUDES_DIR',    SITE_DIR. '/includes');

define('CONTROLLERS_DIR', SITE_DIR. '/controllers');

define('VIEWS_DIR',       SITE_DIR. '/themes');

define('VIEWS_URL',       SITE_URL. '/themes');

AutoLoader::load_class_on_demand(SITE_DIR.'/model/', '.class.php');

AutoLoader::load_class_on_demand(INCLUDES_DIR. '/classes/', '.class.php');

require_once INCLUDES_DIR. '/functions.php';

require SITE_DIR. '/a/base-model.class.php';

require SITE_DIR. '/a/base-controller.class.php';

include(SITE_DIR. '/app-bootstrap.php');

/**
 * Starts the session

 * This method must be called before using any of the UserManager methods,
 * otherwise, you'll get a "Fatal error: Class 'SessionExtended' not found".
 */
UserModel::start_session();
