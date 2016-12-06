<?php
//since with the new import_admin_functions for use with front-end pages,
//if you just 'require' the 'config.php' file, you get notices of previously defined constants and fatal error of re-declaring functions
//so, use the 'require_once' to fix these issues
require_once dirname(__DIR__). '/config.php';
ini_set('display_errors', 1);

define('ADMIN_DIR', rtrim(SITE_DIR, '/'). '/admin');
define('ADMIN_URL', rtrim(SITE_URL, '/'). '/admin');
define('ADMIN_VIEWS_DIR', ADMIN_DIR. '/themes');
define('ADMIN_VIEWS_URL', ADMIN_URL. '/themes');
define('ADMIN_INCLUDES_DIR', ADMIN_DIR. '/includes');

require ADMIN_INCLUDES_DIR. '/admin-functions.php';
require ADMIN_INCLUDES_DIR. '/admin-page.class.php';