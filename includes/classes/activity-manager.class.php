<?php
class ActivityManager
{
	
	/*
	* object_id
	* object_type ('post')
	* subject_id 
	* subject_action ('create', 'like', 'share')
	* description (e.g 'shared on twitter/facebook/gplus') [optional]
	*/
	public static function create_activity($data)
	{
		extract($data);
		
		return ItemModel::add_item(array(
			'category'       => 'user-activities',
			'object_id'      => $object_id,
			'object_type'    => $object_type,
			'subject_id'     => $subject_id,
			'subject_action' => $subject_action,
			'description'    => isset($description) ? $description : '',
			'time_created'   => time()
		));
	}
	
	public static function get_user_activities($user_id, $filters=array())
	{  
		$limit = isset($filters['limit']) ? $filters['limit'] : 0;
		return ItemModel::get_items( array('category'=>'user-activities', 'subject_id'=>$user_id, 'data_to_get'=>array('id')), array(), $limit );
	}
	
	public static function get_activity_data($activity_id, $data_key='')
	{
		return ItemModel::get_item_data($activity_id, $data_key);
	}
}
