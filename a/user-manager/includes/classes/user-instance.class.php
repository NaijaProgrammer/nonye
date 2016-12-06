<?php
/*
* A convenience class to get and set a given user's (identified by $user_id) data
* Useful for quick read-and-write of data belonging to  supplied user
*/
class UserInstance
{
	private $user_id;
	private $user_data;
	
	public function __construct($user_id)
	{
		$this->user_id   = $user_id;
		$this->user_data = UserManager::get_user_data($user_id);
	}
	
	public function get($key = '', $default_value = '')
	{
		return ( empty($key) ? $this->user_data : $this->get_array_member_value($this->user_data, $key, $default_value) );
	}
	
	public function update($key, $new_value, $overwrite = true)
	{
		if( ($key != 'id') && ($key != 'user_id') )
		{
			$update_data[] = array('data_key'=>$key, 'data_value'=>$new_value, 'overwrite'=>$overwrite);
			return UserManager::update_user_data($this->user_id, $update_data);
		}
	}
	
	public function __get($key)
	{
		return $this->get($key, '');
	}
	
	public function __set($key, $value)
	{
		return $this->update($key, $value);
	}
	
	private function get_array_member_value( $array_name, $member_index, $optional_return_value='' )
	{
		return ( isset($array_name[$member_index]) ? $array_name[$member_index] : $optional_return_value );
	}
}