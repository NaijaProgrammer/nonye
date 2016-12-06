<?php
require_once('installation-checker.php');
if(! isset($_REQUEST['get_children']))
{
	echo '';
	exit;
}
$parent_id = $_REQUEST['parent_id'];
//$options   = ItemManager::get_items_as_dropdown_menu_options('', $parent_id, $orders = array('name'=>'ASC') );
//$options   = ItemManager::get_items_as_dropdown_menu_options( array( 'conditions'=>array('parent_id'=>$parent_id), 'order_by'=>array('name'=>'ASC') ));
$options   = ItemManager::get_items_as_dropdown_menu_options( array( 'conditions'=>array('parent_id'=>$parent_id) ));
echo $options;
exit;