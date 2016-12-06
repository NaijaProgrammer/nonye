<?php
/**
 * File name: tags-cache.php
 * A static cache of tags, to be updated whenever a new tag is created or whenever admin gives the command to from the admin page
 * This helps prevent too much calls to database for the tags, and makes retrieving and processing tags data faster
 *
 * Format:
 * $tags_cache = array(
 *	  array(id, creator_id, name, description, posts_count, date_added),
 *    array(id, creator_id, name, description, posts_count, date_added),
 *    ...
 * )
 */

$tags_cache = array(
   array('id'=>'11', 'creator_id'=>'1', 'name'=>'arsenal', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'3', 'creator_id'=>'1', 'name'=>'baseball', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'2', 'creator_id'=>'1', 'name'=>'basketball', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'9', 'creator_id'=>'1', 'name'=>'bundesliga', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'20', 'creator_id'=>'1', 'name'=>'css', 'description'=>'', 'posts_count'=>'1', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'6', 'creator_id'=>'1', 'name'=>'english-premier-league', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'13', 'creator_id'=>'1', 'name'=>'everton', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'24', 'creator_id'=>'1', 'name'=>'facebook', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'15', 'creator_id'=>'1', 'name'=>'fc-barcelona', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'1', 'creator_id'=>'1', 'name'=>'football', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'26', 'creator_id'=>'1', 'name'=>'google', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'27', 'creator_id'=>'1', 'name'=>'google-plus', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'19', 'creator_id'=>'1', 'name'=>'html', 'description'=>'', 'posts_count'=>'1', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'28', 'creator_id'=>'1', 'name'=>'instagram', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'17', 'creator_id'=>'1', 'name'=>'javascript', 'description'=>'', 'posts_count'=>'4', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'16', 'creator_id'=>'1', 'name'=>'jquery', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'7', 'creator_id'=>'1', 'name'=>'la-liga', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'8', 'creator_id'=>'1', 'name'=>'ligue-1', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'12', 'creator_id'=>'1', 'name'=>'man-u', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'23', 'creator_id'=>'1', 'name'=>'oembed', 'description'=>'', 'posts_count'=>'3', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'22', 'creator_id'=>'1', 'name'=>'open-graph', 'description'=>'', 'posts_count'=>'2', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'18', 'creator_id'=>'1', 'name'=>'php', 'description'=>'', 'posts_count'=>'3', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'31', 'creator_id'=>'1', 'name'=>'reddit', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:09:36'),
   array('id'=>'10', 'creator_id'=>'1', 'name'=>'serie-a', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'14', 'creator_id'=>'1', 'name'=>'stoke-city', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'5', 'creator_id'=>'1', 'name'=>'table-tennis', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'4', 'creator_id'=>'1', 'name'=>'tennis', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'30', 'creator_id'=>'1', 'name'=>'tumblr', 'description'=>'', 'posts_count'=>'0', 'date_added'=>'2016-07-31 13:09:36'),
   array('id'=>'25', 'creator_id'=>'1', 'name'=>'twitter', 'description'=>'', 'posts_count'=>'1', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'21', 'creator_id'=>'1', 'name'=>'web-design', 'description'=>'', 'posts_count'=>'2', 'date_added'=>'2016-07-31 13:09:35'),
   array('id'=>'29', 'creator_id'=>'1', 'name'=>'you-tube', 'description'=>'', 'posts_count'=>'1', 'date_added'=>'2016-07-31 13:09:35'),
);