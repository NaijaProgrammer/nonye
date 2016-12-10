<?php
/*
function get_users_by_role($role)
{
	$users       = UserModel::get_users( array('role'=>$role) );
	$query_str   = UserModel::get_users_query_string( array('role'=>$role) );
	$return_data = array();
		
	foreach($users AS $curr_user)
	{   
		$user_role = self::get_user_role($curr_user['id']);
			
		if( strtolower($user_role) == strtolower($role) )
		{
			$return_data[] = $curr_user_data;
		}
	}
	
	return $return_data;
	//return array( 'data'=>$return_data, 'query_string'=>$query_str );
}
*/

/*
* @return associative array with members:
 'Local URI' string local theme url, that is its location on current site
 'Stylesheet' string the url of the stylesheet file
 'Screenshot' string the url of the screenshot.png file for the theme
 'Name' string      the theme's name
 'URI'  string      the theme's hosted url (from where the theme can be downloaded)
 'Description' string 
 'Author' string      theme author name
 'Author URI' string  theme author url
 'Version' 
 'License' 
 'License URI' 
 'Tags' 
 'Text Domain
*/
function get_theme_info($theme_dir)
{
	$theme_url    = get_theme_url($theme_dir);
	$style_url    = $theme_url. '/css/theme.css';
	$theme_css    = file_get_contents($style_url);
	$css_arr      = explode('*/', $theme_css); //explode it at the closing of the initial block-comment area
	$theme_info   = $css_arr[0]; //The very first block-comment holds the theme's info like: Name, URI, Author, Author URI, Description, etc
	$info_parts   = explode(PHP_EOL, $theme_info); //Each theme info begins on a new line, so split in new lines
	array_shift($info_parts); //remove the first line element "/*"
	array_pop($info_parts); //remove the last line element empty string
	
	$theme_data = array('Local URI'=>$theme_url, 'Stylsheet'=>$style_url, 'Screenshot'=>$theme_url. '/screenshot.png');
	
	for($i = 0, $len = count($info_parts); $i < $len; $i++)
	{
		$part_str = $info_parts[$i];
		$part_str = trim( trim($part_str), '*' ); //e.g  * Name : Default
		$part_arr = explode(':', $part_str); //e.g Name : Default, Author : Michael Orji, etc.
		$theme_data[trim($part_arr[0])] = isset($part_arr[1]) ? trim($part_arr[1]) : '';
	}
	
	return $theme_data;
}
function get_user_role($user_id = 0)
{
	return UserModel::get_user_instance( get_valid_user_id($user_id) )->get('role', $default_role = 'user');
}

function get_user_capabilities($user_id = 0)
{
	return UserModel::get_user_instance( get_valid_user_id($user_id) )->get('capabilities', array());
}

function is_admin($user_id = 0)
{
	return  strtolower(get_user_role($user_id)) == 'admin';
}

function is_super_admin($user_id = 0)
{
	return strtolower(get_user_role($user_id)) == 'super admin';
}

function super_admin_exists()
{
	$super_admin = UserModel::get_users( array('role'=>'Super Admin') );
	return !empty($super_admin);
}

function capability_is_enabled_for_all_users($capability)
{
	$capability_id   = get_capability_id($capability);
	$capability_data = ItemModel::get_item_data($capability_id);
	
	return ( isset($capability_data['enabled-for-all-users']) && ($capability_data['enabled-for-all-users'] == 'yes') );
}

function user_has_capability($capability, $user_id=0)
{
	if(capability_is_enabled_for_all_users($capability))
	{
		return true;
	}
	
	$user_id           = get_valid_user_id($user_id);
	$user_capabilities = get_user_capabilities($user_id);
	
	if(empty($user_capabilities))
	{
		return false;
	}
	elseif( is_string($user_capabilities) )
	{
		return ($user_capabilities == $capability);
	}
	else
	{
		return in_array($capability, $user_capabilities);
	}
}

function grant_capability_to_user($user_id, $capability)
{
	if(user_has_capability($capability, $user_id))
	{
		return;
	}
	
	UserModel::update_user_data( get_valid_user_id($user_id), array ( array('data_key'=>'capabilities', 'data_value'=>$capability, 'overwrite'=>false) ) );
}

function revoke_user_role($user_id)
{
	UserModel::update_user_data( $user_id, array ( array('data_key'=>'role', 'data_value'=>' ', 'overwrite'=>true) ) );
}

function revoke_user_capabilities($user_id, $capabilities = array())
{
	if(empty($capabilities))
	{
		return get_db_instance()->delete_records( get_tables_prefix(). 'user_meta', array('user_id'=>$user_id, 'meta_key'=>'capabilities'), $limit = '');
	}
	else
	{
		foreach($capabilities AS $capability)
		{
			revoke_user_capability($capability);
		}
	}
}

function revoke_user_capability($user_id, $capability)
{
	if(user_has_capability($capability, $user_id))
	{
		return get_db_instance()->delete_records( get_tables_prefix(). 'user_meta', array('user_id'=>$user_id, 'meta_key'=>'capabilities', 'meta_value'=>$capability) );
	}
}

function user_can($capability, $user_id = 0)
{
	$user_id           = get_valid_user_id($user_id);
	$user_role         = get_user_role($user_id);
	$user_capabilities = get_user_capabilities($user_id); //UserModel::get_user_data($user_id, 'capabilities');
	
	$user_has_capability = ( is_array($user_capabilities) ? in_array($capability, $user_capabilities) : $user_capabilities == $capability );
	
	return is_super_admin($user_id) || role_has_capability($user_role, $capability) || $user_has_capability;
	//return is_super_admin($user_id) || role_has_capability($user_role, $capability) || in_array($capability, $user_capabilities);
}

function verify_super_admin($user_id = 0)
{
	if(!is_super_admin($user_id))
	{
		$ap_instance = AdminPage::get_instance();
		$ap_instance->load_header('', array('page_title'=>'Access Denied'));
		$ap_instance->load_nav();
		$ap_instance->load_fragment('user-capability-error', array('capability'=>'Super Admin'));
		$ap_instance->load_footer();
		exit;
	}
}
	
function verify_user_can($capability, $user_id=0)
{
	if(!user_can($capability, $user_id))
	{
		$ap_instance = AdminPage::get_instance();
		$ap_instance->load_header('', array('page_title'=>'Access Denied'));
		$ap_instance->load_nav();
		$ap_instance->load_fragment('user-capability-error', array('capability'=>$capability, 'page_title'=>'Access Denied'));
		$ap_instance->load_footer();
		exit;
	}
}

function create_new_role($data)
{
	$name = trim($data['name']);
	$description = trim($data['description']);
	
	if(empty($name))
	{
		return false;
	}
	
	if(role_exists($name))
	{
		return get_role_id($name);
	}
	
	return ItemModel::add_item( array('name'=>$name, 'description'=>$description, 'category'=>'available-user-roles', 'creator_id'=>UserModel::get_current_user_id()) );
}

function get_roles()
{
	return ItemModel::get_items( array('category'=>'available-user-roles') );
}

function get_role_id($role)
{
	$role_id = ItemModel::get_items( array('category'=>'available-user-roles', 'name'=>$role, 'data_to_get'=>array('id'))  );
	if(empty($role_id))
	{
		return 0;
	}
	return is_array($role_id) ? $role_id[0] : $role_id;
}

function role_exists($role)
{
	$role_id = get_role_id($role);
	return !empty($role_id) ? $role_id : false;
}

function grant_capability_to_role($role, $capability)
{
	$bind_id = role_has_capability($role, $capability);
	
	if( $bind_id )
	{
		return $bind_id;
	}
	
	$role_id       = get_role_id($role);
	$capability_id = get_capability_id($capability);
	
	return ItemModel::add_item( array('category'=>'capability-to-role-binds', 'capability-id'=>$capability_id, 'role-id'=>$role_id) );
}

function revoke_role_capability($role, $capability)
{
	$bind_id = role_has_capability($role, $capability);
	if($bind_id)
	{
		ItemModel::delete_item($bind_id, array('remove_records'=>true));
	}
}
	
function role_has_capability($role, $capability)
{
	$role_id       = get_role_id($role);
	$capability_id = get_capability_id($capability);
	$data          = ItemModel::get_items( array('category'=>'capability-to-role-binds', 'capability-id'=>$capability_id, 'role-id'=>$role_id) );
	return !empty($data) ? $data[0]['id'] : false;
}

function create_new_capability($data)
{
	$name = trim($data['name']);
	$description = trim($data['description']);
	
	if(empty($name))
	{
		return false;
	}
	
	if(capability_exists($name))
	{
		return get_capability_id($name);
	}
	
	return ItemModel::add_item( array('name'=>$name, 'description'=>$description, 'category'=>'available-user-capabilities', 'creator_id'=>UserModel::get_current_user_id()) );
}

function get_capabilities()
{
	return ItemModel::get_items( array('category'=>'available-user-capabilities') );
}

function get_capability_id($capability)
{
	$capability_id = ItemModel::get_items( array('category'=>'available-user-capabilities', 'name'=>$capability, 'data_to_get'=>array('id'))  );
	if(empty($capability_id))
	{
		return 0;
	}
	return is_array($capability_id) ? $capability_id[0] : $capability_id;
}

function capability_exists($capability)
{
	$capability_id = get_capability_id($capability);
	return !empty($capability_id) ? $capability_id : false;
}

function do_page_heading($heading)
{
	return '<h3 class="inner-page-title">'. $heading. '</h3>';
}

function create_default_user_capabilities()
{
	$capabilities = array
	(
		array('name'=>'Access Admin Portal',  'description'=>''),
		array('name'=>'View Site Settings',   'description'=>''),
		array('name'=>'Edit Site Settings',   'description'=>''),
		array('name'=>'Manage Site Settings', 'description'=>''),
		array('name'=>'View Users',           'description'=>''),
		array('name'=>'Edit Users',           'description'=>''),
		array('name'=>'Delete Users',         'description'=>''),
		array('name'=>'Manage Users',         'description'=>''),
		array('name'=>'View Capabilities',    'description'=>''),
		array('name'=>'Edit Capabilities',    'description'=>''),
		array('name'=>'Manage Capabilities',  'description'=>''),
		array('name'=>'View Roles',           'description'=>''),
		array('name'=>'Edit Roles',           'description'=>''),
		array('name'=>'Manage Roles',         'description'=>'')
	);
	
	foreach($capabilities AS $capability_data)
	{
		create_new_capability($capability_data);
	}
}

function create_default_user_roles()
{
	create_new_role( array('name'=>'Super Admin', 'description'=>'') );
}