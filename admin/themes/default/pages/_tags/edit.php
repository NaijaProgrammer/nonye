<?php
if($_SERVER['REQUEST_METHOD'] == 'POST')
{  
	foreach($_POST AS $key => $value)
	{
		$$key = trim($value);
	}
	
	if($action == 'create_tags')
	{
		$tags_array = explode(',', $tags); //tag1Name:tag1Description, tag2Name:tag2Description,
		$tags_count = count($tags_array);
			
		$validate = Validator::validate(array(
			array( 'error_condition'=>!UserModel::user_is_logged_in(), 'error_message'=>'You must be logged in to perform this action', 'error_type'=>'unauthenticatedUser'),
			array( 'error_condition'=>!user_can('Manage Tags'), 'error_message'=>'You lack sufficient privilege to perform this action', 'error_type'=>'insufficientPrivilege'),
			array( 'error_condition'=>empty($tags_array[0]), 'error_message'=>'You must specify at least one tag name', 'error_type'=>'noTagSpecified')
		));
			
		if($validate['error'])
		{
			$return_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
		}
			
		else
		{
			for($i  = 0; $i < $tags_count; $i++)
			{
				$curr_tag_data  = $tags_array[$i]; //tag1Name:tag1Description
				$curr_tag_array = explode(':', $curr_tag_data);
					
				$tag_name = isset($curr_tag_array[0]) ? trim($curr_tag_array[0]) : '';
				$tag_desc = isset($curr_tag_array[1]) ? trim($curr_tag_array[1]) : '';
				
				if( !empty($tag_name) )
				{
					$tag_id = TagModel::create_tag(array(
						'name'          => get_slug(strtolower($tag_name)),
						'description'   => $tag_desc,
						'creator_id'    => UserModel::get_current_user_id()
					));
				}
			}
			
			$return_data = array('success'=>true, 'message'=>'Tags created');
		}
	}
		
	elseif($action == 'update_tag')
	{
		$tag_data     = TagModel::get_tag_data($tag_id);
		$tag_category = isset($tag_data['category']) ? $tag_data['category'] : '';
		$validate     = Validator::validate(array(
			array( 'error_condition'=>!UserModel::user_is_logged_in(), 'error_message'=>'You must be logged in to perform this action', 'error_type'=>'unauthenticatedUser'),
			array( 'error_condition'=>!user_can('Manage Tags'), 'error_message'=>'You lack sufficient privilege to perform this action', 'error_type'=>'insufficientPrivilege'),
			array( 'error_condition'=>empty($tag_category),       'error_message'=>'Invalid Tag ID', 'error_type'=>'wrongID'),
			array( 'error_condition'=>$tag_category != 'post-tags', 'error_message'=>'Invalid Tag ID', 'error_type'=>'falseCategory'),
			array( 'error_condition'=>empty($tag_name),  'error_message'=>'The tag name cannot be empty', 'error_type'=>'emptyTagName'),
		));
			
		if($validate['error'])
		{
			$return_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
		}
			
		else
		{
			TagModel::update_tag_data($tag_id, 'name', $tag_name);
			TagModel::update_tag_data($tag_id, 'description', $tag_desc);
			$return_data = array('success'=>true, 'message'=>'Tag updated successfully');
		}
	}
	
	create_json_string($return_data, true);
	exit;
}
?>
<?php verify_user_can('Manage Tags'); ?>
<?php if(isset($_GET['tag'])): $tag_data = TagModel::get_tag_data($_GET['tag']);  endif; ?>
<?php $page_title = ( !empty($tag_data['category']) && ($tag_data['category'] == 'post-tags') ) ? 'Edit '. $tag_data['name']. ' tag' : 'Create new tag'; ?>
<?php
$page_instance = AdminPage::get_instance(array());
$page_instance->load_header('', array('page_title'=>$page_title));
$page_instance->load_nav();
?>
<div class="container">
 <?php echo do_page_heading($page_title); ?>
 <div class="inline-block sidebar float-left"><?php include __DIR__. '/sidenav.php'; ?></div>
 <div class="inline-block main-content" style="width:55%;">
  <?php if( !empty($tag_data['category']) && ($tag_data['category'] == 'post-tags') ): ?>
   <div class="form-group">
    <label>Tag Name</label>
    <input id="tag-name" type="text" class="form-control" value="<?php echo $tag_data['name']; ?>" />
   </div>
   <div class="form-group">
    <label>Tag Description</label>
    <textarea id="tag-description" class="form-control resize-vertical"><?php echo isset($tag_data['description']) $tag_data['description'] : ''; ?></textarea>
   </div>
   <input id="tag-id" type="hidden" value="<?php echo $tag_data['id']; ?>"/>
  <?php else: ?>
   <div class="form-group">
    <label>Tag Names and Descriptions</label>
    <textarea id="tags" class="form-control resize-vertical" placeholder="Enter tag names and descriptions in the form: tag1Name:tag1Description, tag2Name:tag2Description"></textarea>
   </div>
  <?php endif; ?>
  <div id="status-message" class="text-centered status-message"></div>
  <div><button id="submit-btn" class="btn btn-primary processing-bg bg-no-repeat bg-right pull-right">Save</button></div>
 </div>
 <div class="inline-block sidebar no-border float-right"></div>
</div>
<script>
(function(){
	var btnID    = 'submit-btn';
	var msgField = 'status-message';
	Site.Event.attachListener(btnID, 'click', function(e){
		Site.Event.cancelDefaultAction(e);
		disable(btnID);
		showProcessing(btnID);
		
		<?php if( !empty($tag_data['category']) && ($tag_data['category'] == 'post-tags') ): ?>
		var params = '' +
		'action='       + 'update_tag'         +
		'&tag_id='      + $O('tag-id').value   +
		'&tag_name='    + $O('tag-name').value +
		'&tag_desc='    + $O('tag-description').value;
		<?php else: ?>
		var params = '' +
		'action='       + 'create_tags' +
		'&tags='        + $O('tags').value;
		<?php endif; ?>
		
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
					if(response.message.toLowerCase() == 'tags created')
					{
						$O('tags').value = '';
					}
				}
				
				hideProcessing(btnID);
				enable(btnID);
			}
		});
	});
})();
</script>
<?php $page_instance->load_footer('', array()); ?>