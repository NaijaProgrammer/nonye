<?php
/**
 * File name: forums-cache.php
 * A static cache of forums, to be updated whenever a new forum is created or whenever admin gives the command to from the admin page
 * This helps prevent too much calls to database for the forums, and makes retrieving and processing forums data faster
 *
 * Format:
 * $forums_cache = array(
 *	  array(id, creator_id, name, description, posts_count, date_added),
 *    array(id, creator_id, name, description, posts_count, date_added),
 *    ...
 * )
 */

$forums_cache = array(
   array('id'=>'1', 'creator_id'=>'1', 'name'=>'Agriculture', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:06:17'),
   array('id'=>'2', 'creator_id'=>'1', 'name'=>'Arts', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:06:17'),
   array('id'=>'3', 'creator_id'=>'1', 'name'=>'Business', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:06:17'),
   array('id'=>'5', 'creator_id'=>'1', 'name'=>'Culture', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:06:17'),
   array('id'=>'6', 'creator_id'=>'1', 'name'=>'Education', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:06:17'),
   array('id'=>'7', 'creator_id'=>'1', 'name'=>'Events and Entertainment', 'description'=>'', 'posts_count'=>'7', 'date_added'=>'2016-07-31 13:06:18'),
   array('id'=>'8', 'creator_id'=>'1', 'name'=>'Family', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:06:18'),
   array('id'=>'9', 'creator_id'=>'1', 'name'=>'Fashion', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:06:18'),
   array('id'=>'10', 'creator_id'=>'1', 'name'=>'Games', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:06:18'),
   array('id'=>'11', 'creator_id'=>'1', 'name'=>'Health', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:06:18'),
   array('id'=>'4', 'creator_id'=>'1', 'name'=>'Jobs and Careers', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:06:17'),
   array('id'=>'12', 'creator_id'=>'1', 'name'=>'Politics', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:06:18'),
   array('id'=>'13', 'creator_id'=>'1', 'name'=>'Programming', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:06:18'),
   array('id'=>'14', 'creator_id'=>'1', 'name'=>'Real Estate', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:06:18'),
   array('id'=>'15', 'creator_id'=>'1', 'name'=>'Relationship', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:06:18'),
   array('id'=>'16', 'creator_id'=>'1', 'name'=>'Religion', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:06:18'),
   array('id'=>'17', 'creator_id'=>'1', 'name'=>'Science', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:06:18'),
   array('id'=>'18', 'creator_id'=>'1', 'name'=>'Sports', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:06:18'),
   array('id'=>'19', 'creator_id'=>'1', 'name'=>'Technology', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:06:18'),
   array('id'=>'20', 'creator_id'=>'1', 'name'=>'Vacation', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:06:18'),
);