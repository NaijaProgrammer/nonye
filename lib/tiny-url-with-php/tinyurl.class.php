<?php
/*
* @Created by  : Bharat Parmar
* @modified by : Michael Orji
*/
class TinyURL
{
	private $con;
	private $db_server;
	private $db_user;
	private $db_pass;
	private $db_name;
	private static $instance = null;
	
	public static function get_instance( $opts = array() )
	{
		if( !self::$instance )
		{
			self::$instance = new self($opts);
		}
		
		return self::$instance;
	}
	
	/*
	* opts members:
	* db_server string optional
	* db_user string optional 
	* db_pass string optional
	* db_name string optional
	*/
	public function __construct( $opts = array() )
	{
		if( isset($opts['db_server']) && isset($opts['db_user']) && isset($opts['db_pass']) && isset($opts['db_name']) )
		{
			$this->db_server = $opts['db_server'];
			$this->db_user   = $opts['db_user'];
			$this->db_pass   = $opts['db_pass'];
			$this->db_name   = $opts['db_name'];
			
			$this->connect($this->db_server, $this->db_user, $this->db_pass,$this->db_name);
		}
	}

	public function connect($server_name, $user_name, $user_pass, $db_name)
	{
		$this->con = mysqli_connect($server_name, $user_name, $user_pass, $db_name);
	}

	public function create_short_url($long_url, $short_url)
	{
		$this->verify_connection();
		
		$last_id  = mysqli_fetch_assoc(mysqli_query($this->con, "SELECT max(id) AS max_id FROM `tiny_url_master`"));
		$tiny_url = $short_url;
		
		mysqli_query( $this->con,
			"INSERT into `tiny_url_master` SET 
			`long_url` = '". addslashes($long_url). "',
			`tiny_url` = '". $tiny_url."',
			`created_date` = '".date("Y-m-d H:i:s"). "'"
		);
		
		return mysqli_insert_id($this->con);
	}
	
	public function get_long_url($short_url)
	{
		$this->verify_connection();
		
		//$result = mysqli_query($this->con, "SELECT `long_url` FROM `tiny_url_master` WHERE `tiny_url` = '". $short_url. "'");
		$result = mysqli_query($this->con, 
				"SELECT `long_url` ".
				"FROM `tiny_url_master` ".
				"WHERE `tiny_url` = '". $short_url. "' ".
				"OR `tiny_url` = '". $this->get_www_version($short_url). "'"
		);
		
		if(!$result)
		{
			return '';
		}
		
		$data = mysqli_fetch_assoc( $result );

		return isset($data['long_url']) ? $data['long_url'] : '';
	}
	
	private function url_contains_www($url)
	{
		$arr = explode('www.', strtolower($url));
		return ( stripos($arr[0], 'www') !== FALSE );
	}

	private function get_www_version($url)
	{
		return ( $this->url_contains_www($url) ? $url : str_ireplace('http://', 'http://www.', $url) );
	}

	private function verify_connection()
	{
		if(empty($this->con))
		{
			trigger_error('Tiny Url connection not set', E_USER_ERROR);
		}
	}
}