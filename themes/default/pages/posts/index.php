<?php 
if( isset($_GET['v'])&& ($_GET['v'] == 'list') ): 
	include(dirname(__FILE__). '/posts-list.php'); 
else :
	include(dirname(__FILE__). '/posts-grid.php'); 
endif;