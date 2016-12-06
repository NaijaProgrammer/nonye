<?php
class AppSettings
{
	public static function setting_exists($setting_key)
	{
		$db_object = self::_get_db_object();
		$db_object->execute_query("SELECT * FROM ". self::get_tables_prefix(). "app_settings WHERE `setting_key` = '$setting_key'");
		return $db_object->num_rows() > 0;
	}
	
	public static function get_setting($setting_key)
	{
		if(!self::setting_exists($setting_key))
		{
			return '';
		}
		
		$setting_key = DataSanitizer::sanitize_data_for_db_query($setting_key);
		$db_obj      = self::_get_db_object();
		$db_obj->execute_query( "SELECT setting_value FROM ". self::get_tables_prefix(). "app_settings WHERE `setting_key` = '$setting_key' " );
		$row = $db_obj->get_rows();
		$setting_value = isset($row['setting_value']) ? $row['setting_value'] : '';
		return $setting_value;
	}
	
	public static function get_settings()
	{
		$db_obj = self::_get_db_object();
		$db_obj->execute_query( "SELECT * FROM ". self::get_tables_prefix(). "app_settings" );
		
		$matrix = $db_obj->return_result_as_matrix();
		$return_matrix = array();
		
		foreach($matrix AS $arr)
		{
			//$arr['setting_value'] = Util::Unstringify($arr['setting_value']);
			$setting_value = $arr['setting_value'];
			$return_matrix[$arr['setting_key']] = $setting_value;
		}
		
		return $return_matrix;
	}
	
	
	public static function update_settings($opts = array())
	{
		foreach($opts AS $key=>$value)
		{
			self::_update_setting($key, $value);
		}		
	}
	
	protected static function _update_setting($setting_key, $setting_value)
	{
		$setting_key   = trim($setting_key);
		$setting_value = trim($setting_value);
		
		if(empty($setting_key) || empty($setting_value))
		{
			return false;
		}
		
		if(self::setting_exists($setting_key))
		{
			self::_get_db_object()->update_records( self::get_tables_prefix(). 'app_settings', array('setting_value'=>$setting_value), array('setting_key'=>$setting_key) );
		}
		else
		{
			self::_get_db_object()->insert_records( self::get_tables_prefix(). 'app_settings', array('setting_key'=>$setting_key, 'setting_value'=>$setting_value) );
		}
		
	}
	
	private static function get_tables_prefix()
	{
		return TABLES_PREFIX;
	}
	
	private static function _get_db_object()
	{
   		return db::get_instance(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
	}
}