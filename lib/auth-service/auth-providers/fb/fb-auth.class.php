<?php
require_once __DIR__ . '/facebook-php-sdk-v4-5.0.0/src/Facebook/autoload.php';

class FBAuth
{
	private $app_id; //fb app ID
	
	private $app_secret; //fb app secret
	
	private $access_token_session_name; //string default is 'facebook_access_token'
	
	private $permissions; //array of required permissions e.g ['email', 'public_profile']
	
	private $auth_callback_url; //url to redirect to after success authorization
	
	public function __construct( $data )
	{
		extract($data);
		
		$this->_set_app_id_($app_id);
		$this->_set_app_secret_($app_secret);
		$this->_set_access_token_session_name_($access_token_session_name);
		$this->_set_permissions_($permissions);
		$this->_set_auth_callback_url_($auth_callback_url);
	}
	
	public function get_client()
	{	
		$fb = new Facebook\Facebook([
		  'app_id'                  => $this->get_app_id(), 
		  'app_secret'              => $this->get_app_secret(),
		  'default_graph_version'   => 'v2.5',
		  'persistent_data_handler' => 'session'
		]);
		
		return $fb;
	}

	public function get_redirect_login_helper()
	{
		$fb          = $this->get_client();
		$helper      = $fb->getRedirectLoginHelper();
		
		return $helper;
	}

	public function get_auth_url( $extra_params = array() )
	{
		$auth_callback_url  = $this->get_auth_callback_url();
		
		//$auth_callback_url .= '?auth-provider=facebook';
		
		foreach($extra_params AS $key => $value)
		{
			$query_separator = ( url_contains_query_string($auth_callback_url) ? '&' : '?' );
			$auth_callback_url .= $query_separator. urlencode($key. '='. $value);
		}
		
		$helper      = $this->get_redirect_login_helper();
		$permissions = $this->get_permissions(); //['email', 'public_profile']; // optional
		$login_url   = $helper->getLoginUrl($auth_callback_url, $permissions);
		//$qs_prefix = ( parse_url($auth_callback_url, PHP_URL_QUERY) ? '&' : '?' );
		
		//echo $login_url. '<br><br>';
		//echo urldecode($login_url); exit;
		return $login_url;
	}

	public function get_access_token()
	{ 
		$access_token = false;
		
		if( !isset( $_SESSION[$this->get_access_token_session_name()] ))
		{
			$this->_set_access_token();
		}
		
		if( isset($_SESSION[$this->get_access_token_session_name()]) )
		{
			$access_token = $_SESSION[$this->get_access_token_session_name()];
		}
		
		return  $access_token;
	}

	public function get_user_data()
	{
		$fb = $this->get_client();
		
		try 
		{
			//https://developers.facebook.com/docs/graph-api/reference/v2.6/user
		   $response = $fb->get( '/me?fields=id,name,gender,email,first_name,last_name,location,link', $this->get_access_token() );
		} 
		catch(Facebook\Exceptions\FacebookResponseException $e)
		{
			echo 'Graph returned an error: ' . $e->getMessage();
			exit;
		} 
		catch(Facebook\Exceptions\FacebookSDKException $e)
		{
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}

		$user = $response->getGraphUser(); //var_dump($user); exit;
		
		$user_data['name']        = $user['name'];
		$user_data['firstname']   = $user['first_name'];
		$user_data['lastname']    = $user['last_name'];
		$user_data['email']       = $user['email'];
		$user_data['gender']      = $user['gender'];
		$user_data['profile_url'] = $user['link'];
		$user_data['location']    = isset($user['location']) ? $user['location'] : '';
		
		return $user_data;
	}

	public function get_app_id()
	{
		return $this->app_id;
	}
	
	public function get_app_secret()
	{
		return $this->app_secret;
	}
	
	public function get_access_token_session_name()
	{
		return $this->access_token_session_name;
	}
	
	public function get_permissions()
	{
		return $this->permissions;
	}
	
	public function get_auth_callback_url()
	{
		return $this->auth_callback_url;
	}
	
	private function _set_access_token()
	{ 
		$helper = $this->get_redirect_login_helper();
		
		try
		{
			$accessToken = $helper->getAccessToken();
		} 
		
		catch(Facebook\Exceptions\FacebookResponseException $e)
		{
			echo 'Graph returned an error: ' . $e->getMessage();
			exit;
		} 
		
		catch(Facebook\Exceptions\FacebookSDKException $e)
		{
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}

		if (isset($accessToken))
		{
			$fb = $this->get_client();
			$oAuth2Client = $fb->getOAuth2Client();
			$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken((string) $accessToken);
			
			$_SESSION[$this->get_access_token_session_name()] = (string) $longLivedAccessToken;
		}
		
		else if (!isset($accessToken)) 
		{
			if ($helper->getError())
			{
				header('HTTP/1.0 401 Unauthorized');
				echo "Error: " . $helper->getError() . "\n";
				echo "Error Code: " . $helper->getErrorCode() . "\n";
				echo "Error Reason: " . $helper->getErrorReason() . "\n";
				echo "Error Description: " . $helper->getErrorDescription() . "\n";
			} 
			else
			{
				header('HTTP/1.0 400 Bad Request');
				echo 'Bad request';
			}
		}
	}
	
	private function _set_app_id_($app_id)
	{
		$this->app_id = $app_id;
	}
	
	private function _set_app_secret_($app_secret)
	{
		$this->app_secret = $app_secret;
	}
	
	private function _set_access_token_session_name_($access_token_session_name)
	{
		$this->access_token_session_name = $access_token_session_name;
	}
	
	private function _set_permissions_($permissions)
	{
		$this->permissions = $permissions;
	}
	
	private function _set_auth_callback_url_($auth_callback_url)
	{
		$this->auth_callback_url = $auth_callback_url;
	}
}