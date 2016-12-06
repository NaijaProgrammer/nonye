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
			for($i = 0; $i < $tags_count; $i++)
			{
				$curr_tag_data  = $tags_array[$i]; //tag1Name:tag1Description
				$curr_tag_array = explode(':', $curr_tag_data);
					
				$tag_name = isset($curr_tag_array[0]) ? trim($curr_tag_array[0]) : '';
				$tag_desc = isset($curr_tag_array[1]) ? trim($curr_tag_array[1]) : '';
				
				if( !empty($tag_name) )
				{
					$tag = TagModel::create(array(
						'name'        => $tag_name,
						'description' => $tag_desc,
						'creator_id'  => UserModel::get_current_user_id()
					));
				}
			}
			
			$return_data = array('success'=>true, 'message'=>'Tags created');
		}
	}
		
	elseif($action == 'update_tag')
	{
		$validate = Validator::validate(array(
			array( 'error_condition'=>!UserModel::user_is_logged_in(), 'error_message'=>'You must be logged in to perform this action', 'error_type'=>'unauthenticatedUser'),
			array( 'error_condition'=>!user_can('Manage Tags'), 'error_message'=>'You lack sufficient privilege to perform this action', 'error_type'=>'insufficientPrivilege'),
			array( 'error_condition'=>empty($tag_id), 'error_message'=>'No Tag Specified', 'error_type'=>'emptyTagID'),
		));
			
		if($validate['error'])
		{
			$return_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
		}
			
		else
		{
			$tag = TagModel::get_tag_instance($tag_id);
			if( $tag->update( array('name'=>$tag_name, 'description'=>$tag_desc) ) )
			{
				$return_data = array('success'=>true, 'message'=>'Tag updated successfully');
			}
			else
			{
				$return_data = array('error'=>true, 'message'=>'Update operation failed', 'errorType'=>'updateOperationFailureError');
			}
		}
	}
	
	create_json_string($return_data, true);
	exit;
}
?>
<?php verify_user_can('Manage Tags'); ?>

<?php
$page_title    = '';
$page_instance = AdminPage::get_instance(array());
$page_instance->load_header('', array('page_title'=>$page_title));
$page_instance->load_nav();
?>
<?php $mode = isset($_GET['tag']) && !empty($_GET['tag']) ? 'edit' : 'create'; ?>
<div class="container">
 <?php echo do_page_heading($page_title); ?>
 <div class="inline-block sidebar float-left"><?php include __DIR__. '/sidenav.php'; ?></div>
 <div class="inline-block main-content" style="width:55%;">
  <?php if( $mode == 'edit' ): ?>
  <?php $tag = TagModel::get_tag_instance($_GET['tag']); ?>
   <div class="form-group">
    <label>Tag Name</label>
    <input id="tag-name" type="text" class="form-control" value="<?php echo $tag->get('name'); ?>" />
   </div>
   <div class="form-group">
    <label>Tag Description</label>
    <textarea id="tag-description" class="form-control resize-vertical"><?php echo $tag->get('description'); ?></textarea>
   </div>
   <input id="tag-id" type="hidden" value="<?php echo $tag->get('id'); ?>"/>
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
		
		<?php if( $mode == 'edit' ): ?>
		var params = '' +
		'action='       + 'update_tag'         +
		'&tag_id='    + $O('tag-id').value   +
		'&tag_name='  + $O('tag-name').value +
		'&tag_desc='  + $O('tag-description').value;
		<?php else: ?>
		var params = '' +
		'action='       + 'create_tags' +
		'&tags='      + $O('tags').value;
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