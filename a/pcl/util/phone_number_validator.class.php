<?php

/**
* @author Michael Orji
*/
class PhoneNumberValidator extends Validator
{
	public static function is_valid_phone_number($number)
	{
		return preg_match( "^([0-9]){5,15}^", $number );
	}
}