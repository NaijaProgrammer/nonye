<?php
if($_SERVER['REQUEST_METHOD'] == 'POST')
{  
	foreach($_POST AS $key => $value)
	{
		$$key = trim($value);
	}
	
	if($action == 'create_forums')
	{
		$forums_array = explode(',', $forums); //forum1Name:forum1Description, forum2Name:forum2Description,
		$forums_count = count($forums_array);
			
		$validate = Validator::validate(array(
			array( 'error_condition'=>!UserModel::user_is_logged_in(), 'error_message'=>'You must be logged in to perform this action', 'error_type'=>'unauthenticatedUser'),
			array( 'error_condition'=>!user_can('Manage Forums'), 'error_message'=>'You lack sufficient privilege to perform this action', 'error_type'=>'insufficientPrivilege'),
			array( 'error_condition'=>empty($forums_array[0]), 'error_message'=>'You must specify at least one forum name', 'error_type'=>'noForumSpecified')
		));
			
		if($validate['error'])
		{
			$return_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
		}
			
		else
		{
			for($i = 0; $i < $forums_count; $i++)
			{
				$curr_forum_data  = $forums_array[$i]; //forum1Name:forum1Description
				$curr_forum_array = explode(':', $curr_forum_data);
					
				$forum_name = isset($curr_forum_array[0]) ? trim($curr_forum_array[0]) : '';
				$forum_desc = isset($curr_forum_array[1]) ? trim($curr_forum_array[1]) : '';
				
				if( !empty($forum_name) )
				{
					$forum = ForumModel::create(array(
						'name'        => $forum_name,
						'description' => $forum_desc,
						'creator_id'  => UserModel::get_current_user_id()
					));
				}
			}
			
			$return_data = array('success'=>true, 'message'=>'Forums created');
		}
	}
		
	elseif($action == 'update_forum')
	{
		$validate = Validator::validate(array(
			array( 'error_condition'=>!UserModel::user_is_logged_in(), 'error_message'=>'You must be logged in to perform this action', 'error_type'=>'unauthenticatedUser'),
			array( 'error_condition'=>!user_can('Manage Forums'), 'error_message'=>'You lack sufficient privilege to perform this action', 'error_type'=>'insufficientPrivilege'),
			array( 'error_condition'=>empty($forum_id), 'error_message'=>'No Forum Specified', 'error_type'=>'emptyForumID'),
		));
			
		if($validate['error'])
		{
			$return_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
		}
			
		else
		{
			$forum = ForumModel::get_forum_instance($forum_id);
			if( $forum->update( array('name'=>$forum_name, 'description'=>$forum_desc) ) )
			{
				$return_data = array('success'=>true, 'message'=>'Forum updated successfully');
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
<?php verify_user_can('Manage Forums'); ?>

<?php
$page_title    = '';
$page_instance = AdminPage::get_instance(array());
$page_instance->load_header('', array('page_title'=>$page_title));
$page_instance->load_nav();
?>
<?php $mode = isset($_GET['forum']) && !empty($_GET['forum']) ? 'edit' : 'create'; ?>
<div class="container">
 <?php echo do_page_heading($page_title); ?>
 <div class="inline-block sidebar float-left"><?php include __DIR__. '/sidenav.php'; ?></div>
 <div class="inline-block main-content" style="width:55%;">
  <?php if( $mode == 'edit' ): ?>
  <?php $forum = ForumModel::get_forum_instance($_GET['forum']); ?>
   <div class="form-group">
    <label>Forum Name</label>
    <input id="forum-name" type="text" class="form-control" value="<?php echo $forum->get('name'); ?>" />
   </div>
   <div class="form-group">
    <label>Forum Description</label>
    <textarea id="forum-description" class="form-control resize-vertical"><?php echo $forum->get('description'); ?></textarea>
   </div>
   <input id="forum-id" type="hidden" value="<?php echo $forum->get('id'); ?>"/>
  <?php else: ?>
   <div class="form-group">
    <label>Forum Names and Descriptions</label>
    <textarea id="forums" class="form-control resize-vertical" placeholder="Enter forum names and descriptions in the form: forum1Name:forum1Description, forum2Name:forum2Description"></textarea>
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
		'action='       + 'update_forum'         +
		'&forum_id='    + $O('forum-id').value   +
		'&forum_name='  + $O('forum-name').value +
		'&forum_desc='  + $O('forum-description').value;
		<?php else: ?>
		var params = '' +
		'action='       + 'create_forums' +
		'&forums='      + $O('forums').value;
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
					if(response.message.toLowerCase() == 'forums created')
					{
						$O('forums').value = '';
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