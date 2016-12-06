<?php
function setup_db($db_obj, $tables_prefix='')
{ 
	$tables = Db::get_tables();
	 
	$tables_len = count($tables);

	for($i=0; $i < $tables_len; $i++)
	{
		if($tables[$i] == "items")
		{
			$db_obj->execute_query("CREATE TABLE IF NOT EXISTS {$tables_prefix}items(
				`id`         int NOT NULL auto_increment PRIMARY KEY,
				`date_added` datetime NOT NULL
			) DEFAULT CHARACTER SET utf8" );
		}
		
		else if($tables[$i] == "item_meta")
		{
			$db_obj->execute_query("CREATE TABLE IF NOT EXISTS {$tables_prefix}item_meta(
				`id`         int NOT NULL auto_increment PRIMARY KEY,
				`item_id`    int(15),
				`meta_key`   varchar(255),
				`meta_value` longtext
			) DEFAULT CHARACTER SET utf8" ); //end sql command
		}
		
		else if($tables[$i] == "login_attempts")
		{
			$db_obj->execute_query("CREATE TABLE IF NOT EXISTS {$tables_prefix}login_attempts (
				`ipaddress` varchar(50), 
				`attempts`  int(10) NOT NULL default '0',
				`lastlogin` datetime NOT NULL
			) DEFAULT CHARACTER SET utf8" ); 
		}
		
		else if($tables[$i] == "sessions")
		{
			$db_obj->execute_query("CREATE TABLE IF NOT EXISTS {$tables_prefix}sessions (
				id varchar(50) PRIMARY KEY NOT NULL default '',
				data longblob NOT NULL default '',
				useragent varchar(200) NOT NULL default '',
				starttime int NOT NULL default '0',
				lastused int NOT NULL default '0',
				expiry int NOT NULL default '0'
			) DEFAULT CHARACTER SET utf8" ); //end sql command
		}
	  
		else if($tables[$i] == "users")
		{
			$db_obj->execute_query("CREATE TABLE IF NOT EXISTS {$tables_prefix}users (
				`id`              bigint(20) NOT NULL auto_increment PRIMARY KEY,
				`login`           varchar(60),
				`password`        varchar(150),
				`date_registered` datetime NOT NULL
			) DEFAULT CHARACTER SET utf8" );
		}
	  
		else if($tables[$i] == "user_meta")
		{
			$db_obj->execute_query("CREATE TABLE IF NOT EXISTS {$tables_prefix}user_meta (
				`id`          bigint(20) NOT NULL auto_increment PRIMARY KEY,
				`user_id`     bigint(20) NOT NULL,
				`meta_key`    varchar(255),
				`meta_value`  longtext
			) DEFAULT CHARACTER SET utf8" );
		}

		else if($tables[$i] == "user_logins")
		{
			$db_obj->execute_query("CREATE TABLE IF NOT EXISTS {$tables_prefix}user_logins (
				`id`          int NOT NULL auto_increment PRIMARY KEY,
				`user_id`     int(10) NOT NULL,
				`ip_address`  varchar(20),
				`login_page`  varchar(150),
				`login_type`  int(2),
				`login_time`  datetime NOT NULL
			) DEFAULT CHARACTER SET utf8" );
		}

		else if($tables[$i] == "user_logouts")
		{
			$db_obj->execute_query("CREATE TABLE IF NOT EXISTS {$tables_prefix}user_logouts (
				`id`           int NOT NULL auto_increment PRIMARY KEY,
				`login_id`     int(10) NOT NULL,
				`logout_page`  varchar(150),
				`logout_type`  int(2),
				`logout_time`  datetime NOT NULL 
			) DEFAULT CHARACTER SET utf8" );
		}
	  
		else if($tables[$i] == "app_settings")
		{
			$db_obj->execute_query("CREATE TABLE IF NOT EXISTS {$tables_prefix}app_settings (
				`id`             int(20) NOT NULL auto_increment PRIMARY KEY,
				`setting_key`    varchar(255),
				`setting_value`  longtext
			) DEFAULT CHARACTER SET utf8" ); //end sql command
		}   
	}
}