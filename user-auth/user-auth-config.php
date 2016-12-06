<?php
require dirname(__DIR__). '/config.php';

function generate_user_social_password($user_email, $auth_provider)
{
	return Util::stringify($user_email. $auth_provider);
}