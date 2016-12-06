<?php 
require_once '../config.php'; 

if(!UserModel::user_is_logged_in())
{
	header("location:". SITE_URL);
	exit;	
}

update_user_login_status( UserModel::get_current_user_id(), 'offline' );
UserAuth::logout_user( array('redirect_page'=>SITE_URL, 'onbefore_redirect_message'=>'') );
//UserModel::logout_user( array('redirect_page'=>SITE_URL, 'onbefore_redirect_message'=>'') );