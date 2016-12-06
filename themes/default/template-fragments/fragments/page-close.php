<?php if(UserModel::user_is_logged_in()): ?>
<script>
(function compulsoryActions(){
	setTimeout(function updateUserLastSeen(){
		$.ajax(ajaxURL, {
			method : 'GET',
			cache  : true,
			data   : { p : 'users', 'update-user-last-seen' : true, 'url':'<?php echo get_current_url(); ?>' },
			error : function(jqXHR, status, error){
				if(isDevServer)
				{
					console.log( 'User last seen status : ' + status + '\r\nerror : ' + error );
				}
			},
			success  : function(data, status, jqXHR){
				if(isDevServer)
				{
					console.log( 'User last seen status : ' + status + '\r\nsuccess : ' + data );
				}
			},
			complete : function(jqXHR, status)
			{
				setTimeout(updateUserLastSeen, 1000 * 30);
			}
		})
	}, 1000);
	setTimeout(function getUserNotifications(){
		$.ajax(ajaxURL, {
			method : 'GET',
			cache  : true,
			data   : { p : 'users', 'get-notifications' : true, 'last-seen-notification-id':getMaxNotificationID(), 'user-id':'<?php echo UserModel::get_current_user_id(); ?>' },
			error : function(jqXHR, status, error){
				if(isDevServer)
				{
					console.log( 'User notification status : ' + status + '\r\nerror : ' + error );
				}
			},
			success  : function(data, status, jqXHR){
				if(isDevServer)
				{
					console.log( 'User notification status : ' + status + '\r\nsuccess : ' + data );
				}
				data = JSON.parse(data);

				if(data.length > 0)
				{
					updateNotificationsBar(data);
					highlightNotificationsBar();
				}
			},
			complete : function(jqXHR, status)
			{
				setTimeout(getUserNotifications, 1000 * 5);
			}
		});

		function getMaxNotificationID()
		{
			var IDS = getDisplayedNotifications();
			return ( (IDS.length > 0) ? getMaxNumber(IDS) : 0 );
		}
		function updateNotificationsBar(notifications)
		{
			var notificationsCount = parseInt( $('.notification-counter').html() );

			if(isNaN(notificationsCount))
			{
				notificationsCount = 0;
			}
			for( var i = 0, len = notifications.length; i < len; i++ )
			{
				++notificationsCount;
				addNotificationToList(notifications[i]);
			}
			function addNotificationToList(notification)
			{
				$('#notifications').html( $('#notifications').html() + '<li>' + notification.activity + '</li>' );
				addToDisplayedNotificationsQueue(notification.id);
			}

			updateNotificationsCounter(notificationsCount, true);
		}

	}, 1000);

	$('#notifications-toggler').on('click', function(){

		setTimeout(function(){
			//if the notifications bar is open
			if( $('#user-notification-dropdown-container')[0].className.indexOf('open') != -1 )
			{
				clearDisplayedNotificationsQueue();
				unhighlightNotificationsBar();
				updateNotificationsCounter('', false);
			}
		}, 10);

		function clearDisplayedNotificationsQueue()
		{
			var notificationIDS = getDisplayedNotifications();
			for(var i = 0, len = notificationIDS.length; i < len; i++)
			{
				setNotificationAsSeen(notificationIDS[i]);
			}

			function setNotificationAsSeen(notificationID)
			{
				$.ajax(ajaxURL + '/index.php', {
					method : 'POST',
					cache  : true,
					data   : { p:'users', 'set-notification-as-seen':true, 'id':notificationID },
					error : function(jqXHR, status, error){},
					success  : function(data, status, jqXHR){
						if(isDevServer)
						{
							console.log( 'User notification update status : ' + status + '\r\nsuccess : ' + data );
						}
					},
					complete : function(jqXHR, status){}
				});
			}
		}
	});

	var displayedNotificationIDS = [];

	function addToDisplayedNotificationsQueue(notificationID)
	{
		displayedNotificationIDS.push(notificationID);
	}
	function getDisplayedNotifications()
	{
		return displayedNotificationIDS;
	}
	function highlightNotificationsBar()
	{
		$('#notifications-toggler').addClass('new-notifications');
	}
	function unhighlightNotificationsBar()
	{
		$('#notifications-toggler').removeClass('new-notifications');
	}
	function updateNotificationsCounter(count, activate)
	{
		activate ? $('.notification-counter').addClass('active-counter') : $('.notification-counter').removeClass('active-counter');
		$('.notification-counter').html(count);
	}
})();
</script>
<?php endif; ?>

<script>
$(document).on('scroll', function(){

	var mainNav = $('#main-navigation');
	if( $(document).scrollTop() > $("#main-navigation").height() )
	{
		mainNav.removeClass('main-navigation-bottom-border');
		mainNav.addClass('main-navigation-box-shadow');
	}
	else
	{
		mainNav.removeClass('main-navigation-box-shadow');
		mainNav.addClass('main-navigation-bottom-border');
	}
});

//bootstrap affix plugin:
//http://www.tutorialrepublic.com/twitter-bootstrap-tutorial/bootstrap-affix.php
//https://www.sitepoint.com/understanding-bootstraps-affix-scrollspy-plugins/
//http://stackoverflow.com/a/13546786/1743192
$(document).ready(function(){
	/* affix the navbar after scroll below header */
	/*
	$('#main-navigation').affix({
		offset: {
			top: $("#main-navigation").outerHeight(true)
		}
	});
	*/
	/* smooth scrolling for scroll to top */
	$('.scroll-top').click(function(){
	  $('body,html').animate({scrollTop:0},1000);
	})
});
</script>
<script src="<?php echo $theme_url; ?>/js/bootstrap.min.js"></script>
<?php
import_admin_functions();
//if( UserModel::user_is_logged_in() ) : $page_instance->add_fragment( 'post-editor', array('value'=>'', 'placeholder'=>'Type in your post here') );
if( user_can('Create Posts') )
{
	get_post_editor( $opts = array('placeholder'=>'Enter Post', 'value'=>'', 'show_on_init'=>false) );
}

if( !UserModel::user_is_logged_in() )
{
	$page_instance->add_fragment( 'login-signup-forms', array() );
}
?>

<?php //include(INCLUDES_DIR. '/user-timezone-setter.php'); ?>
<?php //echo date_default_timezone_get(); ?>
<?php //<div class="scroll-top cursor-pointer pull-right" style="position:static; bottom:5px;">Top</div> ?>

 </body>
</html>
