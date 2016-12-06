<?php
function create_forum_user_capabilities()
{
	$capabilities = array
	(
		array('name'=>'Create Tags',  'description'=>''),
		array('name'=>'Edit Tags', 'description'=>''),
		array('name'=>'Delete Tags', 'description'=>''),
		array('name'=>'Manage Tags', 'description'=>'Grant user the ability to create, edit or delete tags all in one'),
		
		array('name'=>'Create Posts', 'description'=>''),
		array('name'=>'Edit Posts', 'description'=>''),
		array('name'=>'Delete Posts', 'description'=>''),
		array('name'=>'Manage Posts', 'description'=>'Grant user the ability to create, edit or delete posts all in one'),
		
		array('name'=>'Create Forums', 'description'=>''),
		array('name'=>'Edit Forums', 'description'=>''),
		array('name'=>'Delete Forums', 'description'=>''),
		array('name'=>'Manage Forums', 'description'=>'Grant user the ability to create, edit or delete forums all in one'),
		
		array('name'=>'Create Categories', 'description'=>''),
		array('name'=>'Edit Categories', 'description'=>''),
		array('name'=>'Delete Categories', 'description'=>''),
		array('name'=>'Manage Categories', 'description'=>'Grant user the ability to create, edit or delete categories all in one')
	);
	
	foreach($capabilities AS $capability_data)
	{
		create_new_capability($capability_data);
	}
}