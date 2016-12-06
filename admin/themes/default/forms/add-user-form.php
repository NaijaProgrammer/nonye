<?php echo isset($status_message) ? '<p class="text-centered" id="status-message-container">'. $status_message. '</p>' : ''; ?>
 <!--<form method="post" action="" class="form-horizontal">-->
 <form method="post" action="" class="form-vertical">
	 
  <div class="form-group"><input type="email" name="email" value="<?php echo $email; ?>" placeholder="User Email" class="form-control" /></div>
  <div class="form-group"><input type="text" name="surname" value="<?php echo $surname; ?>" placeholder="User Surname" class="form-control" /></div>
  <div class="form-group"><input type="text" name="othernames" value="<?php echo $othernames; ?>" placeholder="User Othernames" class="form-control" /></div>
  <div class="form-group">
   <select name="user_role" id="user-role-selector" class="form-control">
    <option value="">-- Select User Role --</option>
	<?php echo ItemModel::get_items_as_dropdown_menu_options( array( 'select_element_name'=>'user_role', 'conditions'=>array('category'=>'user_roles'), 'order_by'=>array() ) ); ?>
   </select>
  </div>
  
  <!--
  <div class="form-group">
   <select name="user_capability" class="form-control">
    <option value="">-- Select User Capability --</option>
	<?php echo ItemModel::get_items_as_dropdown_menu_options( array( 'select_element_name'=>'user_capability', 'conditions'=>array('category'=>'user_capabilities'), 'order_by'=>array('name'=>'ASC') ) ); ?>
   </select>
  </div>
  -->
  <div class="form-group" id="user-capabilities-container">
	<label>Specify User Capabilities</label>
	<?php $i= 0; $avail_capabilities = ItemModel::get_items_by_category('user_capabilities', array('name'=>'ASC')); ?>	
	<?php foreach($avail_capabilities AS $avail_capability): ?>
	<?php
		$i++;
		$cap_id = $avail_capability['id'];
		$cap_name = $avail_capability['name'];
		
		/*
		* the first check in_array()comes from getting user capabilities from database
		* the second check isset() takes care of when the form is submitted, provide sticky functionality
		*/
		if(in_array($cap_name, $user_capabilities) || isset($user_capabilities[$cap_name]) )
		{
			$checked_value = 'on';
		}
		else
		{
			unset($checked_value);
		}
		
		if($i % 2){ echo '<br/>'; }
	?>
	<span style="display:inline-block; width:200px; margin-right:15px;">
	 <input style="vertical-align:top; margin-right:5px;" type="checkbox" value="<?php echo $cap_name; ?>" name="user_capabilities[<?php echo $cap_name; ?>]" <?php echo set_as_checked($checked_value); ?> /><?php echo $cap_name; ?>
	</span>
	<?php endforeach; ?>
  </div>
  
  <div class="pull-right"><input type="submit" value="Add User" class="btn btn-primary" /></div>
 
 </form>
 
 <script type="text/javascript" src="<?php echo SITE_URL; ?>/code-libraries/JSLib/JSLib.js"></script>
 <script type="text/javascript" src="<?php echo SITE_URL; ?>/code-libraries/JSLib/globalLibrary.js"></script>
 <script type="text/javascript" src="<?php echo SITE_URL; ?>/code-libraries/JSLib/animator.js"></script>
 <script type="text/javascript" src="<?php echo SITE_URL; ?>/code-libraries/JSLib/slider.js"></script>
 <script type="text/javascript" src="<?php echo SITE_URL; ?>/code-libraries/JSLib/form.js"></script>
 
 <script type="text/javascript">
	var selOptions = $Tag.call($O('user-role-selector'), 'option');
	for(i=0; i < selOptions.length; i++)
	{
		if(selOptions[i].text.toLowerCase() == 'site user')
		{
			selOptions[i].text = 'Travelsmart User';
			break;
		}
	}
 </script>
 
 <script type="text/javascript">
	var userCapabilitiesVisible = true;
	EventManager.attachEventListener('user-role-selector', 'change', function(){
		
			if(form.getSelectElementSelectedText('user-role-selector').toLowerCase()=='travelsmart user')
			{
				slideUp('user-capabilities-container');
				userCapabilitiesVisible = false;
				
			}
			else
			{
				if(!userCapabilitiesVisible)
				{
					slideDown('user-capabilities-container');
					userCapabilitiesVisible = true;
				}
			}
		}, false);
 </script>