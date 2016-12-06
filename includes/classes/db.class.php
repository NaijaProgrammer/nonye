<?php
class Db
{
	private $db_server;
	
	private $db_user;
	
	private $db_pass;
	
	private $db_name;
	
	private $connection_id = null;
	
	private $db_object;
	
	private static $tables = array(
	"items", "item_meta", "login_attempts", "sessions",
	"users", "user_meta", "user_logins", "user_logouts",
	"app_settings"
	);
	
	private static $instance = null;
	
	public static function get_instance($server, $user, $pass, $db)
	{
		if( !self::$instance instanceof self )
		{
			self::$instance = new self($server, $user, $pass, $db);
		}
		
    	return self::$instance->db_object;
	}
	
	public static function get_tables()
	{
		return self::$tables;
	}
	
	public function __construct($server, $user, $pass, $db)
	{
		$this->db_server = $server;
		$this->db_user   = $user;
		$this->db_pass   = $pass;
		$this->db_name   = $db;
		
		$this->db_object = new MySqlExtended();
		$this->connection_id = $this->db_object->connect( $server, $user, $pass, $db );
		MySqlExtended::set_active_connection( $this->connection_id );
		
		self::$instance = $this;
	}
	
	public function get_server()
	{
		return $this->db_server;
	}
	
	public function get_user()
	{
		return $this->db_user;
	}
	
	public function get_pass()
	{
		return $this->db_pass;
	}
	
	public function get_db()
	{
		return $this->db_name;
	}
}