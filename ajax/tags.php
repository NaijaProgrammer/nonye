<?php
include('request-validator.php');
if(isset($_GET['tag-names']))
{ 
	$tags     = array();
	$all_tags = TagModel::get_tags(false);
	
	foreach($all_tags AS $tag)
	{  
		$tags[] = $tag['name'];
	}

	echo json_encode($tags, true);
	exit;
}