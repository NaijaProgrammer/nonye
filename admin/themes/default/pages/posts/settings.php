<?php
if($_SERVER['REQUEST_METHOD'] == 'POST')
{  
	foreach($_POST AS $key => $value)
	{
		$$key = trim($value);
	}
	
	$validate = Validator::validate(array(
		array( 'error_condition'=>!UserModel::user_is_logged_in(), 'error_message'=>'You must be logged in to perform this action', 'error_type'=>'unauthenticatedUser'),
		array( 'error_condition'=>!user_can('Manage Posts'), 'error_message'=>'You lack sufficient privilege to perform this action', 'error_type'=>'insufficientPrivilege')
	));

	if($validate['error'])
	{
		$return_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
	}

	else
	{
		update_app_settings( array(
		    'show-post-title-field'       => $show_post_title_field,
			'require-post-title-field'    => $require_post_title_field, 
			
			'show-post-forum-field'       => $show_post_forum_field,
			'require-post-forum-field'    => $require_post_forum_field,
			
			'show-post-category-field'    => $show_post_category_field,
			'require-post-category-field' => $require_post_category_field,
			
			'show-post-body-field'        => $show_post_body_field,
			'require-post-body-field'     => $require_post_body_field,
			
			'show-post-tags-field'        => $show_post_tags_field,
			'minimum-post-tags'           => $minimum_post_tags
		) );
		
		$return_data = array('success'=>true, 'message'=>'Settings updated');
	}

	create_json_string($return_data, true);
	exit;
}
?>
<?php verify_user_can('Manage Posts'); ?>

<?php
$page_title    = 'Edit Global Posting Settings';
$page_instance = AdminPage::get_instance(array());
$page_instance->load_header('', array('page_title'=>$page_title));
$page_instance->load_nav();
?>
<?php
$show_post_title         = get_app_setting('show-post-title-field', true);
$require_post_title      = get_app_setting('require-post-title-field', true);

$show_post_forum         = get_app_setting('show-post-forum-field', true);
$require_post_forum      = get_app_setting('require-post-forum-field', true);

$show_post_category      = get_app_setting('show-post-category-field', true);
$require_post_category   = get_app_setting('require-post-category-field', true);

$show_post_body          = get_app_setting('show-post-body-field', true);
$require_post_body       = get_app_setting('require-post-body-field', true);

$show_post_tags          = get_app_setting('show-post-tags-field', true);
$minimum_post_tags_count = get_app_setting('minimum-post-tags');
?>

<div class="container">
 <?php echo do_page_heading($page_title); ?>
 <div class="inline-block sidebar float-left"><?php include __DIR__. '/sidenav.php'; ?></div>
 <div class="inline-block main-content">
   <div class="form-group">
    <label>When Posting:</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br />
	
	<div class="form-group">
	 <div class="form-control">
	  <input id="show-post-title-field" type="checkbox" <?php echo $show_post_title ? 'checked="checked"' : ''; ?> /> 
	  Show Post Title field &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <input id="require-post-title-field" type="checkbox" <?php echo $require_post_title ? 'checked="checked"' : ''; ?> /> 
	  Post Title field is required
	 </div>
    </div>	
	
	<div class="form-group">
	 <div class="form-control">
	  <input id="show-post-forum-field" type="checkbox" <?php echo $show_post_forum ? 'checked="checked"' : ''; ?> /> 
	  Show Post Forum field &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	  <input id="require-post-forum-field" type="checkbox" <?php echo $require_post_forum ? 'checked="checked"' : ''; ?> /> 
	  Post Forum field is required
	 </div>
	</div>
	
	<div class="form-group">
	 <div class="form-control">
	  <input id="show-post-category-field" type="checkbox" <?php echo $show_post_category ? 'checked="checked"' : ''; ?> /> 
	  Show Post Category field &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	  <input id="require-post-category-field" type="checkbox" <?php echo $require_post_category ? 'checked="checked"' : ''; ?> /> 
	  Post Category field is required
	 </div>
	</div>
	
	<div class="form-group">
	 <div class="form-control">
	  <input id="show-post-body-field" type="checkbox" <?php echo $show_post_body ? 'checked="checked"' : ''; ?> /> 
	  Show Post Body field &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	  <input id="require-post-body-field" type="checkbox" <?php echo $require_post_body ? 'checked="checked"' : ''; ?> /> 
	  Post Body field is required
	 </div>
	</div>
	
	<div class="form-group">
	 <div class="form-control">
	  <input id="show-post-tags-field" type="checkbox" <?php echo $show_post_tags ? 'checked="checked"' : ''; ?> /> 
	  Show Post Tags field &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	  <label>Minimum Post Tags</label>
	  <select id="minimum-post-tags-field" class>
	   <?php for($i = 0; $i <= 10; $i++): ?>
	   <option value="<?php echo $i; ?>" <?php echo set_as_selected_option($i, $minimum_post_tags_count); ?>><?php echo $i; ?></option>
	   <?php endfor; ?>
	  </select>
	 </div>
	</div>
   </div>
   
  <div id="status-message" class="text-centered status-message"></div>
  <div><button id="submit-btn" class="btn btn-primary processing-bg bg-no-repeat bg-right pull-right">Save</button></div>
 </div>
 <div class="inline-block sidebar no-border float-right"></div>
</div>

<script>
(function(){
	
	var showPostTitleFieldID       = 'show-post-title-field';
	var requirePostTitleFieldID    = 'require-post-title-field';
	var showPostForumFieldID       = 'show-post-forum-field';
	var requirePostForumFieldID    = 'require-post-forum-field';
	var showPostCategoryFieldID    = 'show-post-category-field';
	var requirePostCategoryFieldID = 'require-post-category-field';
	var showPostBodyFieldID        = 'show-post-body-field';
	var requirePostBodyFieldID     = 'require-post-body-field';
	var showPostTagsFieldID        = 'show-post-tags-field';
	var minimumPostTagsFieldID     = 'minimum-post-tags-field';
	
	Site.Event.attachListener(showPostTitleFieldID, 'click', ()=>{ 
		if( !$O(showPostTitleFieldID).checked )
		{
			$O(requirePostTitleFieldID).checked = false;
		} 
	});
	
	Site.Event.attachListener(showPostForumFieldID, 'click', ()=>{ 
		if( !$O(showPostForumFieldID).checked )
		{
			$O(requirePostForumFieldID).checked = false;
		} 
	});
	
	Site.Event.attachListener(showPostCategoryFieldID, 'click', ()=>{ 
		if( !$O(showPostCategoryFieldID).checked )
		{
			$O(requirePostCategoryFieldID).checked = false;
		} 
	});
	
	Site.Event.attachListener(showPostBodyFieldID, 'click', ()=>{ 
		if( !$O(showPostBodyFieldID).checked )
		{
			$O(requirePostBodyFieldID).checked = false;
		} 
	});
	
	Site.Event.attachListener(showPostTagsFieldID, 'click', ()=>{ 
		if( !$O(showPostTagsFieldID).checked )
		{
			form.setSelectElementSelectedValue(minimumPostTagsFieldID, '0');
			form.setSelectElementSelectedText(minimumPostTagsFieldID, '0');
		} 
	});
	
	Site.Event.attachListener(requirePostTitleFieldID, 'click', ()=>{ 
		if( $O(requirePostTitleFieldID).checked )
		{
			$O(showPostTitleFieldID).checked = true;
		} 
	});
	
	Site.Event.attachListener(requirePostForumFieldID, 'click', ()=>{ 
		if( $O(requirePostForumFieldID).checked )
		{
			$O(showPostForumFieldID).checked = true;
		} 
	});
	
	Site.Event.attachListener(requirePostCategoryFieldID, 'click', ()=>{ 
		if( $O(requirePostCategoryFieldID).checked )
		{
			$O(showPostCategoryFieldID).checked = true;
		} 
	});
	
	Site.Event.attachListener(requirePostBodyFieldID, 'click', ()=>{ 
		if( $O(requirePostBodyFieldID).checked )
		{
			$O(showPostBodyFieldID).checked = true;
		} 
	});
	
	Site.Event.attachListener(minimumPostTagsFieldID, 'change', ()=>{ 
		if( form.getSelectElementSelectedValue(minimumPostTagsFieldID) > 0 )
		{
			$O(showPostTagsFieldID).checked = true;
		} 
	});
	
	var btnID    = 'submit-btn';
	var msgField = 'status-message';
	Site.Event.attachListener(btnID, 'click', function(e){
		Site.Event.cancelDefaultAction(e);
		disable(btnID);
		showProcessing(btnID);
		
		var params = '' +
		'&show_post_title_field='       + $O(showPostTitleFieldID).checked  +
		'&require_post_title_field='    + $O(requirePostTitleFieldID).checked  +
        '&show_post_forum_field='       + $O(showPostForumFieldID).checked  +
		'&require_post_forum_field='    + $O(requirePostForumFieldID).checked +
		'&show_post_category_field='    + $O(showPostCategoryFieldID).checked  +
		'&require_post_category_field=' + $O(requirePostCategoryFieldID).checked +
		'&show_post_body_field='        + $O(showPostBodyFieldID).checked  +
		'&require_post_body_field='     + $O(requirePostBodyFieldID).checked +
		'&show_post_tags_field='        + $O(showPostTagsFieldID).checked  +
		'&minimum_post_tags='           + form.getSelectElementSelectedValue(minimumPostTagsFieldID);
		
		console.log(params);
		
		Site.Util.runAjax({
			'requestMethod'      : 'POST',
			'requestURL'         : '',
			'timeoutAfter'       : 30,
			'requestData'        : params,
			'debugCallback'      : function(reply){ console.log(reply); },
			'readyStateCallback' : function(){},
			'errorCallback'      : function(xhrObject, aborted)
			{
				if(aborted)
				{
					displayStatusMessage(msgField, 'Sorry. This request timed out. Please try again', 'error');
					hideProcessing(btnID);
					enable(btnID);
				}
			},
			'successCallback' : function(reply)
			{
				var response = Site.Util.parseAjaxResponse(reply.rawValue);
				if(response.error)
				{
					displayStatusMessage(msgField, response.message, 'error');
					switch(response.errorType)
					{
						case 'unauthenticatedUserError'   : setTimeout( function(){ location.reload(); }, 5000 ); break;
						case 'insufficientPrivilegeError' : setTimeout( function(){ location.href='<?php echo ADMIN_URL; ?>'; }, 5000 ); break;
						default : '';
					}
				}
				else if(response.success)
				{
					displayStatusMessage(msgField, response.message, 'success');
				}
				
				hideProcessing(btnID);
				enable(btnID);
			}
		});
	});
})();
</script>
<?php $page_instance->load_footer('', array()); ?>