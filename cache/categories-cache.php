<?php
/**
 * File name: categories-cache.php
 * A static cache of categories, to be updated whenever a new category is created or whenever admin gives the command to from the admin page
 * This helps prevent too much calls to database for the categories, and makes retrieving and processing categories data faster
 *
 * Format:
 * $categories_cache = array(
 *	  array(id, creator_id, name, description, posts_count, date_added),
 *    array(id, creator_id, name, description, posts_count, date_added),
 *    ...
 * )
 */

$categories_cache = array(
   array('id'=>'1', 'creator_id'=>'1', 'name'=>'bollywood', 'description'=>'', 'posts_count'=>'7', 'date_added'=>'2016-07-31 13:07:16'),
   array('id'=>'2', 'creator_id'=>'1', 'name'=>'celebrities', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:07:16'),
   array('id'=>'3', 'creator_id'=>'1', 'name'=>'christianity', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:07:16'),
   array('id'=>'4', 'creator_id'=>'1', 'name'=>'computers', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:07:16'),
   array('id'=>'5', 'creator_id'=>'1', 'name'=>'ghollywood', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:07:16'),
   array('id'=>'6', 'creator_id'=>'1', 'name'=>'hollywood', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:07:16'),
   array('id'=>'7', 'creator_id'=>'1', 'name'=>'investment', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:07:16'),
   array('id'=>'8', 'creator_id'=>'1', 'name'=>'islam', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:07:16'),
   array('id'=>'9', 'creator_id'=>'1', 'name'=>'jokes', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:07:16'),
   array('id'=>'10', 'creator_id'=>'1', 'name'=>'literature', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:07:16'),
   array('id'=>'11', 'creator_id'=>'1', 'name'=>'money', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:07:16'),
   array('id'=>'12', 'creator_id'=>'1', 'name'=>'movies', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:07:16'),
   array('id'=>'13', 'creator_id'=>'1', 'name'=>'music', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:07:16'),
   array('id'=>'14', 'creator_id'=>'1', 'name'=>'nollywood', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:07:16'),
   array('id'=>'15', 'creator_id'=>'1', 'name'=>'phones', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:07:16'),
   array('id'=>'16', 'creator_id'=>'1', 'name'=>'radio', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:07:16'),
   array('id'=>'17', 'creator_id'=>'1', 'name'=>'romance', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:07:16'),
   array('id'=>'18', 'creator_id'=>'1', 'name'=>'stocks', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:07:16'),
   array('id'=>'19', 'creator_id'=>'1', 'name'=>'television', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:07:16'),
   array('id'=>'20', 'creator_id'=>'1', 'name'=>'vacancies', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:07:16'),
   array('id'=>'21', 'creator_id'=>'1', 'name'=>'video', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:07:16'),
   array('id'=>'22', 'creator_id'=>'1', 'name'=>'web design', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:07:16'),
);