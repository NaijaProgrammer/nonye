<?php
$user_type = 'Registered';
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	foreach($_POST AS $key => $value)
	{
		$$key = is_string($value) ? trim($value) : $value;
	}
	
	if(isset($_POST['apply_filters']))
	{
		$filter_array = array();
		
		if(!empty($search_lastname))
		{
			$filter_array = array_merge( $filter_array, array('lastname'=>$search_lastname) );
		}
		
		if(!empty($search_firstname))
		{
			$filter_array = array_merge( $filter_array, array('firstname'=>$search_firstname) );
		}
		
		if(!empty($search_email))
		{
			$filter_array = array('email'=>$search_email);
		}
		
		if(!empty($search_phone))
		{
			$filter_array = array('mobile_number'=>$search_phone);
		}
		
		if(!empty($search_username))
		{
			$filter_array = array('username'=>$search_username);
		}
		
		$users_data = UserModel::get_users($filter_array);
		//var_dump($users_data);
	}
	
	else if(isset($_POST['cancel_filters']))
	{
		echo '<script type="text/javascript">location.href="'. ADMIN_URL. '/?dir=users"</script>';
	}
	
	else if( isset($_POST['action']) && $_POST['action'] == 'delete_user' )
	{   
		$user_instance = UserModel::get_user_instance($user_id);
		
		$validate = Validator::validate(array(
			array( 'error_condition'=>!UserModel::user_is_logged_in(), 'error_message'=>'You must be logged in to perform this action', 'error_type'=>'unauthenticatedUser'),
			array( 'error_condition'=>!user_can('Manage Users'), 'error_message'=>'You lack sufficient privilege to perform this action', 'error_type'=>'insufficientPrivilege'),
			array( 'error_condition'=>empty($user_id), 'error_message'=>'No user specified for deletion', 'error_type'=>'noUserSpecified'),
			array( 'error_condition'=>empty($user_instance->get('username')), 'error_message'=>'No such user exists', 'error_type'=>'noSuchUser')
		));
			
		if($validate['error'])
		{
			$return_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
		}

		else
		{
			
			$username      = $user_instance->get('username');
			$delete_status = UserModel::delete_user( $user_id, array('remove_records'=>true) ); 
			$return_data   = array('success'=>true, 'message'=>'User '. $username. ' \'s account has been deleted');
		}
		
		create_json_string($return_data, true);
		exit;
	}
}
else
{
	$users_type = isset($_GET['role']) ? trim(urldecode($_GET['role'])) : '';
	//$users       = UserModel::get_users( array('role'=>$role) );
	//$query_str   = UserModel::get_users_query_string( array('role'=>$role) );

	switch(strtolower($users_type))
	{
		case 'user'        : $user_type = 'Front End';    break;
		case 'admin'       : $user_type = 'Admin';        break;
		case 'super admin' : $user_type = 'Super Admin';  break;
		default            : $user_type = 'Registered';   break;
	}
	
	if( !empty($users_type) && in_array($users_type, array('user', 'admin', 'super admin')) )
	{
		$users_data = UserModel::get_users( array('role'=>$users_type) );  
		$query_str  = UserModel::get_users_query_string( array('role'=>$users_type) );
	}
	else
	{
		$users_data = UserModel::get_users();
		$query_str  = UserModel::get_users_query_string();
	}
}

if( isset($_GET['msg']) )
{
	$status_message = Util::unstringify($_GET['msg']);
}
?>
<?php verify_user_can('Manage Users'); ?>
<?php
$page_instance = AdminPage::get_instance(array());
$page_instance->load_header('', array('page_title'=>'Registered users'));
$page_instance->load_nav();
?>
<style>
.single-action-section
{
	border-bottom:1px solid #aaa;
	//border-radius:5px;
	margin-bottom:10px;
	padding-left:10px;
	padding-right:10px;
	padding-bottom:10px;
}
.single-action-section legend 
{
	width:auto;
	margin-bottom:10px;
	border-bottom:none;
}
.single-action-section .form-control { margin-bottom:10px; }

.sidebar { position:relative; right:10px; }
.inline-block { margin-right: 0; }
</style>
<div class="container">
 <?php echo do_page_heading($user_type. ' Users'); ?>

 <div class="inline-block sidebar float-left">
  <fieldset class="single-action-section"><?php include __DIR__. '/sidenav.php'; ?></fieldset>	
  <fieldset class="search-container single-action-section" style="margin-bottom:5px;">
   <legend>Search</legend>
   <form method="post" action="<?php echo ADMIN_URL; ?>/?dir=users" class="form-horizontal">
    <input type="text" class="form-control inline-block" name="search_lastname"    value="<?php //echo $search_lastname; ?>"    placeholder="Lastname" title="Filter users based on lastname" style="width:170px;">
    <input type="text" class="form-control inline-block" name="search_firstname" value="<?php //echo $search_firstname; ?>" placeholder="Firstname" title="Filter users based on firstname" style="width:170px;">
    <input type="text" class="form-control inline-block" name="search_email"      value="<?php //echo $search_email; ?>"      placeholder="Email" title="Search for user with given email" style="width:170px;">
    <input type="text" class="form-control inline-block" name="search_username"   value="<?php //echo $search_username; ?>"   placeholder="Username" title="Search for user with given username" style="width:170px;">
    <div style="margin-bottom:5px;"></div>
    <input type="submit" name="cancel_filters" class="form-control inline-block btn custom-action-buttons" value="Clear" style="width:83px; color:#222;">
    <input type="submit" name="apply_filters" class="form-control inline-block pull-right btn custom-action-buttons" value="Search" style="width:80px; color:#222; margin-right:5px;">
    <div class="clearfix">&nbsp;</div>
   </form>
  </fieldset>
  
  <?php if(is_super_admin()): ?>
  <fieldset class="single-action-section">
   <legend>Export</legend>
   <form action="<?php echo ADMIN_URL; ?>/tools/export-users.php">
    <select name="export-format" class="form-control inline-block" title="Select Export Type">
     <option value="csv">Select Export Format <span style="font-size:10px;">(default is CSV)</span></option>
     <option value="csv">CSV File</option>
     <option value="xls">Excel Spreadsheet</option>
    </select>
    <input type="submit" class="form-control inline-block pull-right btn custom-action-buttons" value="Export" style="width:100px; color:#222; margin-right:0px;">
    <div class="clearfix">&nbsp;</div>
   </form>
  </fieldset>
  <div style="clear:both; margin-bottom:5px;"></div>
  <?php endif; ?>
 </div>
 <div class="inline-block float-left main-content" style="width:78%;">
  <?php echo isset($status_message) ? '<p class="text-centered">'. $status_message. '</p>' : ''; ?>
  <table class="table table-bordered table-hover table-responsive">
   <thead>
    <tr>
     <th class="text-centered">S/No.</th>
     <th class="text-centered">Firstname</th>
     <th class="text-centered">Lastname</th>
     <th class="text-centered">Email</th>
     <th class="text-centered">Username</th>
	 <th class="text-centered">Role</th>
     <th class="text-centered">Actions</th>
    </tr>
   </thead>
   <tfoot>
    <tr>
     <th class="text-centered">S/No.</th>
     <th class="text-centered">Firstname</th>
     <th class="text-centered">Lastname</th>
     <th class="text-centered">Email</th>
     <th class="text-centered">Username</th>
	 <th class="text-centered">Role</th>
     <th class="text-centered">Actions</th>
    </tr>
   </tfoot>
   <tbody>
    <?php $serial_counter = 1; ?>
    <?php foreach($users_data AS $user_data): ?>
    <?php $row_class = ( ($serial_counter % 2) ? 'odd-row' : 'even-row' ); ?>
    <tr class="<?php echo $row_class; ?>">
     <td class="text-centered"><?php echo $serial_counter; ?></td>
     <td class="text-centered"><?php echo isset($user_data['firstname']) ? $user_data['firstname'] : 'Not specified'; ?></td>
     <td class="text-centered"><?php echo isset($user_data['lastname'])  ? $user_data['lastname']  : 'Not specified'; ?></td>
     <td class="text-centered"><?php echo isset($user_data['email'])     ? $user_data['email']     : 'Not specified'; ?></td>
     <td class="text-centered"><?php echo isset($user_data['username'])  ? $user_data['username']  : 'Not specified'; ?></td>
	 <td class="text-centered"><?php echo isset($user_data['role'])      ? $user_data['role']      : 'Not specified'; ?></td>
     <td class="text-centered">
      <ul class="list-inline">
	   <li>
	    <form>
	     <select name="action" class="form-control inline-block bg-no-repeat" style="width:180px; background-position:140px 8px;"
			onchange="applyAction(this, '<?php echo $user_data['id']; ?>', '<?php echo isset($user_data['username'])  ? $user_data['username'] : 'user'; ?>')">
	      <option value="">Select Action</option>
          <option value="edit">Edit</option>
          <option value="delete">Delete</option>
         </select>
	    </form>
	   </li>
	  </ul>
     </td>
    </tr>
    <?php ++$serial_counter; ?>
    <?php endforeach; ?>
   </tbody>
  </table>
 </div>
</div>
<script>
function applyAction(eventTarget, userID, userName)
{
	var action = form.getSelectElementSelectedValue(eventTarget).toLowerCase();

	switch(action)
	{
		case "edit"     : redirectTo('edit');   break;
        case "delete"   : if( confirm('Are you sure you want to delete ' + userName + '\'s account?') ) { deleteUser(userID, userName, eventTarget); } 
						  else { eventTarget.form.reset(); }
						  break;
	}
	
	function redirectTo(page)
	{  
		location.href= '<?php echo ADMIN_URL; ?>/?dir=users&page=' + page + '&user=' + userID;
	}
	
	function deleteUser(userID, userName, evtTarget)
	{
		disable(evtTarget);
		showProcessing(evtTarget);
		
		Site.Util.runAjax({
			'requestMethod'      : 'POST',
			'requestURL'         : '',
			'timeoutAfter'       : 30,
			'requestData'        : 'action=delete_user&user_id=' + userID,
			'debugCallback'      : function(reply){ console.log(reply); },
			'readyStateCallback' : function(){},
			'errorCallback'      : function(xhrObject, aborted)
			{
				if(aborted)
				{
					alert('Sorry. This request timed out. Please try again');
					hideProcessing(evtTarget);
					enable(evtTarget);
				}
			},
			'successCallback' : function(reply)
			{
				var response = Site.Util.parseAjaxResponse(reply.rawValue);
				if(response.error)
				{
					alert(response.message);
					switch(response.errorType)
					{
						case 'unauthenticatedUserError'   : setTimeout( function(){ location.reload(); }, 5000 ); break;
						case 'insufficientPrivilegeError' : setTimeout( function(){ location.href='<?php echo ADMIN_URL; ?>'; }, 5000 ); break;
						default : '';
					}
				}
				else if(response.success)
				{
					alert(response.message);
					//location.reload();
				}
				
				hideProcessing(evtTarget);
				enable(evtTarget);
			}
		});
	}
}
</script>
<?php $page_instance->load_footer('', array()); ?>