<?php
if($_SERVER['REQUEST_METHOD'] == 'POST')
{  
	foreach($_POST AS $key => $value)
	{
		$$key = trim($value);
	}
	
	if($action == 'create_categories')
	{
		$categories_array = explode(',', $categories); //category1Name:category1Description, category2Name:category2Description,
		$categories_count = count($categories_array);
			
		$validate = Validator::validate(array(
			array( 'error_condition'=>!UserModel::user_is_logged_in(), 'error_message'=>'You must be logged in to perform this action', 'error_type'=>'unauthenticatedUser'),
			array( 'error_condition'=>!user_can('Manage Categories'), 'error_message'=>'You lack sufficient privilege to perform this action', 'error_type'=>'insufficientPrivilege'),
			array( 'error_condition'=>empty($categories_array[0]), 'error_message'=>'You must specify at least one category name', 'error_type'=>'noCategorySpecified')
		));
			
		if($validate['error'])
		{
			$return_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
		}
			
		else
		{
			for($i  = 0; $i < $categories_count; $i++)
			{
				$curr_category_data  = $categories_array[$i]; //category1Name:category1Description
				$curr_category_array = explode(':', $curr_category_data);
					
				$category_name = isset($curr_category_array[0]) ? trim($curr_category_array[0]) : '';
				$category_desc = isset($curr_category_array[1]) ? trim($curr_category_array[1]) : '';
				
				if( !empty($category_name) )
				{
					$category_id = CategoryModel::create_category(array(
						'name'          => get_slug(strtolower($category_name)),
						'description'   => $category_desc,
						'creator_id'    => UserModel::get_current_user_id()
					));
				}
			}
			
			$return_data = array('success'=>true, 'message'=>'Categories created');
		}
	}
		
	elseif($action == 'update_category')
	{
		$category_data     = CategoryModel::get_category_data($category_id);
		$category_category = isset($category_data['category']) ? $category_data['category'] : '';
		$validate     = Validator::validate(array(
			array( 'error_condition'=>!UserModel::user_is_logged_in(), 'error_message'=>'You must be logged in to perform this action', 'error_type'=>'unauthenticatedUser'),
			array( 'error_condition'=>!user_can('Manage Categories'), 'error_message'=>'You lack sufficient privilege to perform this action', 'error_type'=>'insufficientPrivilege'),
			array( 'error_condition'=>empty($category_category),      'error_message'=>'Invalid Category ID', 'error_type'=>'wrongID'),
			array( 'error_condition'=>$category_category != 'post-categories', 'error_message'=>'Invalid Category ID', 'error_type'=>'falseCategory'),
			array( 'error_condition'=>empty($category_name), 'error_message'=>'The category name cannot be empty', 'error_type'=>'emptyCategoryName'),
		));
			
		if($validate['error'])
		{
			$return_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
		}
			
		else
		{
			CategoryModel::update_category_data($category_id, 'name', $category_name);
			CategoryModel::update_category_data($category_id, 'description', $category_desc);
			$return_data = array('success'=>true, 'message'=>'Category updated successfully');
		}
	}
	
	create_json_string($return_data, true);
	exit;
}
?>
<?php verify_user_can('Manage Categories'); ?>
<?php if(isset($_GET['category'])): $category_data = CategoryModel::get_category_data($_GET['category']);  endif; ?>
<?php $page_title = ( !empty($category_data['category']) && ($category_data['category'] == 'post-categories') ) ? 'Edit '. $category_data['name']. ' category' : 'Create new category'; ?>
<?php
$page_instance = AdminPage::get_instance(array());
$page_instance->load_header('', array('page_title'=>$page_title));
$page_instance->load_nav();
?>
<div class="container">
 <?php echo do_page_heading($page_title); ?>
 <div class="inline-block sidebar float-left"><?php include __DIR__. '/sidenav.php'; ?></div>
 <div class="inline-block main-content" style="width:55%;">
  <?php if( !empty($category_data['category']) && ($category_data['category'] == 'post-categories') ): ?>
   <div class="form-group">
    <label>Category Name</label>
    <input id="category-name" type="text" class="form-control" value="<?php echo $category_data['name']; ?>" />
   </div>
   <div class="form-group">
    <label>Category Description</label>
    <textarea id="category-description" class="form-control resize-vertical"><?php echo isset($category_data['description']) ? $category_data['description'] : ''; ?></textarea>
   </div>
   <input id="category-id" type="hidden" value="<?php echo $category_data['id']; ?>"/>
  <?php else: ?>
   <div class="form-group">
    <label>Category Names and Descriptions</label>
    <textarea id="categories" class="form-control resize-vertical" placeholder="Enter category names and descriptions in the form: category1Name:category1Description, category2Name:category2Description"></textarea>
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
		
		<?php if( !empty($category_data['category']) && ($category_data['category'] == 'post-categories') ): ?>
		var params = ''   +
		'action='         + 'update_category'         +
		'&category_id='   + $O('category-id').value   +
		'&category_name=' + $O('category-name').value +
		'&category_desc=' + $O('category-description').value;
		<?php else: ?>
		var params = '' +
		'action='       + 'create_categories' +
		'&categories='        + $O('categories').value;
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