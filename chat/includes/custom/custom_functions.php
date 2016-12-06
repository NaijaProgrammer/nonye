<?php
require_once dirname( dirname( dirname(__DIR__) ) ). '/config.php';
/* implement to return id of the current user */
function get_current_user_id()
{
	return UserModel::get_current_user_id();
}

function is_online($user_id)
{
	return user_is_online($user_id);
}

/*
* implement this function to get a user's (bio-)data
* 
* @params: user's id
* @return value: an array in the form
* arr_name['id'], arr_name['name']
*/
if(!function_exists('get_user_data')):
function get_user_data($user_id)
{
	$user      = UserModel::get_user_instance($user_id);
	$user_data = array(
		'id'           => $user_id,
		'name'         => $user->get('username'),
		'email'        => $user->get('email'),
		'sex'          => $user->get('sex', ''),
		'age'          => $user->get('age', ''),
		'country'      => $user->get('location', ''),
		'state'        => $user->get('location', ''),
		'photo'        => '',
		'login_status' => ( user_is_online($user_id) ? 1 : '0' ),
		'profile_path' => ''
	);

	return $user_data;
}
endif; 

/*
* implement this function to get current user's buddies
* 
* @params: current user's id, login status of buddies to retrieve(optional)
* @possible values for buddies' login status: 0(offline), 1(online), 2(session expiry: i.e idle), default(get all buddies)
* @return value: an array in the form
* arr_name[0]['id'], arr[0]['name'] ... arr_name[n]['id'], arr[n]['name']
*/

function get_friends($user_id)
{
	$buddies = array();
	$buddies[0]['id'] = 15;
	$buddies[0]['name'] = 'mikky';
	$buddies[0]['login_status'] = 0;
	
	/*
	 $f_buddy = get_user_data(1);
	 $s_buddy = get_user_data(2);
	 $t_buddy = get_user_data(3);
	 $ft_buddy = get_user_data(4);

	 $buddies = array();


	/*
	* function must return values in the format specified below
	* values may be different but array name and keys must not change
	*/
	/*
	$buddies[0]['id'] = $f_buddy['id'];
	$buddies[0]['name'] = $f_buddy['name'];
	$buddies[0]['login_status'] = 0; //$f_buddy['login_status'];

	$buddies[1]['id'] = $s_buddy['id'];
	$buddies[1]['name'] = $s_buddy['name'];
	$buddies[1]['login_status'] = 1; //$s_buddy['login_status'];

	$buddies[2]['id'] = $t_buddy['id'];
	$buddies[2]['name'] = $t_buddy['name'];
	$buddies[2]['login_status'] = 2; //$t_buddy['login_status'];

	$buddies[3]['id'] = $ft_buddy['id'];
	$buddies[3]['name'] = $ft_buddy['name'];
	$buddies[3]['login_status'] = 1; //$ft_buddy['login_status'];
	*/

	return $buddies;
}