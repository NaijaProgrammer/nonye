<?php
require __DIR__. '/admin-bootstrap.php';
if(!UserModel::user_is_logged_in())
{
	header("location:". SITE_URL);
	exit;	
}
UserModel::logout_user( array('redirect_page'=>ADMIN_URL) );