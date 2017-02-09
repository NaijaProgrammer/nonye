<?php //the index controller loads and executes the posts controller
$limit = 20;
$order_data = array('date_created'=>'DESC');
$image_url  = SITE_URL. '/logo-large.png';
$posts = PostModel::get_posts( true, array('parent_id'=>0), $order_data, $limit ); //get only top-level posts
include dirname(__DIR__). '/posts/index.php';