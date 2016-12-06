<?php
/**
 * File name: users-cache.php
 * A static cache of forums, to be updated whenever a new user is created or whenever admin gives the command to from the admin page
 * This helps prevent too much calls to database for the users, and makes retrieving and processing users data faster
 *
 * Format:
 * $users_cache = array(
 *	  array(id, email, username, firstname, lastname, date_registered),
 *    array(id, email, username, firstname, lastname, date_registered),
 *    ...
 * )
 */

$users_cache = array(
   array('id'=>'1', 'email'=>'orji4y@hotmail.com', 'username'=>'user_385435', 'firstname'=>'N/A', 'lastname'=>'N/A', 'date_registered'=>'2016-07-30 12:22:09'),
   array('id'=>'3', 'email'=>'orji4y@yahoo.com', 'username'=>'user_482812', 'firstname'=>'Michael', 'lastname'=>'O', 'date_registered'=>'2016-08-13 12:30:32'),
   array('id'=>'5', 'email'=>'mikkyorji@gmail.com', 'username'=>'user_872163', 'firstname'=>'Michael', 'lastname'=>'O', 'date_registered'=>'2016-08-13 21:07:30'),
   array('id'=>'6', 'email'=>'Michael05907608@twitter.com', 'username'=>'user_643644', 'firstname'=>'Michael', 'lastname'=>'O', 'date_registered'=>'2016-08-14 10:24:59'),
);