<?php $user_is_logged_in = UserModel::user_is_logged_in(); ?>
<!--<nav class="navbar navbar-inverse navbar-static-top" role="navigation">-->
<nav id="main-navigation" class="navbar navbar-inverse navbar-fixed-top main-navigation-bottom-border" role="navigation">
 <div class="container">
  <div class="navbar-header">
   <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbarCollapse">
    <span class="sr-only">Toggle navigation</span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
   </button>
   <a class="navbar-brand" href="<?php //echo sanitize_html_attribute(get_site_url()); ?>"><?php //echo get_site_name(); ?></a>
  </div>         
  <div class="collapse navbar-collapse" id="navbarCollapse">
   <?php /*
   <ul class="nav navbar-nav">
    <li><a href="#">About</a></li>
	<li><a href="#">Blog</a></li>
	<li><a href="#">Contact</a></li>
    <li><a href="#">FAQ</a></li>
   </ul>
   */?>
   <ul class="nav navbar-nav">
    <?php if($user_is_logged_in): ?>
	<li class="dropdown cursor-pointer authenticated-user-menu">
	 <a class="dropdown-toggle glyphicon glyphicon-user" data-toggle="dropdown" aria-expanded="false"></a>
	 <ul class="dropdown-menu" role="menu">
	  <li title="My account"><a href="<?php echo get_user_profile_url(); ?>"><span class="fa fa-icon fa-th-list"></span>&nbsp;My account</a></li>
	  <li title="Registered users"><a href="<?php echo get_site_url(); ?>/users"><span class="fa fa-icon fa-users"></span>&nbsp; Users</a></li>
	  <li title="Sign out"><a href="<?php echo get_site_url(); ?>/logout"><span class="fa fa-icon fa-sign-out"></span>&nbsp; Sign out</a></li>
	 </ul>
	</li>
	<?php else: ?>
	<li class="user-auth-btn" title="Login or Signup"><a class="glyphicon glyphicon-user cursor-pointer"></a></li>
	<?php endif; ?>
   </ul>
   <ul class="nav navbar-nav full-effect">
	<li class="hidden-xs hidden-sm">
	 <div id="user-notification-dropdown-container" class="dropdown dropdown-lg">
	  <span class="notification-counter"></span>
	  <span id="notifications-toggler" class="glyphicon glyphicon-bell cursor-pointer"  data-toggle="dropdown" aria-expanded="false"></span>
	  <div class="dropdown-menu dropdown-menu-left" role="menu">
	   <div>
	   <ul id="notifications"><ul>
	   </div>
	  </div>
	 </div>
	</li>
   </ul>
   <ul class="nav navbar-nav pull-right">
	<li class="hidden-xs hidden-sm">
     <div class="input-group" id="adv-search">
      <input type="text" class="form-control" placeholder="Find posts" />
      <div class="input-group-btn">
       <div class="btn-group" role="group">
        <div class="dropdown dropdown-lg">
         <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="caret"></span></button>
         <div class="dropdown-menu dropdown-menu-right" role="menu">
          <form class="form-horizontal" role="form">
           <div class="form-group">
            <label for="filter">Filter by</label>
            <select class="form-control">
             <option value="0" selected>All Posts</option>
             <option value="1">Featured</option>
             <option value="2">Most popular</option>
             <option value="3">Top rated</option>
             <option value="4">Most commented</option>
            </select>
           </div>
           <div class="form-group">
            <label for="contain">Author</label>
            <input class="form-control" type="text" />
           </div>
           <div class="form-group">
            <label for="contain">Contains the words</label>
            <input class="form-control" type="text" />
           </div>
           <button type="submit" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
          </form>
         </div>
        </div>
        <button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
       </div>
      </div>
     </div>
	</li>
	<li>
	 <button class="<?php echo $user_is_logged_in ? 'post-editor-opener' : 'user-auth-btn'; ?> cursor-pointer btn btn-primary btn-small widget-button" data-parent-id="0" title="Start a new topic">
	 + New Topic
	 </button>
	</li>
   </ul>
  </div>
 </div>
</nav>
<div id="notice-bar">
 <span id="notice-bar-content-area"></span>
 <span id="notice-bar-dismisser" class="float-right cursor-pointer ml10 mr10" onclick="slideUp('notice-bar');" title="Dismiss">&times;</span>
</div>
<script>
function notify(msg,opts)
{
	opts     = opts || {};
	duration = opts.duration || 5;
	$Html('notice-bar-content-area', msg);
	
	if( $Style('notice-bar').display != 'block' )
	{
		slideDown('notice-bar', {
			'onSlideDownEnd' : function(){
				setTimeout( function(){slideUp('notice-bar')}, duration * 1000 );
			}
		});
	}
}
</script>