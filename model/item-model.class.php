<?php

require SITE_DIR. '/a/item-manager/item-manager.php';

class ItemModel extends BaseModel
{
	/*
	* $query_type string ['insert' | 'update' | 'delete' | 'select']
	*/
	public static function execute_query($query_type, $query_str)
	{
		return ItemManager::execute_query($query_type, $query_str);
	}
	
	public static function get_items_count($conditions = array())
	{
		return ItemManager::get_items_count($conditions);
	}
	
	public static function get_items( $conditions = array(), $orders = array(), $limit = 0 )
	{
		return ItemManager::get_items( $conditions, $orders, $limit );
	}
	
	public static function get_items_by_category($category, $orders=array())
	{
		return ItemManager::get_items_by_category($category, $orders);
	}
	
	public static function get_item_ids_by_name($item_name)
	{
		return ItemManager::get_item_ids_by_name($item_name);
	}
	
	public static function get_item_data($item_id, $data_key='')
	{
		return ItemManager::get_item_data($item_id, $data_key);
	}
	
	/**
	* $opts Array in the form ('name'=>'windows7', 'category'=>'pc', ...)
	*/
	public static function add_item( $opts = array() )
	{
		return ItemManager::add_item($opts);
	}
	
	/**
	* $matrix e.g = array('data_key'='', 'data_value'='', 'overwrite'=true), array('data_key'=>'', 'data_value'=>'')
	*/
	public static function update_item($item_id, $matrix)
	{
		ItemManager::update_item($item_id, $matrix);
	}
	
	public static function delete_item($item_id, $opts=array() )
	{
		return ItemManager::delete_item($item_id, $opts);
	}
	
	/**
	* array( 'conditions'=>array('category'=>''), 'order_by'=>array('name'=>'ASC') )
	*/
	public static function get_items_as_dropdown_menu_options( $opts = array() )
	{
		return ItemManager::get_items_as_dropdown_menu_options($opts);
	}
	
	public static function load_dropdown_list( $opts = array() )
	{
		return ItemManager::load_dropdown_list($opts);
	}
	public static function load_dropdown_list_option( $opts = array() )
	{
		return ItemManager::load_dropdown_list_option($opts);
	}
}