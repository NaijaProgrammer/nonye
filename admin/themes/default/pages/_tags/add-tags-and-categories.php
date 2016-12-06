<?php
//include dirname(__DIR__). '/admin-config.php';
//include ADMIN_INCLUDES_DIR. '/admin-functions.php';

$cats = array(
	array('name'=>'agriculture',   'description'=>'Agriculture stuff'),
	array('name'=>'arts',          'description'=>'Agriculture stuff'),
	array('name'=>'autos',         'description'=>'Agriculture stuff'),
	array('name'=>'bollywood',     'description'=>'Agriculture stuff'),
	array('name'=>'business',      'description'=>'Agriculture stuff'),
	array('name'=>'career',        'description'=>'Agriculture stuff'),
	array('name'=>'celebrities',   'description'=>'Agriculture stuff'),
	array('name'=>'christianity',  'description'=>'Agriculture stuff'),
	array('name'=>'computers',     'description'=>'Computer stuff'),
	array('name'=>'crime',         'description'=>'Agriculture stuff'),
	array('name'=>'culture',       'description'=>'Agriculture stuff'),
	array('name'=>'diaries',       'description'=>'Agriculture stuff'),
	array('name'=>'education',     'description'=>'Education stuff'),
	array('name'=>'entertainment', 'description'=>'Agriculture stuff'),
	array('name'=>'events',        'description'=>'Agriculture stuff'),
	array('name'=>'family',        'description'=>'Agriculture stuff'),
	array('name'=>'fashion',       'description'=>'Agriculture stuff'),
	array('name'=>'food',          'description'=>'Agriculture stuff'),
	array('name'=>'games',         'description'=>'Agriculture stuff'),
	array('name'=>'gollywood',     'description'=>'Agriculture stuff'),
	array('name'=>'graphics',      'description'=>'Agriculture stuff'),
	array('name'=>'health',        'description'=>'Agriculture stuff'),
	array('name'=>'hollywood',     'description'=>'Agriculture stuff'),
	array('name'=>'investment',    'description'=>'Agriculture stuff'),
	array('name'=>'islam',         'description'=>'Agriculture stuff'),
	array('name'=>'jobs',          'description'=>'Agriculture stuff'),
	array('name'=>'jokes',         'description'=>'Agriculture stuff'),
	array('name'=>'literature',    'description'=>'Agriculture stuff'),
	array('name'=>'money',         'description'=>'Agriculture stuff'),
	array('name'=>'movies',        'description'=>'Agriculture stuff'),
	array('name'=>'music',         'description'=>'Agriculture stuff'),
	array('name'=>'nollywood',     'description'=>'Nollywood stuff'),
	array('name'=>'pets',          'description'=>'Agriculture stuff'),
	array('name'=>'phones',        'description'=>'Agriculture stuff'),
	array('name'=>'politics',      'description'=>'Political stuff'),
	array('name'=>'professionals', 'description'=>'Agriculture stuff'),
	array('name'=>'programming',   'description'=>'Agriculture stuff'),
	array('name'=>'properties',    'description'=>'Agriculture stuff'),
	array('name'=>'radio',         'description'=>'Agriculture stuff'),
	array('name'=>'religion',      'description'=>'Agriculture stuff'),
	array('name'=>'romance',       'description'=>'Agriculture stuff'),
	array('name'=>'science',       'description'=>'Agriculture stuff'),
	array('name'=>'sports',        'description'=>'Sport stuff'),
	array('name'=>'stocks',        'description'=>'Agriculture stuff'),
	array('name'=>'technology',    'description'=>'Agriculture stuff'),
	array('name'=>'television',    'description'=>'Television stuff'),
	array('name'=>'travel',        'description'=>'Agriculture stuff'),
	array('name'=>'vacancies',     'description'=>'Agriculture stuff'),
	array('name'=>'video',         'description'=>'Agriculture stuff'),
	array('name'=>'web design',    'description'=>'Agriculture stuff'),
);

/*
foreach($cats AS $cat_data)
{
	CategoryModel::create_category(array(
		'parent_id'     => 0,
		'name'          => $cat_data['name'],
		'description'   => $cat_data['description'],
		'creator_id'    => $cat_data['creator_id']
	));
	
	$category_id = CategoryModel::get_category_id($cat_data['name']);
	CategoryModel::update_category_data($category_id, 'description', $cat_data['description']);
	
	//echo CategoryModel::category_exists($cat). '<br>';
	//echo CategoryModel::get_category_id($cat). '<br>';
}
*/
foreach($cats AS $cat_data)
{
	TagModel::create_tag(array(
		'parent_id'     => 0,
		'name'          => get_slug(strtolower($cat_data['name'])),
		'description'   => $cat_data['description'],
		'creator_id'    => 1
	));
	
	//$tag_id = TagModel::get_tag_id($cat_data['name']);
	//TagModel::update_tag_data($tag_id, 'description', $cat_data['description']);
}

