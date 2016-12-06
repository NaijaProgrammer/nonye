<?php
/*
class NotificationManager
{
	public function notify($user_id, $notification_id)
	{
		self::create($data);
	}
	
	/*
	* user_id,
	* object_id,
	* /
	private static function create($data)
	{
		extract($data);
		return ItemModel::add_item(array(
			'category'  => 'user-notifications',
			'user_id'   => $user_id,
			'object_id' => $object_id,
			'status'    => 'not-seen'
		));
	}
	
	private static function set_as_seen($notification_id)
	{
		ItemModel::update_item($notification_id, array(array('data_key'=>'status', 'data_value'=>'seen')));
	}
	
	/*
	* object_id,
	* 
	* /
	public static function get($user_id, $filters=array())
	{
		$query_data = array('category'=>'user-notifications', 'user_id'=>$user_id);
		
		if( isset($filters['object_id']) )
		{
			$query_data['object_id'] = $filters['object_id'];
		}
		if(isset($filters['seen']))
		{
			$query_data['status'] = 'seen';
		}
		if(isset($filters['not-seen']))
		{
			$query_data['status'] = 'not-seen';
		}
		
		$limit = ( isset($filters['limit']) ? $filters['limit'] : 0 );
		
		ItemModel::get_items($query_data, array(), $limit);
	}
}
*/