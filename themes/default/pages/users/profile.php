<?php $current_user_id = is_object($current_user) ? $current_user->get('id') : 0; ?>
<?php
$page_instance->add_header(array(
	'page_title'       => $page_title,
	'page_keywords'    => $page_keywords,
	'page_description' => $page_description,
	'robots_value'     => $robots_value,
	'open_graph_data'  => $open_graph_data,
	'current_user'     => $current_user //coming from the app-controller class
));

$page_instance->add_nav();

if($owner_id == $current_user_id)
{
	$page_instance->add_stylesheets( array(SITE_URL. '/js/lib/dropzone/dropzone.css') );
	$page_instance->add_scripts( array(SITE_URL. '/js/lib/dropzone/dropzone.js', get_theme_url(). '/pages/users/profile-image-functions.js') );
}
?>
<?php $page_instance->add_nav('secondary-navigation'); ?>

<div class="container main-container">

 <div class="row">
  <div class="col-sm-3 text-center" id="owner-photo-container">
    <?php $image_title = ($owner_id == $current_user_id) ? 'Click to change your profile image' : $owner->get('username'). '\'s image'; ?>
    <img id="user-photo" class="user-image cursor-pointer" title="<?php echo sanitize_html_attribute($image_title); ?>" src="<?php echo sanitize_html_attribute($owner->get('image-url', get_app_setting('default-user-image-url'))); ?>">
	
	<?php if($owner_id == $current_user_id): ?>
	<div id="image-crop-preview"></div>
    <div class="text-centered" style="margin-bottom:0;">
     <span id="profile-pix-processing">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
     <?php
     $cropper_unique_id_prefix = 'userImage';
	 include(SITE_DIR. '/lib/image-cropper/image-cropper.php');
     echo ImageCropper::get_image_cropper(array(
		'plugin_url'                   => SITE_URL. '/lib/image-cropper',
		'unique_id_prefix'             => $cropper_unique_id_prefix,
		'action_page'                  => SITE_URL. '/ajax/index.php', 
		'post_data'                    => array('p'=>'users'),
		'include_thumb_scale_details'  => false, 
		'crop_button_value'            => 'Save',
		'crop_processing_callback'     => 'cropProcessingCallback',
		'crop_success_callback'        => 'cropSuccessCallback'
	 ));
	 ?>
     <style>.dz-preview, .dz-success-mark, .dz-error-mark{display:none;}</style>
     <script>$(document).ready( function(){activateUploaderOn('user-photo', {imageCropperIDPrefix:'<?php echo  $cropper_unique_id_prefix; ?>'})} );</script>
    </div>
	
	<?php else: ?>
	<div>&nbsp;</div>
	<?php endif; ?>
	
	<a href="<?php echo sanitize_html_attribute(get_user_profile_url($owner_id)); ?>"><h1 style="margin-top:0;"><?php echo $owner->get('username'); ?></h1></a>
  </div>
 </div>
 
 <div class="row">
  <div class="col-sm-3">
   <ul class="list-group">
    <li class="list-group-item text-muted">Profile <small><i class="fa fa-user fa-1x"></i></small></li>
    <li class="list-group-item text-right"><span class="pull-left"><strong>Joined</strong></span>&nbsp;<?php echo format_date($owner->get('date_registered'), 'F d, Y'); ?></li>
    <li class="list-group-item text-right">
	 <span class="pull-left"><strong>Last seen</strong></span> <?php echo get_time_elapsed_intelligent(format_date(format_time($owner->get('last-seen-time')))); ?></li>
    <li class="list-group-item text-right"><span class="pull-left"><strong>Real name</strong></span><?php echo $owner->get('firstname', ''). '&nbsp;'. $owner->get('lastname', ''); ?></li>       
    <?php if( ($owner_id == $current_user_id) || ($owner->get('email-visibility') == 'public') ): ?>
	<li class="list-group-item text-right"><span class="pull-left"><strong>Email</strong></span>&nbsp;<?php echo $owner->get('email'); ?></li>
	<?php endif; ?>
   </ul> 
   <ul class="list-group">
    <li class="list-group-item text-muted">Activity <small><i class="fa fa-dashboard fa-1x"></i></small></li>
    <!--<li class="list-group-item text-right"><span class="pull-left"><strong>Shares</strong></span> 125</li>-->
    <!--<li class="list-group-item text-right"><span class="pull-left"><strong>Likes</strong></span> 13</li>-->
    <li class="list-group-item text-right">
	 <span class="pull-left"><strong>Posts</strong></span> 
	 <a title="<?php echo sanitize_html_attribute('click to view posts by '. $owner->get('username')); ?>" href="<?php echo sanitize_html_attribute( generate_url(array('controller'=>'posts', 'action'=>'author', 'qs'=>array($owner->get('username')))) ); ?>">
	  <?php echo count(get_user_posts($owner_id, array('parent_id'=>0))); ?>
	 </a>
	</li>
    <!--<li class="list-group-item text-right"><span class="pull-left"><strong>Followers</strong></span> 78</li>-->
   </ul>
   <div class="panel panel-default">
    <div class="panel-heading">Online Profile</div>
    <div class="panel-body">
	 <a title="website" href="<?php echo sanitize_html_attribute($owner->get('website-url')); ?>" target="_blank"><i class="fa glyphicon glyphicon-link fa-2x"></i></a>
     <a title="facebook page" href="<?php echo sanitize_html_attribute($owner->get('facebook-url')); ?>" target="_blank"><i class="fa fa-facebook fa-2x"></i></a> 
	 <a title="google plus page" href="<?php echo sanitize_html_attribute($owner->get('google-plus-url')); ?>" target="_blank"><i class="fa fa-google-plus fa-2x"></i></a>
	 <a title="instagram page" href="<?php echo sanitize_html_attribute($owner->get('instagram-url')); ?>" target="_blank"><i class="fa fa-instagram fa-2x"></i></a>
	 <a title="linked-in page" href="<?php echo sanitize_html_attribute($owner->get('linked-in-url')); ?>" target="_blank"><i class="fa fa-linkedin fa-2x"></i></a>
	 <a title="twitter page" href="<?php echo sanitize_html_attribute($owner->get('twitter-url')); ?>" target="_blank"><i class="fa fa-twitter fa-2x"></i></a>
	 <a title="youtube page" href="<?php echo sanitize_html_attribute($owner->get('youtube-url')); ?>" target="_blank"><i class="fa fa-youtube fa-2x"></i></a>
    </div>
   </div>

   <?php $page_instance->add_sidebar('popular-links'); ?>
  </div>
  <div class="col-sm-9">
   <ul class="nav nav-tabs" id="profile-tabs">
    <li class="active"><a href="#activity" data-toggle="tab" data-tab="activity">Recent Activity</a></li>
	<?php if( $owner->get('id') == $current_user_id ): ?>
	 <li><a href="#settings" data-toggle="tab" data-tab="settings">Settings</a></li>
	 <li><a href="#web-settings" data-toggle="tab" data-tab="web-settings">Web Presence</a></li>
	 <li><a href="#password-settings" data-toggle="tab" data-tab="password-settings">Change Password</a></li>
	<?php endif; ?>
   </ul>
   <div class="tab-content">
	<div class="tab-pane active" id="activity">
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
     <form class="form">
      <div class="form-group">
       <div class="col-xs-6">
        <label for="first_name"><h4>First name</h4></label>
        <input id="firstname-field" class="form-control" type="text"  placeholder="first name" title="enter your first name if any." value="<?php echo $owner->get('firstname'); ?>">
       </div>
      </div>
      <div class="form-group">
       <div class="col-xs-6">
        <label for="last_name"><h4>Last name</h4></label>
        <input id="lastname-field" class="form-control" type="text" placeholder="last name" title="enter your last name if any." value="<?php echo $owner->get('lastname'); ?>">
       </div>
      </div>
	  <div class="form-group">
       <div class="col-xs-6">
        <label for="email">
		 <h4>Email&nbsp;
		  <small>Make my email public</small>&nbsp;
		  <input id="email-visibility-modifier" type="checkbox" style="vertical-align:middle;" <?php echo ($owner->get('email-visibility') == 'public') ? 'checked' : ''; ?>/>
		 </h4>
		</label>
        <input id="email-field" class="form-control" type="email" placeholder="you@email.com" title="enter your email." value="<?php echo $owner->get('email'); ?>">
       </div>
      </div>
	  <div class="form-group">
       <div class="col-xs-6">
        <label for="mobile"><h4>Username</h4></label>
        <input id="username-field" class="form-control" type="text" placeholder="enter your username" title="enter your username" value="<?php echo $owner->get('username'); ?>">
       </div>
      </div>
      <div class="form-group">
       <div class="col-xs-6">
        <label for="mobile number"><h4>Mobile</h4></label>
        <input id="mobile-number-field" class="form-control" type="text" placeholder="enter your mobile number" title="enter your mobile number" value="<?php echo $owner->get('mobile-number'); ?>">
       </div>
      </div>
      <div class="form-group">
       <div class="col-xs-6">
        <label for="location"><h4>Location</h4></label>
        <input id="location-field" type="text" class="form-control" placeholder="somewhere" title="enter a location" value="<?php echo $owner->get('location'); ?>">
       </div>
      </div>
      <div class="form-group">
       <div class="col-xs-12"><br>
		<span class="pull-right">
         <button id="profile-update-btn" class="btn btn-lg btn-success pl25 pr25" type="submit"><i class="glyphicon glyphicon-ok-sign"></i> Save</button>
         <!--<button class="btn btn-lg" type="reset"><i class="glyphicon glyphicon-repeat"></i> Reset</button>-->
		</span>
       </div>
      </div>
     </form>
    </div>
	
	<div class="tab-pane" id="web-settings">
     <form class="form">
      <div class="form-group">
	   <div class="col-xs-6">
	    <label for="website"><h4>Website</h4></label>
	    <input class="form-control" id="website-url-field" placeholder="http://mywebsite.com" value="<?php echo $owner->get('website-url'); ?>">
	   </div>
	  </div>
      <div class="form-group">
	   <div class="col-xs-6">
	    <label for="facebook page"><h4>Facebook Page</h4></label>
	    <input class="form-control" id="facebook-url-field" placeholder="http://facebook.com/profile-url" value="<?php echo $owner->get('facebook-url'); ?>">
	   </div>
	  </div>
	  <div class="form-group">
	   <div class="col-xs-6">
	    <label for="google plus page"><h4>Google+ Page</h4></label>
	    <input class="form-control" id="google-plus-url-field" placeholder="plus.google.com/profile-url" value="<?php echo $owner->get('google-plus-url'); ?>">
	   </div>
 	  </div>
	  <div class="form-group">
	   <div class="col-xs-6">
	    <label for="instagram page"><h4>Instagram Page</h4></label>
	    <input class="form-control" id="instagram-url-field" placeholder="http://instagram.com/profile-url" value="<?php echo $owner->get('instagram-url'); ?>">
	   </div>
	  </div>
	  <div class="form-group">
	   <div class="col-xs-6">
	    <label for="linked-in page"><h4>LinkedIn Page</h4></label>
	    <input class="form-control" id="linkedin-url-field" placeholder="http://linkedin.com/profile-url" value="<?php echo $owner->get('linked-in-url'); ?>">
	   </div>
	  </div>
	  <div class="form-group">
	   <div class="col-xs-6">
	    <label for="twitter page"><h4>Twitter Page</h4></label>
	    <input class="form-control" id="twitter-url-field" placeholder="@twitter-handle" value="<?php echo $owner->get('twitter-url' ); ?>">
	   </div>
	  </div>
	  <div class="form-group">
	   <div class="col-xs-6">
	    <label for="you tube channel"><h4>YouTube Page</h4></label>
	    <input class="form-control" id="youtube-url-field" placeholder="http://youtube.com/profile-url" value="<?php echo $owner->get('youtube-url'); ?>">
	   </div>
	  </div>
      <div class="form-group">
       <div class="col-xs-12"><br>
		<span class="pull-right">
         <button id="web-profile-update-btn" class="btn btn-lg btn-success pl25 pr25" type="submit"><i class="glyphicon glyphicon-ok-sign"></i> Save</button>
		</span>
       </div>
      </div>
     </form>
    </div>
	
	<div class="tab-pane" id="password-settings">
     <form class="form">
	  <div class="form-group">
	   <div class="col-xs-12">
	    <label for="current password"><h4>Current Password</h4></label>
	    <input class="form-control" id="current-password-field" placeholder="Enter you current password" type="password">
	   </div>
	  </div>
      <div class="form-group">
       <div class="col-xs-6">
        <label for="new password"><h4>New Password</h4></label>
        <input id="new-password-field" type="password" class="form-control" placeholder="password" title="enter your password.">
       </div>
      </div>
      <div class="form-group">
       <div class="col-xs-6">
        <label for="password confirmation"><h4>Verify New Password</h4></label>
        <input id="new-password-confirmation-field" type="password" class="form-control" placeholder="verify password" title="Verify your new password.">
       </div>
      </div>
      <div class="form-group">
       <div class="col-xs-12"><br>
		<span class="pull-right">
         <button id="password-update-btn" class="btn btn-lg btn-success pl25 pr25" type="submit"><i class="glyphicon glyphicon-ok-sign"></i> Save</button>
         <!--<button class="btn btn-lg" type="reset"><i class="glyphicon glyphicon-repeat"></i> Reset</button>-->
		</span>
       </div>
      </div>
     </form>
    </div>

	<div class="clear">&nbsp;</div>
	<div id="status-message" class="col-xs-12 text-center"></div>
	<?php endif; ?>
	
   </div>
  </div>
 </div>
</div>
<?php if(UserModel::user_is_logged_in()): ?>
<script>
$(document).ready(function(){
	autoSelectTab();
	
	$('#profile-update-btn').on('click', function(e){
		
		e.preventDefault();
		disable('profile-update-btn');
		setAsProcessing('profile-update-btn');
		
		$.ajax(ajaxURL + '/index.php', {
			method : 'POST',
			cache  : false,
			data   : { 
				p                  : 'users', 
				'update-user-data' : true, 
				firstname          : $('#firstname-field').val(),
				lastname           : $('#lastname-field').val(),
				username           : $('#username-field').val(),
				email              : $('#email-field').val(),
				mobile_number      : $('#mobile-number-field').val(),
				email_visibility   : $('#email-visibility-modifier')[0].checked ? 'public' : 'private',
				location           : $('#location-field').val()
			},
			error : function(jqXHR, status, error){
				if(isDevServer)
				{
					console.log( 'User data update status : ' + status + '\r\nerror : ' + error );
				}
			},
			success  : function(data, status, jqXHR){
				if(isDevServer)
				{
					console.log( 'User data update status : ' + status + '\r\nsuccess : ' + data );
				}
				data = JSON.parse(data);
				if(data.error)
				{
					displayStatusMessage('status-message', data.message, 'error');
					if(data.errorType == 'unauthenticatedUserError')
					{
						reloadPage();
					}
				}
				else if(data.success)
				{
					displayStatusMessage('status-message', 'Account data successfully updated', 'success');
				}
			},
			complete : function(jqXHR, status)
			{
				unsetAsProcessing('profile-update-btn');
				enable('profile-update-btn');
			}
		})
	});

	$('#password-update-btn').on('click', function(e){
		
		e.preventDefault();
		disable('password-update-btn');
		setAsProcessing('password-update-btn');
		
		$.ajax(ajaxURL + '/index.php', {
			method : 'POST',
			cache  : false,
			data   : { 
				p                      : 'users', 
				'update-user-password' : true, 
				'current_password'     : $('#current-password-field').val(),
				'new_password'         : $('#new-password-field').val(),
				'new_password_confirm' : $('#new-password-confirmation-field').val()
			},
			error : function(jqXHR, status, error){
				if(isDevServer)
				{
					console.log( 'User password update status : ' + status + '\r\nerror : ' + error );
				}
			},
			success  : function(data, status, jqXHR){
				if(isDevServer)
				{
					console.log( 'User password update status : ' + status + '\r\nsuccess : ' + data );
				}
				data = JSON.parse(data);
				if(data.error)
				{
					displayStatusMessage('status-message', data.message, 'error');
					if(data.errorType == 'unauthenticatedUserError')
					{
						reloadPage();
					}
				}
				else if(data.success)
				{
					var msg = 'Password successfully updated';
					
					if( (typeof data.reauthenticateUser !== 'undefined') && (data.reauthenticateUser) )
					{
						msg += '<br>You will be logged out in a moment so that you can login again';
						reloadPage();
					}
					
					displayStatusMessage('status-message', msg);
				}
			},
			complete : function(jqXHR, status)
			{
				unsetAsProcessing('password-update-btn');
				enable('password-update-btn');
			}
		})
	});

	$('#web-profile-update-btn').on('click', function(e){
		
		e.preventDefault();
		disable('web-profile-update-btn');
		setAsProcessing('web-profile-update-btn');
		
		$.ajax(ajaxURL + '/index.php', {
			method : 'POST',
			cache  : false,
			data   : { 
				p                         : 'users', 
				'update-user-online-data' : true,
				'website_url'             : $('#website-url-field').val(),
				'facebook_url'            : $('#facebook-url-field').val(),
				'google_plus_url'         : $('#google-plus-url-field').val(),
				'instagram_url'           : $('#instagram-url-field').val(),
				'linkedin_url'            : $('#linkedin-url-field').val(),
				'twitter_url'             : $('#twitter-url-field').val(),
				'youtube_url'             : $('#youtube-url-field').val()
			},
			error : function(jqXHR, status, error){
				if(isDevServer)
				{
					console.log( 'User web profile update status : ' + status + '\r\nerror : ' + error );
				}
			},
			success  : function(data, status, jqXHR){
				if(isDevServer)
				{
					console.log( 'User web profile update status : ' + status + '\r\nsuccess : ' + data );
				}
				data = JSON.parse(data);
				if(data.error)
				{
					displayStatusMessage('status-message', data.message, 'error');
					if(data.errorType == 'unauthenticatedUserError')
					{
						reloadPage();
					}
				}
				else if(data.success)
				{
					displayStatusMessage('status-message', 'Online account data successfully updated', 'success');
				}
			},
			complete : function(jqXHR, status)
			{
				unsetAsProcessing('web-profile-update-btn');
				enable('web-profile-update-btn');
			}
		})
	});
	
	$(window).on('popstate', autoSelectTab); //handle clicking of browser's 'back' button
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e){
		//e.target // newly activated tab
		//e.relatedTarget // previous active tab
		var selectedTab = '';
		console.log(e.target.getAttribute('data-tab'));
		switch(e.target.getAttribute('data-tab'))
		{
			case 'settings'          : selectedTab = 'settings'; break;
			case 'password-settings' : selectedTab = 'password'; break;
			case 'web-settings'      : selectedTab = 'e-presence'; break;
			default : selectedTab = 'activity'; break;
		}
		
		updateUrl(selectedTab);
	});
	
	function reloadPage()
	{
		setTimeout( function(){ location.reload(); }, 5000 );
	}
	function autoSelectTab()
	{
		var activeTab = Site.Util.getQueryStringParameterValue('tab');
		switch(activeTab)
		{
			case 'settings'   : $('#profile-tabs a[href="#settings"]').tab('show'); break;
			case 'password'   : $('#profile-tabs a[href="#password-settings"]').tab('show'); break;
			case 'e-presence' : $('#profile-tabs a[href="#web-settings"]').tab('show'); break;
			default           : $('#profile-tabs a[href="#activity"]').tab('show'); break;
		}
	}
	function updateUrl(selectedTab)
	{
		history.pushState(null, "", '?tab=' + selectedTab);
	}
});
</script>
<?php endif; ?>

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