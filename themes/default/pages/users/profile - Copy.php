<?php
$page_instance->add_header(array(
	'page_title'       => $page_title,
	'page_keywords'    => $page_keywords,
	'page_description' => $page_description,
	'robots_value'     => $robots_value,
	'open_graph_data'  => $open_graph_data,
	'current_user'     => $current_user //coming from the app-controller class
));

$page_instance->add_stylesheets(array());
$page_instance->add_nav();
?>
<?php $current_user_id = is_object($current_user) ? $current_user->get('id') : 0; ?>
<div class="container main-container">
 <div class="row">
  <div class="col-sm-3 text-center">
   <a href="<?php get_user_profile_url($owner_id); ?>">
    <img title="<?php echo sanitize_html_attribute($owner->get('username')); ?> image" class="mg-responsive user-image" src="<?php echo sanitize_html_attribute($owner->get('image-url', get_app_setting('default-user-image-url'))); ?>">
	<h1><?php echo $owner->get('username'); ?></h1>
   </a>
  </div>  	
 </div>
 <div class="row">
  <div class="col-sm-3">
   <ul class="list-group">
    <li class="list-group-item text-muted">Profile <small><i class="fa fa-user fa-1x"></i></small></li>
    <li class="list-group-item text-right"><span class="pull-left"><strong>Joined</strong></span>&nbsp;<?php echo $owner->get('date_registered'); ?></li>
    <li class="list-group-item text-right"><span class="pull-left"><strong>Last seen</strong></span> Yesterday</li>
    <li class="list-group-item text-right"><span class="pull-left"><strong>Real name</strong></span> Joseph Doe</li>       
   </ul> 
   <ul class="list-group">
    <li class="list-group-item text-muted">Activity <small><i class="fa fa-dashboard fa-1x"></i></small></li>
    <li class="list-group-item text-right"><span class="pull-left"><strong>Shares</strong></span> 125</li>
    <!--<li class="list-group-item text-right"><span class="pull-left"><strong>Likes</strong></span> 13</li>-->
    <li class="list-group-item text-right"><span class="pull-left"><strong>Posts</strong></span> 37</li>
    <!--<li class="list-group-item text-right"><span class="pull-left"><strong>Followers</strong></span> 78</li>-->
   </ul> 
   <div class="panel panel-default">
    <div class="panel-heading">Social Media</div>
    <div class="panel-body">
	 Website <i class="fa fa-link fa-1x"></i>
	 <a href="http://bootply.com">bootply.com</a><br>
     <i class="fa fa-facebook fa-2x"></i> 
	 <i class="fa fa-github fa-2x"></i> 
	 <i class="fa fa-twitter fa-2x"></i> 
	 <i class="fa fa-pinterest fa-2x"></i> 
	 <i class="fa fa-google-plus fa-2x"></i>
    </div>
   </div>     
  </div>
  <div class="col-sm-9">
   <ul class="nav nav-tabs" id="myTab">
    <li class="active"><a href="#home" data-toggle="tab">Recent Activity</a></li>
	<?php if( $owner->get('id') == $current_user_id ): ?>
	 <li><a href="#settings" data-toggle="tab">Settings</a></li>
	<?php endif; ?>
   </ul>
   <div class="tab-content">
	<div class="tab-pane active" id="home">
     <h4></h4>
     <div id="user-recent-activity" class="table-responsive">
	  <?php $owner_activities = get_user_activities($owner_id); ?>
      <table class="table table-hover">
       <tbody id="items">
	    <?php foreach($owner_activities AS $activity_id): ?>
		<tr><td><?php echo  format_activity($activity_id, $current_user_id); ?></td></tr>
        <!--<tr><td><i class="pull-right fa fa-edit"></i> Today, 12:20 - You posted a new blog entry title "Why social media is".</td></tr>
        <tr><td><i class="pull-right fa fa-edit"></i> Yesterday - Karen P. liked your post.</td></tr>
        <tr><td><i class="pull-right fa fa-edit"></i> 2 Days Ago - Philip W. liked your post.</td></tr>-->
		<?php endforeach; ?>
       </tbody>
      </table>
	  <hr>
      <div class="row">
       <div class="col-md-4 col-md-offset-4 text-center"><ul class="pagination" id="myPager"></ul></div>
      </div>
     </div>      
    </div>
	
	<?php if( $owner->get('id') == $current_user_id ): ?>
    <div class="tab-pane" id="settings">
     <hr>
     <form class="form">
      <div class="form-group">
       <div class="col-xs-6">
        <label for="first_name"><h4>First name</h4></label>
        <input id="first-name-field" class="form-control" type="text"  placeholder="first name" title="enter your first name if any.">
       </div>
      </div>
      <div class="form-group">
       <div class="col-xs-6">
        <label for="last_name"><h4>Last name</h4></label>
        <input id="last-name-field" class="form-control" type="text" placeholder="last name" title="enter your last name if any.">
       </div>
      </div>
	  <div class="form-group">
       <div class="col-xs-6">
        <label for="email"><h4>Email</h4></label>
        <input id="email-field" class="form-control" type="email" placeholder="you@email.com" title="enter your email.">
       </div>
      </div>
	  <div class="form-group">
       <div class="col-xs-6">
        <label for="mobile"><h4>Username</h4></label>
        <input id="username-field" class="form-control" type="text" placeholder="enter your username" title="enter your username">
       </div>
      </div>
      <div class="form-group">
       <div class="col-xs-6">
        <label for="mobile number"><h4>Mobile</h4></label>
        <input id="mobile-number" class="form-control" type="text" placeholder="enter your mobile number" title="enter your mobile number">
       </div>
      </div>
      
      <div class="form-group">
       <div class="col-xs-6">
        <label for="email"><h4>Location</h4></label>
        <input type="email" class="form-control" id="location" placeholder="somewhere" title="enter a location">
       </div>
      </div>
      <div class="form-group">
       <div class="col-xs-6">
        <label for="password"><h4>Password</h4></label>
        <input type="password" class="form-control" name="password" id="password" placeholder="password" title="enter your password.">
       </div>
      </div>
      <div class="form-group">
       <div class="col-xs-6">
        <label for="password2"><h4>Verify</h4></label>
        <input type="password" class="form-control" name="password2" id="password2" placeholder="password2" title="enter your password2.">
       </div>
      </div>
      <div class="form-group">
       <div class="col-xs-12"><br>
		<span class="pull-right">
         <button class="btn btn-lg btn-success" type="submit"><i class="glyphicon glyphicon-ok-sign"></i> Save</button>
         <!--<button class="btn btn-lg" type="reset"><i class="glyphicon glyphicon-repeat"></i> Reset</button>-->
		</span>
       </div>
      </div>
     </form>
    </div> 
	<?php endif; ?>
	
   </div>
  </div>
 </div>
</div>
<script>
$.fn.pageMe = function(opts){
    var $this = this,
        defaults = {
            perPage: 7,
            showPrevNext: false,
            numbersPerPage: 1,
            hidePageNumbers: false
        },
        settings = $.extend(defaults, opts);
    
    var listElement = $this;
    var perPage = settings.perPage; 
    var children = listElement.children();
    var pager = $('.pagination');
    
    if (typeof settings.childSelector!="undefined") {
        children = listElement.find(settings.childSelector);
    }
    
    if (typeof settings.pagerSelector!="undefined") {
        pager = $(settings.pagerSelector);
    }
    
    var numItems = children.size();
    var numPages = Math.ceil(numItems/perPage);

    pager.data("curr",0);
    
    if (settings.showPrevNext){
        $('<li><a href="#" class="prev_link">«</a></li>').appendTo(pager);
    }
    
    var curr = 0;
    while(numPages > curr && (settings.hidePageNumbers==false)){
        $('<li><a href="#" class="page_link">'+(curr+1)+'</a></li>').appendTo(pager);
        curr++;
    }
  
    if (settings.numbersPerPage>1) {
       $('.page_link').hide();
       $('.page_link').slice(pager.data("curr"), settings.numbersPerPage).show();
    }
    
    if (settings.showPrevNext){
        $('<li><a href="#" class="next_link">»</a></li>').appendTo(pager);
    }
    
    pager.find('.page_link:first').addClass('active');
    if (numPages<=1) {
        pager.find('.next_link').hide();
    }
  	pager.children().eq(1).addClass("active");
    
    children.hide();
    children.slice(0, perPage).show();
    
    pager.find('li .page_link').click(function(){
        var clickedPage = $(this).html().valueOf()-1;
        goTo(clickedPage,perPage);
        return false;
    });
    pager.find('li .prev_link').click(function(){
        previous();
        return false;
    });
    pager.find('li .next_link').click(function(){
        next();
        return false;
    });
    
    function previous(){
        var goToPage = parseInt(pager.data("curr")) - 1;
        goTo(goToPage);
    }
     
    function next(){
        goToPage = parseInt(pager.data("curr")) + 1;
        goTo(goToPage);
    }
    
    function goTo(page){
        var startAt = page * perPage,
            endOn = startAt + perPage;
        
        children.css('display','none').slice(startAt, endOn).show();
        
        if (page>=1) {
            pager.find('.prev_link').show();
        }
        else {
            pager.find('.prev_link').hide();
        }
        
        if (page<(numPages-1)) {
            pager.find('.next_link').show();
        }
        else {
            pager.find('.next_link').hide();
        }
        
        pager.data("curr",page);
       
        if (settings.numbersPerPage>1) {
       		$('.page_link').hide();
       		$('.page_link').slice(page, settings.numbersPerPage+page).show();
    	}
      
      	pager.children().removeClass("active");
        pager.children().eq(page+1).addClass("active");  
    }
};

//$('#items').pageMe({pagerSelector:'#myPager',childSelector:'tr',showPrevNext:true,hidePageNumbers:false,perPage:15});
</script>

<?php //$page_instance->add_footer(); ?>
<?php $page_instance->close_page(); ?>