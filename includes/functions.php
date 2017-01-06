<?php
/*
* Core Framework API Functions
*/
function get_accepted_origins()
{
	$allowed_cors_origins = ItemModel::get_items( array('category'=>'allowed-cors-origins') );
	$accepted_origins = array(); //array("http://localhost", "http://127.0.0.1", "http://192.168.1.1");
	
	foreach($allowed_cors_origins AS $origin){
		$accepted_origins[] = $origin['value'];
	}
	return array_unique($accepted_origins);
}
function verify_request_origin($die = true, $message='')
{
	//same-origin requests won't set an origin.
	if (isset($_SERVER['HTTP_ORIGIN']))
	{
		//If the origin is set, ensure it is a valid origin.
		if (in_array($_SERVER['HTTP_ORIGIN'], get_accepted_origins()))
		{
			header('Access-Control-Allow-Origin: '. $_SERVER['HTTP_ORIGIN']);
		} 
		else 
		{
			header("HTTP/1.0 403 Origin Denied");
			if($die)
			{
				die($message);
			}
			
			return;
		}
    }
}
function get_db_instance()
{
	return Db::get_instance(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
}

function get_tables_prefix()
{
	return TABLES_PREFIX;
}

function import_admin_functions()
{
	include_once SITE_DIR. '/admin/admin-bootstrap.php';
}

function get_app_setting( $setting_key, $get_as_boolean = false )
{
	$setting = AppSettings::get_setting($setting_key);
	
	if($get_as_boolean)
	{
		$setting = strtolower($setting);
		return ( ( empty($setting) || $setting == 'false' ) ? false : true );
	}
	
	return $setting;
}

function update_app_settings($update_data)
{
	return AppSettings::update_settings($update_data);
}

function get_current_theme()
{
	return get_app_setting('active-theme');
}

function get_site_name()
{
	return get_app_setting('site-name');
}

function get_site_url()
{
	return SITE_URL;
}

function get_theme_url($theme='')
{
	$theme = !empty($theme) ? $theme : get_current_theme();
	return VIEWS_URL. '/'. $theme;
}

function is_development_server()
{
	if( stristr($_SERVER['HTTP_HOST'], 'local') || (substr($_SERVER['HTTP_HOST'], 0, 7) == '192.168') )
	{
		return true;
	}
	
	else
	{
		return false;
	}
}

function is_external_resource($resource)
{
	if(is_string($resource))
	{
		//if string is a full url, then it should contain our site name in it, otherwise, it is an external resource
		if( (stripos($resource, 'http') !== FALSE) && (stripos($resource, get_site_name()) === FALSE) )
		{
			return true;
		}
	}
	
	return false;
}

function load_plugin($opts=array())
{
	$plugins_directory = SITE_DIR. '/lib/plugins';
	
	$defs = array('plugin_folder_name'=>'', 'plugin_file_name'=>'', 'include_once'=>false, 'required'=>false);
	ArrayManipulator::copy_array($defs, $opts);
	foreach($defs AS $key => $value)
	{
		$$key = is_string($value) ? trim($value) : $value;
	}
	
	
	if(empty($plugin_file_name))
	{
		return;
	}
		
	$file_to_include = $plugins_directory. '/'. $plugin_folder_name. '/'. $plugin_file_name;
	if(!file_exists($file_to_include))
	{
		trigger_error('File. '. $file_to_include. ' not found', E_USER_ERROR);
		return;
	}
	if($include_once)
	{
		if($required)
		{
			require_once($plugins_directory. '/'. $plugin_folder_name. '/'. $plugin_file_name);
		}
		else
		{
			include_once($plugins_directory. '/'. $plugin_folder_name. '/'. $plugin_file_name);
		}
	}
	else
	{
		if($required)
		{
			require($plugins_directory. '/'. $plugin_folder_name. '/'. $plugin_file_name);
		}
		else
		{
			include($plugins_directory. '/'. $plugin_folder_name. '/'. $plugin_file_name);
		}
	}
}

function send_email( $opts = array() )
{
		$defs = array( 'to'=>'', 'from'=>'', 'subject'=>'', 'message'=>'', 'attachment'=>'', 'attachment_type'=> '', 'attachment_name'=>'');
		
		ArrayManipulator::copy_array($defs, $opts);
		foreach($defs AS $key => $value)
		{
			$$key = is_string($value) ? trim($value) : $value;
		}
		
		if( empty($to) || empty($message) )
		{
			return false;
		}
		
		if(!empty($attachment))
		{
			require_once(SITE_DIR. '/lib/phpmailer/PHPMailerAutoload.php');
			$mailer = new PHPMailer();                    
			$mailer->From = $from;
			$mailer->AddAddress($to);

			if($attachment_type == 'string') 
			{
				$mailer->AddStringAttachment($attachment, $attachment_name);
			}
			
			else
			{
				$mailer->AddAttachment($attachment);
			}
			
			$mailer->Subject = $subject; 
			$mailer->AltBody = "To view the message, please use an HTML compatible email viewer";
			$mailer->MsgHTML($message);
			//$mailer->Body    = $message;
			
			return $mailer->Send();
		}

		else
		{
			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8\r\n";
			$headers .= "To: ". $to. "\r\n";
			$headers .= "From: ". $from;

			return mail($to, $subject, $message, $headers);	
		}
}

/* Messages */
function get_mail_message($message, $placeholder_replacement_data=array())
{
	$message = htmlspecialchars_decode( get_app_setting($message) );
	
	foreach($placeholder_replacement_data AS $placeholder => $value)
	{
		$message = str_ireplace('{{'. $placeholder. '}}', $value, $message);
	}
	
	$placeholders = array( '{{site_name}}', '{{site_url}}' );
	$replacements = array( get_site_name(), get_site_url() );
	$message      = str_ireplace($placeholders, $replacements, $message);
	
	return $message;
}

/* URL and Request functions */
function get_protocol()
{
   return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
}

function url_contains_www($url)
{
	$arr = explode('www.', strtolower($url));
	return ( stripos($arr[0], 'www') !== FALSE );
}

function get_www_version($url)
{
	return ( url_contains_www($url) ? $url : str_ireplace('http://', 'http://www.', $url) );
}

function get_current_url()
{
	return rtrim( SITE_URL, '/' ). '/'. rtrim( new PathModel(), '/' );
}

function set_login_redirect_url($url)
{
	$_SESSION['login_redirect_url'] = $url;
}

function get_login_redirect_url($unset = true)
{
	$login_redirect_url = SITE_URL;
	
	if( !empty($_SESSION['login_redirect_url']) )
	{
		$login_redirect_url = $_SESSION['login_redirect_url'];
		
		if($unset)
		{
			unset($_SESSION['login_redirect_url']);
		}
	}
	
	return $login_redirect_url;
}

function is_ajax_request()
{
    return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
}

function get_request_url()
{
	if (isset($_SERVER['PATH_INFO']))
	{
		$path = (substr($_SERVER['PATH_INFO'], -1) == "/") ? substr($_SERVER['PATH_INFO'], 0, -1) : $_SERVER['PATH_INFO'];
	}
	else
	{ 
		$path = (substr($_SERVER['REQUEST_URI'], -1) == "/") ? substr($_SERVER['REQUEST_URI'], 0, -1) : $_SERVER['REQUEST_URI'];
	}
		
	$bits = explode("/", substr($path, 1));
		
	if( stristr(UrlInspector::get_base_url(), 'localhost') !== FALSE )
	{ 
		array_shift($bits); //if on localhost, then the leading path is the folder containing the site, so remove that
		array_shift($bits); //on my system where we have /sites/site_folder_name
	}
	
	$requested_url = implode('/', $bits);
	
	return SITE_URL. '/'. $requested_url;
}

function get_request_url2()
{
	if ( !empty($_SERVER['SCRIPT_URI']) )
	{
        $url = $_SERVER['SCRIPT_URI'];
	}
    
	else
	{
        $url = $_SERVER['REQUEST_SCHEME'] . '://' .$_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	}
	
	return $url;
}

function generate_url($opts)
{
	extract($opts); //controller, action, $qs = array of query-string key/value pairs
		
	$extras = '';
		
	if( !empty($qs) && is_array($qs) )
	{
		foreach($qs AS $value)
		{
			$extras .= $value. '/';
		}
	}
		
	$controller = !empty($controller) ? trim($controller) : '';
	$action     = !empty($action)     ? trim($action)     : '';
		
	$url = $url = rtrim(SITE_URL, "/");
		
	if( !empty( $controller ) )
	{
		$url .= "/". $controller;
	}
		
	if( !empty( $action ) )
	{
		$url .= "/". $action;
	}
		
	if( !empty($extras) )
	{
			$url .= '/'. $extras;
	}
		
	return $url;
}

/*
* $opts data members:
* uri string the uri whose end point to get [optional] defaults to current uri
* ignore_query_string boolean, whether to ignore the query string part of the uri
*/
function get_uri_end_point( $opts = array() )
{
	extract($opts);
	
	$uri = empty($uri) ? get_request_url() : $uri;
	$ignore_query_string = isset($ignore_query_string) ? $ignore_query_string : false;
	$points = explode('/', $uri);
	
	$end_point = $points[sizeof($points)-1];
	
	if( $ignore_query_string )
	{
		if( stripos($uri, '/?') !== FALSE )
		{ 
			/*
			* e.g http://localhost/sites/naija-so/users/5/mikky-mouse/edit/?tab=education, 
			* the end point here should be the /edit/. 
			* The '?tab=education' only provides additional info for the end point uri
			*/
			$end_point = $points[sizeof($points)-2]; 
		}
		
		else if( stripos($end_point, '?') !== FALSE )
		{
			/*
			* e.g http://localhost/sites/naija-so/users/5/mikky-mouse/edit?tab=education
			* end point here is edit?tab=education, but we only need the 'edit' part of the string
			*/
			$end_point = substr($end_point, 0, stripos($end_point, '?'));
		}
	}
	 
	return $end_point;
}

function determine_query_string_separator($url)
{
	return ( (stristr($requested_url, '?') === FALSE) ? '?' : '&' );
}

function url_contains_query_string($url)
{
	//@credit: http://stackoverflow.com/a/7864244/1743192
	return parse_url($url, PHP_URL_QUERY);
}

function get_slug($title)
{
	return Slug::makeSlugs($title);
	//return strtolower(str_replace(array(" ", ",", "~'~", "?", "(", ")"), array('-', '-', '-', '', '-', '-'), $title));
}


/* User functions */

/* 
* get a user's id either by ID, email, or username
*/
function get_user_id($identifier)
{
	if(is_numeric($identifier))
	{
		return $identifier;
	}
	else if(is_valid_email($identifier))
	{
		return email_exists($identifier) ? UserModel::get_users(array('email'=>$identifier), array(), 1)[0]['id'] : 0;
	}
	else
	{
		return username_exists($identifier) ? UserModel::get_users(array('username'=>$identifier), array(), 1)[0]['id'] : 0;
	}
}

function get_valid_user_id($user_id = '')
{
	return empty($user_id) ? UserModel::get_current_user_id() : $user_id;
}

function verify_user_logged_in()
{
	if(!UserModel::user_is_logged_in())
	{
		header("Location:". SITE_URL);
		exit;
	}
}

function assign_role_to_user($user_id, $role)
{
	UserModel::update_user_data( get_valid_user_id($user_id), array ( array('data_key'=>'role', 'data_value'=>$role, 'overwrite'=>true) ) );
}

/*
* e.g of $query_data : array('email'=>$email)
*/
function get_users_by($query_data, $orders=array(), $limit = 1)
{
	$users = UserModel::get_users($query_data, $orders, $limit);
	
	if(empty($users))
	{
		return array();
	}
	
	return ( $limit == 1 ? $users[0] : $users );
}

function get_user_data($user_id='', $data_key = '', $default_value='')
{
	$user_id = get_valid_user_id($user_id);
	$user_data = UserModel::get_user_data($user_id);
	
	return empty($data_key) ? $user_data : get_array_member_value($user_data, $data_key, $default_value);
}

function username_exists($username)
{
	$user_data = UserModel::get_users(array('username'=>$username), array(), 1);
	return !empty($user_data);
}

function generate_username($user_id)
{
	$username = UserModel::get_user_data($user_id, 'username');
	if(empty($username))
	{
		$username = 'user_'. str_pad(generate_random_string('123456789', 6), 6, "0", STR_PAD_RIGHT);
		while(username_exists($username))
		{
			$username = 'user_'. str_pad(generate_random_string('123456789', 6), 6, "0", STR_PAD_RIGHT);
		}
		UserModel::update_user_data( $user_id, array( array('data_key'=>'username', 'data_value'=>$username) ) );
	}
}

function email_exists($email)
{
	if( !is_valid_email($email) )
	{
		return false;
	}
	else
	{
		$user_data = UserModel::get_users(array('email'=>$email), array(), 1);
		return !empty($user_data);
	}
}

function is_valid_email($email)
{
	return EmailValidator::is_valid_email($email);
}

/*
* This function is called inside the base controller on every page request by a logged-in user.
* You don't necessarily have to call this function directly,
* (unless in an Ajax request end-point: The one place we call it directly is in /ajax/users.php)
* Instead, you call the get_user_data('last-seen-time') to get you the last seen data for the specified user
*/
function update_user_last_seen_data($user_id = '', $last_seen_url='')
{
	$path          = new PathModel();
	$last_seen_url = isset($last_seen_url) ? trim($last_seen_url) : get_current_url(); //rtrim( SITE_URL, '/'). '/'. ltrim($path, '/');
	update_user_data( get_valid_user_id($user_id), array( 'last-seen-time'=>time(), 'last-seen-url'=>$last_seen_url ) );
}

/*
* Used to update user data that should be unique
* e.g login, password, firstname, etc.
* Don't use for data that can have multiple values: e.g emails (where the user can have more than one email associated with their account)
*/
function update_user_data($user_id, $data)
{
	foreach($data AS $key => $value)
	{
		$update_data[] = array('data_key'=>$key, 'data_value'=>$value, 'overwrite'=>true);
	}
	
	return UserModel::update_user_data($user_id, $update_data);
}

function update_user_login_status($user_id, $status)
{
	//$user = UserModel::get_user_instance($user_id);
	//$user->update('login-status', $status, $overwrite = true);
	update_user_data($user_id, array('login-status'=>strtolower($status)));
}

function user_is_online($user_id)
{
	$user = UserModel::get_user_instance($user_id);
	
	if( $user->get('login-status') == 'offline' )
	{
		return false;
	}
	
	if( time() < ( $user->get('last-seen-time') + get_app_setting('session-expiry') )  )
	{
		//user's session has expired
		return false;
	}
	
	return true;
}

/** Utility functions */

function extract_urls_from_text($text)
{
	//pattern credits: http://daringfireball.net/2010/07/improved_regex_for_matching_urls
	$pattern  = "#".
	            "(?i)\b((?:https?://|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)".
	            "(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+".
	            "(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\"".
	            ".,<>?«»“”‘’]))".
				"#i"; //I added the 'i' (case-insensitive) modifier
	
	//pattern credits: https://gist.github.com/gruber/8891611 (updated version of above)
	$pattern = '#'.
	           '(?i)\b((?:https?:(?:/{1,3}|[a-z0-9%])'.
	           '|[a-z0-9.\-]+[.]'.
			   '(?:com|net|org|edu|gov|mil|aero|asia|biz|cat|coop|info|int|jobs|mobi|museum|name|post|pro|tel|travel|xxx|ac|ad|ae|af|ag|ai|al|'.
			   'am|an|ao|aq|ar|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|'.
			   'cs|cu|cv|cx|cy|cz|dd|de|dj|dk|dm|do|dz|ec|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|'.
			   'gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|'.
			   'mc|md|me|mg|mh|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|ps|pt|'.
			   'pw|py|qa|re|ro|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|Ja|sk|sl|sm|sn|so|sr|ss|st|su|sv|sx|sy|sz|tc|td|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|'.
			   'tz|ua|ug|uk|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)/)'.
			   '(?:[^\s()<>{}\[\]]+|\([^\s()]*?\([^\s()]+\)[^\s()]*?\)|\([^\s]+?\))+(?:\([^\s()]*?\([^\s()]+\)[^\s()]*?\)|\([^\s]+?\)|[^\s`!()\[\]{};:\'"'.
			   '.,<>?«»“”‘’])|(?:(?<!@)[a-z0-9]+(?:[.\-][a-z0-9]+)*[.]'.
			   '(?:com|net|org|edu|gov|mil|aero|asia|biz|cat|coop|info|int|jobs|mobi|museum|name|post|pro|tel|travel|xxx|ac|ad|ae|af|ag|ai|al|'.
			   'am|an|ao|aq|ar|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|'.
			   'cs|cu|cv|cx|cy|cz|dd|de|dj|dk|dm|do|dz|ec|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|'.
			   'gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|'.
			   'mc|md|me|mg|mh|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|ps|pt|'.
			   'pw|py|qa|re|ro|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|Ja|sk|sl|sm|sn|so|sr|ss|st|su|sv|sx|sy|sz|tc|td|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|'.
			   'tz|ua|ug|uk|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)\b/?(?!@)))'.
			   '#i'; //I added the 'i' (case-insensitive) modifier

	preg_match_all($pattern, $text, $matches, PREG_PATTERN_ORDER);
	return $matches[0];
}

function escape_output_string($str)
{
	return DataSanitizer::escape_output_string($str);
}

function generate_random_string($allowed_chars='123456789', $str_length=10)
{
	$generator  = new RandomStringGenerator($allowed_chars);
	return $generator->generate($str_length);
}

/*
* sort multidimensional array by given key
*/
function sort_by_key_value($the_array, $key, $sort_order = 'DESC')
{
	if(!function_exists('sort_by_order'))
	{
		//'use' idea credits: http://stackoverflow.com/a/4938280/1743192
		$sort_by_order = function ($a, $b)  use ($key, $sort_order)
		{
			if( strtoupper($sort_order) == 'DESC')
			{
				return ( ($a[$key] > $b[$key]) ? -1 : ( ($a[$key] == $b[$key]) ? 0 : 1 ) );
			}
	
			else
			{
				return ( ($a[$key] > $b[$key]) ? 1 : ( ($a[$key] == $b[$key]) ? 0 : -1 ) );
			}
		}; //must put a semicolon, since it is a declaration-definition
	}
	uasort($the_array, $sort_by_order);
	
	/*
	$cmp_val="((\$a['$key']>\$b['$key'])?1:((\$a['$key']==\$b['$key'])?0:-1))";
	$cmp=create_function('$a, $b', "return $cmp_val;");
	uasort($the_array, $cmp);
	*/
	
	return $the_array;
}

function parse_file_contents($file, $file_data=array())
{
	ob_start(); // start output buffer
	extract($file_data);
	include $file;
		
	$parsed_content = ob_get_contents(); // get contents of buffer
		
	ob_end_clean();
	return $parsed_content;
}

/*
* returns a file name without the extension
*/
function get_file_name($file_name)
{
	return FileInspector::get_file_name($file_name); //substr( $file_name, 0,  strrpos($file_name, '.') );
}

function get_file_extension($file_name)
{
	return FileInspector::get_file_extension($file_name); //substr( $file_name, strrpos($file_name, '.')+1);
}

/*
* Return the value of an array member:
* Basically helps us avoid direct testing inside the script file.
* E.g if we have an array $user_data = ('name'=>'Michael', 'email'=>'abc@xyz.com')
* we could obtain its email member value using : get_array_member_value($user_data, 'email')
* We could also specify an optional value to return if the specified member value does not exist.
* E.g to obtain a non-existent firstname member, (with a fallback firstname to return), we do
* get_array_member_value($user_data, 'firstname', 'James')
*/
function get_array_member_value( $array_name, $member_index, $optional_return_value='' )
{
	return ( isset($array_name[$member_index]) ? htmlspecialchars($array_name[$member_index]) : $optional_return_value );
}

function make_remote_request($url, $opts=array())
{
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	
	curl_setopt_array($ch, $opts);
	
	$response = curl_exec($ch);

	if(curl_errno($ch))
	{
		return array('error'=>true, 'message'=>'Curl error<< Error Number: (' . curl_errno($ch). ') Error Message: '. curl_error($ch). '>>');
	}
		
	curl_close($ch); 

	return $response;
}

/*
* @credits: Stoyan Stefanov http://www.phpied.com/simultaneuos-http-requests-in-php-with-curl/
* @modified Michael Orji
*/
function do_parallel_curl_request($data, $options = array())
{
	ini_set('max_execution_time', 500); //300 seconds = 5 minutes
	
	$curly  = array(); // array of curl handles
	$result = array(); // data to be returned
	$mh     = curl_multi_init(); // multi handle
 
	/*
	* loop through $data and create curl handles
	* then add them to the multi-handle
	*/
	foreach ($data as $id => $d)
	{
		$curly[$id] = curl_init();
 
		$url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
		curl_setopt($curly[$id], CURLOPT_URL,            $url);
		curl_setopt($curly[$id], CURLOPT_HEADER,         0);
		curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);
 
		// post?
		if (is_array($d))
		{
			if (!empty($d['post']))
			{
				curl_setopt($curly[$id], CURLOPT_POST,       1);
				curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $d['post']);
			}
			
			/*
			* added by Michael Orji
			* allows you set individual CURL options for each (POST) request
			*/
			if(!empty($d['options']))
			{
				curl_setopt_array($curly[$id], $d['options']);
			}
		}
 
		// extra options?
		if (!empty($options))
		{
			curl_setopt_array($curly[$id], $options);
		}
 
		curl_multi_add_handle($mh, $curly[$id]);
	}
 
	// execute the handles
	$running = null;
	do 
	{
		curl_multi_exec($mh, $running);
	} while($running > 0);
 
 
	// get content and remove handles
	foreach($curly as $id => $c)
	{
		$result[$id] = curl_multi_getcontent($c);
		curl_multi_remove_handle($mh, $c);
	}
 
	// all done
	curl_multi_close($mh);
 
	return $result;
	
	/**
	* Usage Examples:
	* 
	* 1. GET Example: 
	* $data = array(
	*	'http://search.yahooapis.com/VideoSearchService/V1/videoSearch?appid=YahooDemo&query=Pearl+Jam&output=json',
	*	'http://search.yahooapis.com/ImageSearchService/V1/imageSearch?appid=YahooDemo&query=Pearl+Jam&output=json',
	*	'http://search.yahooapis.com/AudioSearchService/V1/artistSearch?appid=YahooDemo&artist=Pearl+Jam&output=json'
	*	);
	*	$r = multiRequest($data);
	* echo '<pre>';
	* print_r($r);
	*
	* 2. POST Example:
	* $data = array(array(),array());
	* $data[0]['url']  = 'http://search.yahooapis.com/ContentAnalysisService/V1/termExtraction';
	* $data[0]['post'] = array();
	* $data[0]['post']['appid']   = 'YahooDemo';
	* $data[0]['post']['output']  = 'php';
	* $data[0]['post']['context'] = 'Now I lay me down to sleep,
                               I pray the Lord my soul to keep;
                               And if I die before I wake,
                               I pray the Lord my soul to take.';
	* $data[1]['url']  = 'http://search.yahooapis.com/ContentAnalysisService/V1/termExtraction';
	* $data[1]['post'] = array();
	* $data[1]['post']['appid']   = 'YahooDemo';
	* $data[1]['post']['output']  = 'php';
	* $data[1]['post']['context'] = 'Now I lay me down to sleep,
                               I pray the funk will make me freak;
                               If I should die before I waked,
                               Allow me Lord to rock out naked.';
	* $r = multiRequest($data);
	* print_r($r);
	*/
}

function create_json_string($data=array(), $output=false)
{
	$str = '{';
		
	foreach($data AS $key => $value)
	{
		$value = ( is_string($value) ? ('"'. $value. '"') : $value );
		$str  .= '"'. $key. '":'. $value. ', ';
	}
	$str  = substr($str, 0, -2); //remove trailing space and comma (, )
	$str .= '}';
		
	if($output)
	{
		header("Content-Type: application/json");
		echo $str;
	}
		
	return $str;
}

function extract_number_from_string($string_with_number)
{
	if(!is_string($string_with_number))
	{
		return $string_with_number;
	}
	
	$string_arr = str_split($string_with_number);
	
	if( count($string_arr) <= 0)
	{
		return 0;
	}
	if( count($string_arr) == 1 )
	{
		return is_numeric($string_arr[0]) ? $string_arr[0] : 0;
	}
	
	while(!is_numeric($string_arr[0]))
	{
		array_shift($string_arr); //remove any non-numeric characters from the string
	}
	return join('', $string_arr);
}

function format_number($number, $num_of_decimals = 2)
{
	$number = extract_number_from_string($number);
	$number = number_format($number, $num_of_decimals);
	return $number;
}

//credits: http://stackoverflow.com/a/11096805/1743192
function format_count($num, $point='.', $sep=',')
{
    if ($num < 0) 
	{
        return 0;
    }

    if ($num < 10000)
	{
        return number_format($num, 0, $point, $sep);
    }

    $d = $num < 1000000 ? 1000 : 1000000;
    $f = round($num / $d, 1);

    return number_format($f, $f - intval($f) ? 1 : 0, $point, $sep) . ($d == 1000 ? 'k' : 'M');
}

/*
* $data members:
* query_count int number of results returned by the query to paginate,
* per_page int number of results to dipslay per page,
* current_page_number int the (current) page number, default is 1
* url string the url
* previous_page_link_label
* next_page_link_label
* first_page_link_label
* last_page_link_label
* pages_pre_ellipsis int the number of pages to the left of the ellipsis
* container_class string the css class for the parent <ul> element holding each page <li>
* page_item_class string the css class for a single <li> page item
* current_page_class string the css class for the <a> holding the current page
* ellipsis_class the css class for the ellipsis (...) container
* page_detection_query_string string the query string to pass along with the url to know which page user currently is on
*/
function paginate($data)
{ 
	extract($data);
	
	$total                       = $query_count;
	$adjacents                   = 2; 
	$pages_pre_ellipsis          = isset($pages_pre_ellipsis)  ? $pages_pre_ellipsis  : "7";
	$prevlabel                   = isset($previous_page_link_label) ? $previous_page_link_label : "&lsaquo; Prev";
	$nextlabel                   = isset($next_page_link_label)     ? $next_page_link_label     : "Next &rsaquo;";
	$lastlabel                   = isset($last_page_link_label)     ? $last_page_link_label     : "Last &rsaquo;&rsaquo;";
	$firstlabel                  = isset($first_page_link_label)    ? $first_page_link_label    : "&lsaquo;&lsaquo; First";
	$container_class             = isset($container_class)    ? $container_class    : "pagination";
	$page_item_class             = isset($page_item_class)    ? $page_item_class    : "";
	$current_page_class          = isset($current_page_class) ? $current_page_class : "current";
	$ellipsis_class              = isset($ellipsis_class)     ? $ellipsis_class     : "dot";
	$page_detection_query_string = isset($page_detection_query_string) ? $page_detection_query_string : "pagination-page"; 
	$page                        = ($current_page_number == 0 ? 1 : $current_page_number);  
	$start                       = ($page - 1) * $per_page;                               
	$prev                        = $page - 1;                          
	$next                        = $page + 1;
	$lastpage                    = ceil($total/$per_page);
	$lpm1                        = $lastpage - 1; //last page minus 1

	$pagination                  = "";
	
	if($lastpage > 1)
	{   
		$pagination .= "<ul class='$container_class'>";
		//$pagination .= "<li class='page_info'>Page {$page} of {$lastpage}</li>";

        if ($page > 1)
		{
			$pagination.= "<li><a class='$page_item_class' href='{$url}{$page_detection_query_string}=1'>{$firstlabel}</a></li>";
			$pagination.= "<li><a class='$page_item_class' href='{$url}{$page_detection_query_string}={$prev}'>{$prevlabel}</a></li>";
		}
        
		/*
		* If the last page number is less than the number of links to show - including the  next, prev, first, and last links -
		* then display everything (?)
		*/
		if ( $lastpage < (7 + ($adjacents * 2)) )
		//if ($lastpage < $pages_pre_ellipsis + ($adjacents * 2) ) 
		{   
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
				{
					$pagination.= "<li><a class='$current_page_class'>{$counter}</a></li>";
				}
				else
				{
					$pagination.= "<li><a class='$page_item_class' href='{$url}{$page_detection_query_string}={$counter}'>{$counter}</a></li>"; 
				}
			}
		}
		
		elseif( $lastpage > (5 + ($adjacents * 2)) )
		{
			if( $page < (1 + ($adjacents * 2)) )
			{ 
				//for ($counter = 1; $counter <= $pages_pre_ellipsis; $counter++)
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $page)
					{
						$pagination.= "<li><a class='$current_page_class'>{$counter}</a></li>";
					}
					
					else
					{
						$pagination.= "<li><a class='$page_item_class' href='{$url}{$page_detection_query_string}={$counter}'>{$counter}</a></li>";    
					}
				}
				
				$pagination.= "<li><a class='$ellipsis_class'>...</a></li>";
				$pagination.= "<li><a class='$page_item_class' href='{$url}{$page_detection_query_string}={$lpm1}'>{$lpm1}</a></li>";
				$pagination.= "<li><a class='$page_item_class' href='{$url}{$page_detection_query_string}={$lastpage}'>{$lastpage}</a></li>";   
			}
			
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{ 
				$pagination.= "<li><a class='$page_item_class' href='{$url}{$page_detection_query_string}=1'>1</a></li>";
				$pagination.= "<li><a class='$page_item_class' href='{$url}{$page_detection_query_string}=2'>2</a></li>";
				$pagination.= "<li><a class='$ellipsis_class'>...</a></li>";
				
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
					{
						$pagination.= "<li><a class='$current_page_class'>{$counter}</a></li>";
					}
					
					else
					{
						$pagination.= "<li><a class='$page_item_class' href='{$url}{$page_detection_query_string}={$counter}'>{$counter}</a></li>"; 
					}
				}
				
				$pagination.= "<li><a class='$ellipsis_class'>...</a></li>";
				$pagination.= "<li><a class='$page_item_class' href='{$url}{$page_detection_query_string}={$lpm1}'>{$lpm1}</a></li>";
				$pagination.= "<li><a class='$page_item_class' href='{$url}{$page_detection_query_string}={$lastpage}'>{$lastpage}</a></li>";      
			}
			
			else
			{ 
				$pagination.= "<li><a class='$page_item_class' href='{$url}{$page_detection_query_string}=1'>1</a></li>";
				$pagination.= "<li><a class='$page_item_class' href='{$url}{$page_detection_query_string}=2'>2</a></li>";
				$pagination.= "<li><a class='$ellipsis_class'>...</a></li>";
				
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) 
				{
					if ($counter == $page)
					{
						$pagination.= "<li><a class='$current_page_class'>{$counter}</a></li>";
					}
					
					else
					{
						$pagination.= "<li><a class='$page_item_class' href='{$url}{$page_detection_query_string}={$counter}'>{$counter}</a></li>"; 
					}
				}
			}
		}
       
        if ($page < $counter - 1)
		{
            $pagination.= "<li><a class='$page_item_class' href='{$url}{$page_detection_query_string}={$next}'>{$nextlabel}</a></li>";
            $pagination.= "<li><a class='$page_item_class' href='{$url}{$page_detection_query_string}=$lastpage'>{$lastlabel}</a></li>";
        }
          
		$pagination.= "</ul>";        
	}
      
	return $pagination;
}

function get_form_field_value($form_field_name)
{
	global $$form_field_name;
	return !empty($$form_field_name) ? $$form_field_name : '';
}

function is_selected_radio_button($button_value, $group_name)
{
	return isset($group_name) && ($group_name == $button_value);
}

function set_as_selected_option($option_value, $selected_value)
{
	return $option_value == $selected_value ? 'selected="selected"' : '';
}

function set_as_selected_radio_button($button_value, $group_name)
{
	return ( isset($group_name) && ($group_name == $button_value) ? 'checked' : '' );
}

function set_as_checked($checkbox_value_name)
{
	return !empty($checkbox_value_name) ? 'checked="checked"' : '';
}

function generate_data_rows($data, $recursive=true)
{
	$tbl_contents = '';
	foreach($data AS $key => $value)
	{
		if( $recursive && is_array($value) )
		{
			$tbl_contents .= generate_data_rows($value);
		}
		else
		{
			$tbl_contents .= "<tr><td>$key</td><td>$value</td></tr>";
		}
	}
	
	$tbl_contents .= '';
	return $tbl_contents;
}

function generate_html_mail( $opts = array() )
{
	$defs = array('title'=>'', 'message'=>'');
	
	ArrayManipulator::copy_array($defs, $opts);
	
	foreach($defs AS $key => $value)
	{
		$$key = is_string($value) ? trim($value) : $value;
	}
	
	$title    = get_site_name(). ' - '. $title;
	$site_url = SITE_URL;
	
$message_tpl = <<<EOD
<!doctype html>
<html>
 <head>
  <meta charset="utf-8">
  <title>$title</title>
  <link href='//fonts.googleapis.com/css?family=Lato' rel='stylesheet'>
  <link href='//fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
 </head>
 <body>$message</body>
</html>
EOD;

return $message_tpl;
}
	
function sanitize_html_attribute($text)
{
	return htmlspecialchars($text);
}

function sanitize_for_browser_display($text)
{
	return $text;
}

function get_substring($str, $max_len=50, $ellipsis = '...')
{
	if(strlen($str) <= $max_len)
	{
		return $str;
	}
	else
	{
		return substr($str, 0, $max_len). $ellipsis;
	}
}



/** Date and Time functions */

/*
* default format returns sql date-time format: 2016-03-27 14:10:44
*/
function format_time( $timestamp = '', $format = 'Y-m-d H:i:s' )
{
	$timestame = empty($timestamp) ? time() : $timestamp;
	return date($format, $timestamp);
}

function format_date($date, $format = 'F d, Y H:i:s')
{
	return format_time(strtotime($date), $format);
	//return date( $format, strtotime($date) );
}

function get_time_elapsed($start, $return_highest=true, $end='')
{
	$data = array(
		'years'   => get_difference($start, 'y', $end),
		'months'  => get_difference($start, 'm', $end),
		'days'    => get_difference($start, 'd', $end),
		'hours'   => get_difference($start, 'h', $end),
		'minutes' => get_difference($start, 'i', $end),
		'seconds' => get_difference($start, 's', $end),
	);
	
	if($return_highest)
	{
		foreach($data AS $k => $v)
		{
			if(!empty($v))
			{
				return $v. ' '. $k;
			}
		}
	}
	return $data;
}

/*
* Cf. http://ca2.php.net/manual/en/dateinterval.format.php
* Most used formats for this app:
* y, m, d, h, i, s, 
*/
function get_difference($start, $format, $end ='')
{
    $start_date = new DateTime($start);
	$end_date   = new DateTime($end);
	$interval   = $end_date->diff($start_date);
	return $interval->format('%'. $format);
}

//$offset e.g -7:00
function convert_offset_to_timezone($offset)
{
	//credits: http://stackoverflow.com/a/11896631/1743192
	
	// Calculate seconds from offset
	//list($hours, $minutes) = explode(':', $offset);
	
	$arr     = explode(':', $offset);
	$hours   = $arr[0];
	$minutes = isset($arr[1]) ? $arr[1]: 0;
	$seconds = $hours * 60 * 60 + $minutes * 60;
	
	// Get timezone name from seconds
	$tz = timezone_name_from_abbr('', $seconds, 1);
	
	// Workaround for bug #44780
	if($tz === false) $tz = timezone_name_from_abbr('', $seconds, 0);
	
	return $tz;
}