<?php
require_once __DIR__ . '/google-api-php-client-v1/src/Google/autoload.php';

class GoogleAuth
{
	private $app_id; //google app ID
	
	private $app_secret; //google app secret
	
	private $access_token_session_name; //string default is 'google_access_token'
	
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
		/*
		* Build and configure the client object
		* The client object is the primary container for classes and configuration in the library.
		*/
		$client = new Google_Client();
		$client->setClientId($this->get_app_id());
		$client->setClientSecret($this->get_app_secret());
		
		/*
		* Specify the URL to your application's auth endpoint, 
		* which will handle the response from the OAuth 2.0 server.
		*/
		$client->setRedirectUri($this->get_auth_callback_url());
		
		/*
		* OR ALTERNATELY:
		* Use the client_secrets.json file that you created to configure a client object in your application. 
		* When you configure a client object, you specify the scopes your application needs to access, 
		* along with the URL to your application's auth endpoint, 
		* which will handle the response from the OAuth 2.0 server.
		*
		* Important: Do not store the client_secrets.json file in a publicly-accessible location, 
		* and if you share the source code to your application—for example, on GitHub-
		* store the client_secrets.json file outside of your source tree 
		* to avoid inadvertently sharing your client credentials.
		*/
		//$client->setAuthConfigFile(SITE_DIR. '/_secret/client_secret.json');
		
		/* 
		* Specify the scopes your application needs to access
		*/
		$client->setScopes(array(
			'https://www.googleapis.com/auth/plus.login',      //get access to google plus data, circles, etc
			'https://www.googleapis.com/auth/userinfo.email',  //get access to user email
			'https://www.googleapis.com/auth/userinfo.profile' //get access to user data: name, given_name, family_name, gender, link(google+url), picture, locale
		));
		
		return $client;
	}

	/*
	* Google's implementation only allows redirecting to top-level domains, with no trailing slashes or path info.
	* The $state_value string allows us to set the value of the 'state' query string that will be appended to the URL
	* that google redirects to. That way, we can test for that state on the top-level of our application and redirect to our custom auth page
	*/
	public function get_auth_url( $state_value='' )
	{
		$client    = $this->get_client();
		$auth_url  = $client->createAuthUrl(); //Generate a URL to request access from Google's OAuth 2.0 server:
		$auth_url .= '&state='. $state_value;
		return $auth_url;
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
		$client       = $this->get_client();
		$access_token = $this->get_access_token(); 
		$access_token = json_decode($access_token, true); 
		$access_token = $access_token['access_token'];
		$q            = 'https://www.googleapis.com/oauth2/v1/userinfo?access_token='. $access_token;
		
		/* USE file_get_contents()
		$json      = file_get_contents( filter_var($q, FILTER_SANITIZE_URL));
		$user_data = json_decode($json,true);
		*/
		
		//* OR use CURL (both methods work)
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $q);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($ch);	
		if(curl_errno($ch))
		{
			return array('error'=>true, 'message'=>'Curl error<< Error Number: (' . curl_errno($ch). ') Error Message: '. curl_error($ch). '>>');
		}
		curl_close($ch); 
		$user_data = json_decode($response,true);
		
		//*/
		
		$return_data = array(
			'id'          => $user_data['id'],
			'fullname'    => $user_data['name'],
			'firstname'   => $user_data['given_name'],
			'lastname'    => $user_data['family_name'],
			'email'       => $user_data['email'],
			'gender'      => $user_data['gender'],
			'image_url'   => $user_data['picture'],
			'profile_url' => $user_data['link']
		);
		
		return $return_data;
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
		/*
		* The OAuth 2.0 server responds to your application's access request by using the URL specified in the request.
		* If the user approves the access request, then the response contains an authorization code. 
		* If the user does not approve the request, the response contains an error message. 
		* All responses are returned to the web server on the query string, as shown below:
		*
		* An error response: https://oauth2-login-demo.appspot.com/auth?error=access_denied
		* An authorization code response: https://oauth2-login-demo.appspot.com/auth?code=4/P7q7W91a-oMsCeLvIaQm6bTrgtp7
		*
		* Important: If your response endpoint renders an HTML page, 
		* any resources on that page will be able to see the authorization code in the URL. 
		* Scripts can read the URL directly, 
		* and all resources may be sent the URL in the Referer HTTP header. 
		* Carefully consider if you want to send authorization credentials to all resources on that page 
		* (especially third-party scripts such as social plugins and analytics). 
		* To avoid this issue, we recommend that the server first handle the request, 
		* then redirect to another URL that doesn't include the response parameters.
		*/
		if(isset($_GET['code']))
		{
			$client = $this->get_client();
			
			/*
			* After the web server receives the authorization code, 
			* it can exchange the authorization code for an access token.
			* To exchange an authorization code for an access token, 
			* use the authenticate method:
			*/
			$client->authenticate($_GET['code']);
			
			/*
			* You can retrieve the access token with the getAccessToken method:
			*/
			$_SESSION[$this->get_access_token_session_name()] = $client->getAccessToken(); 
		}
		
		$client->setAccessToken($_SESSION[$this->get_access_token_session_name()]);
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