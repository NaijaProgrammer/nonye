<?php
function create_forum_tables()
{ 
	$db_obj        = get_db_instance();
	$tables        = array('forums', 'categories', 'tags', 'posts', 'comments', 'forum_categories', 'forum_posts', 'category_posts', 'tag_posts', 'post_views');
	$tables_len    = count($tables);
	$tables_prefix = TABLES_PREFIX;
	
	for($i=0; $i < $tables_len; $i++)
	{
		if($tables[$i] == "forums")
		{
			$db_obj->execute_query("CREATE TABLE IF NOT EXISTS {$tables_prefix}forums(
				`id`          int NOT NULL auto_increment PRIMARY KEY,
				`creator_id`  int NOT NULL,
				`name`        varchar(100),
				`description` text,
				`date_added` datetime NOT NULL
			) DEFAULT CHARACTER SET utf8 AUTO_INCREMENT=11;" );
		}
		
		else if($tables[$i] == "categories")
		{
			$db_obj->execute_query("CREATE TABLE IF NOT EXISTS {$tables_prefix}categories(
				`id`          int NOT NULL auto_increment PRIMARY KEY,
				`name`        varchar(100),
				`description` text,
				`creator_id`  int NOT NULL,
				`date_added`  datetime NOT NULL
			) DEFAULT CHARACTER SET utf8 AUTO_INCREMENT=101;" ); //end sql command
		}
		
		else if($tables[$i] == "tags")
		{
			$db_obj->execute_query("CREATE TABLE IF NOT EXISTS {$tables_prefix}tags (
				`id`          int NOT NULL auto_increment PRIMARY KEY,
				`name`        varchar(100),
				`description` text,
				`creator_id`  int NOT NULL,
				`date_added`  datetime NOT NULL
			) DEFAULT CHARACTER SET utf8 AUTO_INCREMENT=2345;" ); 
		}
		
		else if($tables[$i] == "posts")
		{
			$db_obj->execute_query("CREATE TABLE IF NOT EXISTS {$tables_prefix}posts (
				`id`          int NOT NULL auto_increment PRIMARY KEY,
				`parent_id`   int NOT NULL default 0,
				`author_id`   int NOT NULL,
				`title`       text,
				`content`     longtext,
				`date_added`  datetime NOT NULL
			) DEFAULT CHARACTER SET utf8 AUTO_INCREMENT=3456;" ); //end sql command
		}
	  
		else if($tables[$i] == "comments")
		{
			$db_obj->execute_query("CREATE TABLE IF NOT EXISTS {$tables_prefix}comments (
				`id`         int NOT NULL auto_increment PRIMARY KEY,
				`post_id`    int NOT NULL,
				`parent_id`  int NOT NULL default 0,
				`author_id`  int NOT NULL,
				`content`    longtext,
				`date_added` datetime NOT NULL
			) DEFAULT CHARACTER SET utf8 AUTO_INCREMENT=1001;" );
		}
		
		else if($tables[$i] == "forum_categories")
		{
			$db_obj->execute_query("CREATE TABLE IF NOT EXISTS {$tables_prefix}forum_categories (
				`forum_id`    int NOT NULL,
				`category_id` int NOT NULL,
				PRIMARY KEY(`forum_id`, `category_id`)
			) DEFAULT CHARACTER SET utf8" );
		}
	  
		else if($tables[$i] == "forum_posts")
		{
			$db_obj->execute_query("CREATE TABLE IF NOT EXISTS {$tables_prefix}forum_posts (
				`forum_id` int NOT NULL,
				`post_id`  int NOT NULL,
				PRIMARY KEY(`forum_id`, `post_id`)
			) DEFAULT CHARACTER SET utf8" );
		}

		else if($tables[$i] == "category_posts")
		{
			$db_obj->execute_query("CREATE TABLE IF NOT EXISTS {$tables_prefix}category_posts (
				`category_id` int NOT NULL,
				`post_id`     int NOT NULL,
				PRIMARY KEY(`category_id`, `post_id`)
			) DEFAULT CHARACTER SET utf8" );
		}

		else if($tables[$i] == "tag_posts")
		{
			$db_obj->execute_query("CREATE TABLE IF NOT EXISTS {$tables_prefix}tag_posts (
				`tag_id`  int NOT NULL,
				`post_id` int NOT NULL,
				PRIMARY KEY(`tag_id`, `post_id`) 
			) DEFAULT CHARACTER SET utf8" );
		}
	  
		else if($tables[$i] == "post_views")
		{
			$db_obj->execute_query("CREATE TABLE IF NOT EXISTS {$tables_prefix}post_views (
				`post_id`     int NOT NULL,
				`viewer_id`   int NOT NULL,
				`date_viewed` datetime NOT NULL
			) DEFAULT CHARACTER SET utf8" ); //end sql command
		}
	}
	
	$db_obj->execute_query("CREATE TABLE IF NOT EXISTS `tiny_url_master` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`long_url` varchar(256) NOT NULL,
		`tiny_url` varchar(200) NOT NULL,
		`created_date` datetime NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;" );
	
	$db_obj->execute_query("ALTER TABLE {$tables_prefix}forums AUTO_INCREMENT=11");
	$db_obj->execute_query("ALTER TABLE {$tables_prefix}categories AUTO_INCREMENT=101");
	$db_obj->execute_query("ALTER TABLE {$tables_prefix}tags AUTO_INCREMENT=2345");
	$db_obj->execute_query("ALTER TABLE {$tables_prefix}posts AUTO_INCREMENT=3456");
	$db_obj->execute_query("ALTER TABLE {$tables_prefix}comments AUTO_INCREMENT=1001");
}