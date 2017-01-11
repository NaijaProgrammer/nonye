<?php
require '../a/pcl/ini.php';

define("NL", "\r\n");
define("TAB", "\t");

$site_name     = '';
$db_server     = '';
$db_user       = '';
$db_pass       = '';
$db_name       = '';
$tables_prefix = 'tp_';

if($_SERVER['REQUEST_METHOD'] == 'POST'):

	foreach($_POST AS $key => $value)
	{
		$$key = is_string($value) ? trim($value) : $value;
		
		if( ($key == 'tables_prefix') && empty($value) )
		{
			$value = 'mo_';
			$$key = $value;
		}
	}
	
	$validate = Validator::validate(array(
		array('error_condition'=>empty($site_name), 'error_message'=>'The Site Name field cannot be empty'),
		array('error_condition'=>empty($db_server), 'error_message'=>'Database server field cannot be empty'),
		array('error_condition'=>empty($db_user),   'error_message'=>'The Database user field cannot be empty'),
		array('error_condition'=>empty($db_name),   'error_message'=>'The Database name field cannot be empty')
	));
	
	if($validate['error'])
	{
		$status_message = $validate['status_message'];
		include 'installation-form.php';
		exit;
	}
	
	$link = @mysqli_connect( $db_server, $db_user, $db_pass, $db_name );
	
	$validate = Validator::validate(array(
		array( 
			'error_condition'=>!$link, 
			'error_message'=>'Unable to connect to the database server.<br/>MySQL reported the following error: <b>'. mysqli_connect_error(). '</b><br>'.
							'Ensure the database server name, username and password are correct and try again.'
		)
	));
	
	if($validate['error'])
	{
		$status_message = $validate['status_message'];
		include 'installation-form.php';
		exit;
	}
	
	$content = "<?php". NL.
	
					"/**". NL. 
					" * File name: config.php". NL. 
					" * contains global - framework - configuration information". NL.
					" * @author Michael Orji". NL. 
					" */". NL. NL.
	
					"error_reporting(E_ALL);". NL. NL.
					
					"ini_set('display_errors', 1);". NL. NL.
					
					"if(get_magic_quotes_gpc()):". NL. NL. 
					TAB. "function stripslashes_recursive(\$value)". NL. 
					TAB. "{". NL. 
					TAB. TAB. "\$value = is_array(\$value) ? array_map('stripslashes_recursive', \$value) : stripslashes(\$value);". NL. 
					TAB. TAB. "return \$value;". NL.
					TAB. "}". NL.
					TAB. "\$_POST    = array_map('stripslashes_recursive', \$_POST);". NL.
					TAB. "\$_GET     = array_map('stripslashes_recursive', \$_GET);". NL.
					TAB. "\$_REQUEST = array_map('stripslashes_recursive', \$_REQUEST);". NL.
					TAB. "\$_COOKIE  = array_map('stripslashes_recursive', \$_COOKIE);". NL. NL.
					"endif;". NL. NL.
					
					"require 'a/pcl/ini.php';". NL. NL.
					
					"\$current_script_paths = UrlInspector::get_path(dirname(__FILE__));". NL. NL.
					  
					"/**". NL. 
					" * database server name". NL. 
					" */". NL.
					"define('DB_SERVER', '$db_server');". NL. NL.
					   
					"/**". NL. 
					" * database user name". NL. 
					" */". NL.
					"define('DB_USER', '$db_user');". NL. NL.
					   
					"/**". NL. 
					" * database user password". NL. 
					" */". NL.
					"define('DB_PASS', '$db_pass');". NL. NL.
					   
					"/**". NL. 
					" * database name". NL. 
					" */". NL.
					"define('DB_NAME', '$db_name');". NL. NL.
					   
					"/**". NL. 
					" * database tables prefix". NL. 
					" */". NL.
					"define('TABLES_PREFIX', '$tables_prefix');". NL. NL.
					
					"define('SITE_DIR', rtrim(\$current_script_paths['dir_path'], '/'));". NL. NL.
					
					"define('SITE_URL', rtrim(\$current_script_paths['http_path'], '/'));". NL. NL.
					
					"define('INCLUDES_DIR',    SITE_DIR. '/includes');". NL. NL.
					
					"define('CONTROLLERS_DIR', SITE_DIR. '/controllers');". NL. NL.
					
					"define('VIEWS_DIR',       SITE_DIR. '/themes');". NL. NL.
					
					"define('VIEWS_URL',       SITE_URL. '/themes');". NL. NL.
					
					"AutoLoader::load_class_on_demand(SITE_DIR.'/model/', '.class.php');". NL. NL.

					"AutoLoader::load_class_on_demand(INCLUDES_DIR. '/classes/', '.class.php');". NL. NL.
					
					"require_once INCLUDES_DIR. '/functions.php';". NL. NL.
					
					"require SITE_DIR. '/a/base-model.class.php';". NL. NL.
					
					"require SITE_DIR. '/a/base-controller.class.php';". NL. NL.
					
					"include(SITE_DIR. '/app-bootstrap.php');". NL. NL.
					
					"/**". NL.
					" * Starts the session".NL. NL.
					" * This method must be called before using any of the UserManager methods,". NL.
					" * otherwise, you'll get a \"Fatal error: Class 'SessionExtended' not found\".". NL.
					" */". NL.
					"UserModel::start_session();". NL. "";
			   
	$config_file = new FileWriter('../config.php', 'WRITE_ONLY');
	$config_file->write($content);
	
	include '../includes/classes/db.class.php';
	include 'db-setup.php';
	$db_obj = Db::get_instance($db_server, $db_user, $db_pass, $db_name);
	setup_db($db_obj, $tables_prefix);
	
	$registration_success_msg = '<p>Dear {{username}},</p>'.
	'<p>Thank you for signing up on {{site_name}}, your reliable and trusted knowledge community.<br>'.
	'We hope you find the answers you seek through your use of {{site_name}} services.<br>'.
	'And we look forward to learning from your wealth of knowledge and experience.</p>'.
	'<p>-- The {{site_name}} team';
	
	$password_recovery_msg = '<p>'.
	 'You are receiving this mail because someone has requested a password reset for your {{site_name}} account<br />'.
	 'If you did not initiate this request, then you need not do anything further.<br />'.
	 'However, if you would like to reset your password, click on the link below: <br />'.
	 '<a href="{{password_reset_url}}?nonce={{nonce}}">Reset Password</a><br/><br />'.
	 '(NOTE: This operation must be performed within 24 hours of receiving this email)'.
	'</p>';
	
	$user_privilege_change_msg = 'Dear {{username}},<br />'.
	'Your data has been updated on {{site_name}}'.
	'{{roles}} {{capabilities}} {{login_url}}'.
	'	-- The {{site_name}} Site Admin';
	
	include '../config.php'; 
	import_admin_functions();
	create_default_user_roles();
	create_default_user_capabilities();
	update_app_settings( array(
		'site-name'                          => $site_name, 
		'active-theme'                       => 'default',
		'session-lifetime'                   => 60,
		'password-min-length'                => 6,
		'company-address'                    => '',
		'default-user-image-url'             => get_site_url(). '/resources/images/default-user-avatar.png',
		'registration-success-mail-message'  => htmlspecialchars( $registration_success_msg ),
		'registration-success-mail-sender'   => 'admin@{{site_name}}.com',
		'password-recovery-mail-message'     => htmlspecialchars( $password_recovery_msg ),
		'password-recovery-mail-sender'      => 'password-recovery@{{site_name}}.com',
		'user-privilege-change-mail-message' => htmlspecialchars( $user_privilege_change_msg ),
		'user-privilege-change-mail-sender'  => 'admin@{{site_name}}.com',
	) );
	
	
	//a custom function defined in the app-functions.php file to run app-specific setup after framework installation
	if(function_exists('setup_app'))
	{
		setup_app();
	}
	
	UrlManipulator::redirect( SITE_URL. '/admin/setup' );

endif;

include('installation-form.php');