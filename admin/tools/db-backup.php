<?php
require dirname(__DIR__). '/admin-bootstrap.php';

verify_user_logged_in();
verify_super_admin();

require SITE_DIR. '/lib/mysql-dump/mysql_backup_import.php';
	
$dest  = ADMIN_DIR. '/site-backups/';
$fname = DB_NAME. '_'. date("F d, Y H:i:s");
	
backup_database( $dest, $fname, DB_SERVER, DB_USER, DB_PASS, DB_NAME);
exit;