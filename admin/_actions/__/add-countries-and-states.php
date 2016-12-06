<?php
include dirname(__DIR__). '/admin-config.php';
include ADMIN_INCLUDES_DIR. '/admin-functions.php';
include dirname(__FILE__). '/countries-states-array.php';
ini_set('max_execution_time', 3000);

/*
//delete already added countries and states
$countries = ItemModel::get_items( array('item_category'=>'countries') );
$states    = ItemModel::get_items( array('item_category'=>'country-states'));

foreach($countries AS $country)
{ 
	$country_id = $country['id'];
	ItemModel::delete_item( $country_id, array('remove_records'=>true) );
}

foreach($states AS $state)
{
	$state_id = $state['id'];
	ItemModel::delete_item( $state_id, array('remove_records'=>true) );
}

echo 'deleted';
exit;
*/

foreach($countries_and_states AS $country => $states)
{  
	if( country_exists($country) )
	{
		$country_data = ItemModel::get_items( array('item_category'=>'countries', 'name'=>$country, 'data_to_get'=>array('item_id') ), array(), 1 );
		$country_id   = $country_data[0]['item_id'];
	}
	else
	{
		$country_id = ItemModel::add_item( array('name'=>$country, 'parent_id'=>0, 'item_category'=>'countries', 'contributor_id'=>0) );
	}
	
	if( !empty($country_id) )
	{
		if(is_array($states) && !empty($states))
		{
			foreach($states AS $state)
			{
				if( !state_exists_in_country($country_id, $state) )
				{
					ItemModel::add_item( array('name'=>$state, 'parent_id'=>$country_id, 'item_category'=>'country-states', 'contributor_id'=>0) );
				}
			}
		}
	}
}

function country_exists($id_or_name)
{ 
	if(is_integer($id_or_name))
	{
		$item_exists = ItemModel::get_item_data($item_id);
	}
	else
	{
		$item_exists = ItemModel::get_items( array('item_category'=>'countries', 'name'=>$id_or_name) );
	}
		
	if(empty($item_exists))
	{
		return false;
	}
		
	else 
	{
		return true;
	}
}

function state_exists_in_country($country_id, $state_id_or_name)
{ 
	$data = ItemModel::get_items( array('item_category'=>'country-states', 'name'=>$state_id_or_name, 'parent_id'=>$country_id) );
	return ( empty($data) ? false : true );
}