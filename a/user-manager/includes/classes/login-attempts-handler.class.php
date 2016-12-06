<?php

define("TIME_PERIOD", "24");
define("MAX_ATTEMPTS_NUMBER", "3");

class LoginAttemptsHandler
{
	public static function suspicious_login_attempts($ipaddress)
	{ 
		$q = "SELECT attempts, (CASE WHEN lastlogin is NOT NULL and DATE_ADD(lastlogin, INTERVAL ". TIME_PERIOD. " HOUR)>NOW() then 1 else 0 end) as Denied 
         FROM ". TABLES_PREFIX. "login_attempts WHERE ipaddress = '$ipaddress'"; 

		$result = mysql_query($q); 
		$data   = mysql_fetch_array($result); 
		if (!$data)
		{ 
			return 0; 
		}
	
		if ($data["attempts"] >= MAX_ATTEMPTS_NUMBER) 
		{ 
			if($data["Denied"] == 1)
			{ 
				return 1; 
			} 
			else
			{ 
				self::clearLoginAttempts($value); 
				return 0; 
			} 
		} 
		return 0; 
	} 

	public static function add_login_attempt($ipaddress)
	{
		//Increase number of attempts. Set last login attempt if required.
		$q = "SELECT * FROM ". TABLES_PREFIX. "login_attempts WHERE ipaddress = '$ipaddress'"; 
		$result = mysql_query($q);
		$data   = mysql_fetch_array($result);

		if(!$data)
		{
			$q = "INSERT INTO ". TABLES_PREFIX."login_attempts (attempts, ipaddress, lastlogin) values (1, '$ipaddress', NOW())";
			$result = mysql_query($q); 
			return;
		}

		$attempts = $data["attempts"]+1; 

		if($attempts == MAX_ATTEMPTS_NUMBER)
		{
		   $q = "UPDATE ". TABLES_PREFIX. "login_attempts SET attempts=". $attempts. ", lastlogin=NOW() WHERE ipaddress = '$ipaddress'";
		   $result = mysql_query($q);
		}
	
		else
		{
		   $q = "UPDATE ". TABLES_PREFIX."login_attempts SET attempts=". $attempts. " WHERE ipaddress = '$ipaddress'";
		   $result = mysql_query($q);
		}
	} 

	public static function clear_login_attempts($ipaddress)
	{
		$q = "UPDATE ". TABLES_PREFIX. "login_attempts SET attempts = 0 WHERE ipaddress = '$ipaddress'"; 
		return mysql_query($q);
	}
}