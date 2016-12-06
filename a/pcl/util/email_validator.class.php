<?php

class EmailValidator extends Validator
{
	public static function is_valid_email($email)
	{
		return preg_match( "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[az0-9-]+)*(\.[a-z]{2,4})^", $email );
	}
}