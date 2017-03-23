<?php
if($_SERVER['REQUEST_METHOD'] == 'POST')
{  
	foreach($_POST AS $key => $value) {
		$$key = trim($value);
	}
	
	if($action == 'create_categories') {
		$categories_array = explode(',', $categories); //category1Name:category1Description, category2Name:category2Description,
		$categories_count = count($categories_array);
			
		$validate = Validator::validate(array(
			array( 'error_condition'=>!UserModel::user_is_logged_in(), 'error_message'=>'You must be logged in to perform this action', 'error_type'=>'unauthenticatedUser'),
			array( 'error_condition'=>!user_can('Manage Categories'), 'error_message'=>'You lack sufficient privilege to perform this action', 'error_type'=>'insufficientPrivilege'),
			array( 'error_condition'=>empty($categories_array[0]), 'error_message'=>'You must specify at least one category name', 'error_type'=>'noCategorySpecified')
		));
			
		if($validate['error']) {
			$return_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
		}
			
		else {
			for($i = 0; $i < $categories_count; $i++) {
				$curr_category_data  = $categories_array[$i]; //category1Name:category1Description
				$curr_category_array = explode(':', $curr_category_data);
					
				$category_name = isset($curr_category_array[0]) ? trim($curr_category_array[0]) : '';
				$category_desc = isset($curr_category_array[1]) ? trim($curr_category_array[1]) : '';
				
				if( !empty($category_name) ) {
					$category = CategoryModel::create(array(
					    'parent_id'   => $parent_id,
						'name'        => $category_name,
						'description' => $category_desc,
						'creator_id'  => UserModel::get_current_user_id()
					));
				}
			}
			
			$return_data = array('success'=>true, 'message'=>'Categories created');
		}
	}
		
	elseif($action == 'update_category') {
		$validate = Validator::validate(array(
			array( 'error_condition'=>!UserModel::user_is_logged_in(), 'error_message'=>'You must be logged in to perform this action', 'error_type'=>'unauthenticatedUser'),
			array( 'error_condition'=>!user_can('Manage Categories'), 'error_message'=>'You lack sufficient privilege to perform this action', 'error_type'=>'insufficientPrivilege'),
			array( 'error_condition'=>empty($category_id), 'error_message'=>'No Category Specified', 'error_type'=>'emptyCategoryID'),
		));
			
		if($validate['error']) {
			$return_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
		}
			
		else {
			$forums_add    = json_decode($forums_add, true);
			$forums_remove = json_decode($forums_remove, true);
			$category      = CategoryModel::get_category_instance($category_id);
			
			foreach($forums_add AS $forum) {
				if( !$category->belongs_to_forum($forum) ) {
					$category->add_to_forum($forum);
				}
			}

			foreach($forums_remove AS $forum) {
				if( $category->belongs_to_forum($forum) ) {
					$category->remove_from_forum($forum);
				}
			}
			
			if( $category->update( array('parent_id'=>$parent_id, 'name'=>$category_name, 'description'=>$category_desc) ) ) {
				$return_data = array('success'=>true, 'message'=>'Category updated successfully');
			}
			else {
				$return_data = array('error'=>true, 'message'=>'Update operation failed', 'errorType'=>'updateOperationFailureError');
			}
		}
	}
	
	create_json_string($return_data, true);
	exit;
}
?>
<?php verify_user_can('Manage Categories'); ?>

<?php
$page_title    = '';
$page_instance = AdminPage::get_instance(array());
$page_instance->load_header('', array('page_title'=>$page_title));
$page_instance->load_nav();
?>
<?php $mode = isset($_GET['category']) && !empty($_GET['category']) ? 'edit' : 'create'; ?>
<div class="container">
 <?php echo do_page_heading($page_title); ?>
 <div class="inline-block sidebar float-left"><?php include __DIR__. '/sidenav.php'; ?></div>
 <div class="inline-block main-content" style="width:55%;">
  <?php if( $mode == 'edit' ): ?>
  <?php $category = CategoryModel::get_category_instance($_GET['category']); ?>
   <div class="form-group">
    <label>Category Name</label>
    <input id="category-name" type="text" class="form-control" value="<?php echo $category->get('name'); ?>" />
   </div>
   <div class="form-group">
    <label>Category Description</label>
    <textarea id="category-description" class="form-control resize-vertical"><?php echo $category->get('description'); ?></textarea>
   </div>
   <div class="form-group" id="forums-container">
	<label>Select Forums</label>
	<?php $i = 0;  $forums = ForumModel::get_forums( $ids_only = false ); ?>	
	<?php foreach($forums AS $forum): ?>
	<?php
	 $forum_id   = $forum['id'];
	 $forum_name = $forum['name'];
		
	 $i++;
	 /*
	 * the first check in_array()comes from getting user capabilities from database
	 * the second check isset() takes care of when the form is submitted, provide sticky functionality
	 */
	 //if(in_array($cap_name, $user_capabilities) || isset($user_capabilities[$cap_name]) )
	 
	 if( $category->belongs_to_forum($forum_id) )
	 {
		$checked_value = 'on';
	 }
	 else
	 {
		//unset($checked_value);
		$checked_value = '';
	 }
	 
	 if($i % 2){ echo '<br/>'; }
	?>
	<span style="display:inline-block; width:200px; margin-right:15px;">
	 <input type="checkbox" class="forums" value="<?php echo $forum_name; ?>" style="vertical-align:top; margin-right:5px;" <?php echo set_as_checked($checked_value); ?> /><?php echo $forum_name; ?>
	</span>
	<?php endforeach; ?>
   </div>
   <input id="category-id" type="hidden" value="<?php echo $category->get('id'); ?>"/>
  <?php else: ?>
   <div class="form-group">
    <label>Category Names and Descriptions</label>
    <textarea id="categories" class="form-control resize-vertical" placeholder="Enter category names and descriptions in the form: category1Name:category1Description, category2Name:category2Description"></textarea>
   </div>
  <?php endif; ?>
  
  <div class="form-group">
   <label>Select Parent Category</label>
   <?php $categories = CategoryModel::get_categories(); ?>
   <select id="parent-category" class="form-control">
   <option value="0">Select Parent Category</option>
   <?php foreach($categories AS $curr_cat_id): ?>
   <?php $curr_cat = CategoryModel::get_category_instance($curr_cat_id); ?>
   <option 
       value="<?php echo sanitize_html_attribute( $curr_cat->get('id') ); ?>"
	   
	   <?php if( $mode == 'edit' ): ?>
	   <?php //$category instance was retrieved above in the first if($mode == 'edit') ?>
	   <?php //if current category id EQUALS/IS the parent id of the category we are currently editing ?>
	   <?php echo set_as_selected_option( $curr_cat->get('id'), $category->get('parent_id') ); ?>
	   <?php endif; ?>
   >
    <?php echo $curr_cat->get('name'); ?>
   </option>
   <?php endforeach; ?>
   </select>
  </div>
  
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
		
		var parentCategory = form.getSelectElementSelectedValue('parent-category');
		
		<?php if( $mode == 'edit' ): ?>
		
		var forums             = document.querySelectorAll('.forums');
		var forumsToAddTo      = [];
		var forumsToRemoveFrom = [];
		
		for(var i = 0; i < forums.length; i++)
		{
			if(forums[i].checked)
			{
				forumsToAddTo.push(forums[i].value);
			}
			else
			{
				forumsToRemoveFrom.push(forums[i].value);
			}
		}
		
		var params = ''    +
		'action='          + 'update_category'                 +
		'&category_id='    + $O('category-id').value           +
		'&parent_id='      + parentCategory                    + 
		'&category_name='  + $O('category-name').value         +
		'&category_desc='  + $O('category-description').value  + 
		'&forums_add='     + JSON.stringify(forumsToAddTo)     +
		'&forums_remove='  + JSON.stringify(forumsToRemoveFrom);
		
		<?php else: ?>
		
		var params = '' +
		'action='       + 'create_categories' +
		'&categories='  + $O('categories').value +
		'&parent_id='   + parentCategory;
		
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
					if(response.message.toLowerCase() == 'categories created')
					{
						$O('categories').value = '';
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