<?php
include('request-validator.php');
if(isset($_GET['forum']))
{  
	$forum           = ForumModel::get_forum_instance( $_GET['forum'] );
	$categories      = $forum->get_categories();
	$response_data   = array();
	
	if( !empty($categories) )
	{
		$response_data[] = array( 'id'=>0, 'name'=>'Select Category' );
	}
	
	foreach($categories AS $category_id)
	{
		$category = CategoryModel::get_category_instance($category_id);
		$response_data[] = array( 'id'=>$category->get('id'), 'name'=>$category->get('name') );
	}
	
	echo json_encode($response_data, true);
	exit;
}