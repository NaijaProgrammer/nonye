<?php //for cases where a user is not logged in, or where we include admin verification on front-end, don't display the admin header bar ?>
<?php if( !UserModel::user_is_logged_in() || ( !is_admin() && !is_super_admin() ) ): return; endif; ?>
<nav role="navigation" class="navbar navbar-default">
	
 <div class="navbar-header">
  <button type="button" data-target="#navbarCollapse" data-toggle="collapse" class="navbar-toggle">
   <span class="sr-only">Toggle navigation</span>
   <span class="icon-bar"></span>
   <span class="icon-bar"></span>
   <span class="icon-bar"></span>
  </button>
  <a href="<?php echo SITE_URL; ?>" class="navbar-brand" id="site-link"><span class="glyphicon glyphicon-home"></span>&nbsp;<?php echo get_site_name(); ?></a>
 </div>
 
 <div id="navbarCollapse" class="collapse navbar-collapse">
  <ul class="nav navbar-nav">
   <li><a href="<?php echo ADMIN_URL; ?>"><span class="glyphicon glyphicon-th"></span>&nbsp;Admin Home</a></li>
 
   <?php if( is_super_admin() ): ?>
   <li class="dropdown">
    <a href="#" data-toggle="dropdown" class="dropdown-toggle"><span class="glyphicon glyphicon-wrench"></span>&nbsp;Tools<b class="caret"></b></a>
    <ul class="dropdown-menu">
	 <li><a href="<?php echo ADMIN_URL; ?>/tools/db-backup.php">Backup Database</a></li>
	 <li><a href="<?php echo ADMIN_URL; ?>/tools/export-users.php">Export Users</a></li>
    </ul>
   </li>
   
   <li class="dropdown">
    <a href="#" data-toggle="dropdown" class="dropdown-toggle"><span class="glyphicon glyphicon-"></span>&nbsp;Site Administration<b class="caret"></b></a>
    <ul class="dropdown-menu">
	 <?php if( user_can('Manage Site Settings') ): ?><li><a href="<?php echo ADMIN_URL; ?>/?dir=settings">Settings Management</a></li><?php endif; ?>
	 <div class="spacer"></div>
	 
	 <li><a href="<?php echo ADMIN_URL; ?>/?dir=categories">Categories Management</a></li>
	 <li><a href="<?php echo ADMIN_URL; ?>/?dir=capabilities">Capabilities Management</a></li>
	 <li><a href="<?php echo ADMIN_URL; ?>/?dir=forums">Forums Management</a></li>
	 <li><a href="<?php echo ADMIN_URL; ?>/?dir=posts">Posts Management</a></li>
	 <li><a href="<?php echo ADMIN_URL; ?>/?dir=roles">Roles Management</a></li>
	 <li><a href="<?php echo ADMIN_URL; ?>/?dir=tags">Tags Management</a></li>
	 
	 <div class="spacer"></div>
	 <li><a href="<?php echo ADMIN_URL; ?>/?dir=make">Manage Cache Makers</a></li>
	 
    </ul>
   </li>
   <?php endif; ?>
 
   <li class="dropdown">
    <a href="#" data-toggle="dropdown" class="dropdown-toggle"><span class="glyphicon glyphicon-user"></span>&nbsp;User Administration<b class="caret"></b></a>
    <ul class="dropdown-menu">
	 <li><a href="<?php echo ADMIN_URL; ?>/?dir=users">View All Users</a></li>
     <li><a href="<?php echo ADMIN_URL; ?>/?dir=users&role=<?php echo urlencode('user'); ?>">View Users</a></li>
     <li><a href="<?php echo ADMIN_URL; ?>/?dir=users&role=<?php echo urlencode('admin'); ?>">View Admins</a></li>
	 <li><a href="<?php echo ADMIN_URL; ?>/?dir=users&role=<?php echo urlencode('super admin'); ?>">View Super Admins</a></li>
	 <li><a href="<?php echo ADMIN_URL; ?>/?dir=users&page=add-user">Add New User</a></li>
    </ul>
   </li>
   
   <li><a href="<?php echo ADMIN_URL; ?>/?dir=messages">Messages</a></li>
   
  </ul>
  
  <ul class="nav navbar-nav navbar-right"><li><a href="<?php echo ADMIN_URL; ?>/logout.php">Sign out</a></li></ul>
 </div>
</nav>