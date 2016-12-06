<?php
require dirname(__DIR__). '/admin-bootstrap.php';

verify_user_logged_in();
verify_super_admin();

$export_format = isset($_GET['export-format']) ? trim($_GET['export-format']) : 'csv';

$users       = UserModel::get_users(); 
$num         = count($users);
$export_data = array();

for($i = 0; $i < $num; $i++)
{
	$curr_user  = $users[$i];
	
	$export_data[$i] = array(
		'S/N'        =>($i+1),
		'Username'   => isset($curr_user['username'])  ? $curr_user['username']  : 'N/A',
		'Firstname'  => isset($curr_user['firstname']) ? $curr_user['firstname'] : 'N/A',
		'Lastname'   => isset($curr_user['lastname'])  ? $curr_user['lastname']  : 'N/A',
		'Email'      => $curr_user['email'],
		'Registered' => date( 'F d, Y', strtotime($curr_user['date_registered']) )
	);
}

require SITE_DIR. '/lib/data-exporter/data-exporter.php';	
export( $export_data, array('filename'=>get_slug(get_site_name(). '-users'), 'extension'=>$export_format) );