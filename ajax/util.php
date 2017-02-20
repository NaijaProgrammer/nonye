<?php
include('request-validator.php');
$response_data = array();

if(isset($_POST['subscribe-to-newsletter'])) {
	
	$subscriber_email = !empty($_POST['subscriber-email']) ? trim($_POST['subscriber-email']) : '';
	
	$validate = Validator::validate(array(
		array('error_condition'=>!is_valid_email($subscriber_email), 'error_message'=>'Please enter your email', 'error_type'=>'emptyEmailField'),
	));
	
	if($validate['error']) {
		$response_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
	}
	else {
		SubscriberModel::subscribe_user( array('firstname'=>'', 'lastname'=>'', 'email'=>$subscriber_email) );
		$response_data = array('success'=>true);
	}
}
else if(isset($_POST['contact-us'])) {
	
	foreach($_POST AS $key => $value) {
		$$key = is_string($value) ? trim($value) : $value;
	}
	
	$validate = Validator::validate(array(
		array('error_condition'=>empty($name), 'error_message'=>'Please specify your name'),
		array('error_condition'=>empty($email), 'error_message'=>'Please specify your email'),
		array('error_condition'=>empty($subject), 'error_message'=>'Please enter your message subject'),
		array('error_condition'=>empty($message), 'error_message'=>'Please enter your message'),
	));
	
	if($validate['error']) {
		$response_data = array('error'=>true, 'message'=>$validate['status_message']);
	}
	
	else {
		ItemModel::add_item( array('category'=>'contact-us-messages', 'sender'=>$name, 'email'=>$email, 'subject'=>$subject, 'message'=>$message) );
		@send_email(array(
			'to'      => 'orji4y@yahoo.com',
			'from'    => $name. ' <'. $email. '>',
			'subject' => $subject,
			'message' => $message
		));
		
		$response_data = array('success'=>true, 'message'=>'Your message has been received. Thank you for reaching out to us.');
	}
}
else if( isset($_POST['track-share']) ) {
	$item_type      = isset($_POST['item-type']) ? trim($_POST['item-type']) : 'article';
	$item_type      = !empty($item_type) ? $item_type : 'article';
	$item_id        = $_POST['item-id'];
	$share_provider = $_POST['provider'];
	$sharer_id      = $user_is_logged_in ? $current_user_id : 0;
	
	$validate = Validator::validate(array(
		array('error_condition'=>empty($item_type), 'error_message'=>'Please specify the type of item to share', 'error_type'=>'noItemType Specified'),
		array('error_condition'=>empty($item_id), 'error_message'=>'Please specify the item to share', 'error_type'=>'noItemSpecified'),
		array('error_condition'=>empty($share_provider), 'error_message'=>'Please select a share provider', 'error_type'=>'noShareProviderSpecified'),
	));
	
	if($validate['error']) {
		$response_data = array('error'=>true, 'message'=>$validate['status_message'], 'errorType'=>$validate['error_type']. 'Error');
	}
	
	else {
		$share_id = ItemModel::add_item(array(
			'category'  => 'social-share',
			'item-type' => $item_type,
			'item-id'   => $item_id,
			'provider'  => $share_provider,
			'sharer-id' => $sharer_id
		));
		
		if( $share_id ) {
		    $response_data = array('success'=>true, 'shareID'=>$share_id);
		}
		else {
			$response_data = array('error'=>true, 'message'=>'An unexpected error occurred', 'errorType'=>'systemError');
		}
	}
}
echo json_encode($response_data, true);
exit;