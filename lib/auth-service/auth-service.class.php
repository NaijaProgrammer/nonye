<?php
/*
* Class AuthService
* Author Michael Orji http://shybits.com/
*/

$auth_providers_dir = __DIR__. '/auth-providers';
require $auth_providers_dir. '/fb/fb-auth.class.php';
require $auth_providers_dir. '/google/google-auth.class.php';

class AuthService
{
	private $service_provider;
	
	private $app_id; 
	
	private $app_secret;
	
	private $access_token_session_name; 
	
	private $permissions;
	
	private $auth_callback_url; //url to redirect to after success authorization
	
	private $client;
	
	private $auth_url;
	
	/*
	* $data members:
	* service_provider (google, facebook)
	* app_id
	* app_secret
	* access_token_session_name
	* permissions
	* auth_callback_url
	*/
	public function __construct($data)
	{
		extract($data);
		$this->set_service_provider($service_provider);
		$this->_set_client_($data);
	}
	
	private function _set_client_($data)
	{
		switch($this->get_service_provider())
		{
			case 'facebook' : $this->client = new FBAuth($data); break;
			case 'google'   : $this->client = new GoogleAuth($data); break;
		}
	}

	public function get_client()
	{
		return $this->client;
	}
	
	public function set_service_provider($provider_name)
	{
		$this->service_provider = $provider_name;
	}
	
	public function get_service_provider()
	{
		return $this->service_provider;
	}
	
	public function get_auth_url( $extra_params = array() )
	{
		return $this->get_client()->get_auth_url($extra_params);
	}

	public function get_access_token()
	{
		return $this->get_client()->get_access_token();
	}
	
	public function get_user_data()
	{
		return $this->get_client()->get_user_data();
	}
	
	private function _throw_error_($type='unsupported_service_provider')
	{
		die('Unsupported oAuth Service Provider');
	}
}