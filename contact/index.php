<?php
require dirname(__DIR__). '/config.php';

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	foreach($_POST AS $key => $value)
	{
		$$key = is_string($value) ? trim($value) : $value;
	}
	
	$validate = Validator::validate(array(
		array('error_condition'=>empty($name), 'error_message'=>'Please specify your name'),
		array('error_condition'=>empty($email), 'error_message'=>'Please specify your email'),
		array('error_condition'=>empty($subject), 'error_message'=>'Please enter your message subject'),
		array('error_condition'=>empty($message), 'error_message'=>'Please enter your message'),
	));
	
	if($validate['error'])
	{
		$return_data = array('error'=>true, 'message'=>$validate['status_message']);
	}
	
	else
	{
		ItemModel::add_item( array('category'=>'contact-us-messages', 'sender'=>$name, 'email'=>$email, 'subject'=>$subject, 'message'=>$message) );
		UserModel::send_email(array(
			'to'      => 'orji4y@yahoo.com',
			'from'    => $name. ' <'. $email. '>',
			'subject' => $subject,
			'message' => $message
		));
		
		$return_data = array('success'=>true, 'message'=>'Your message has been received. Thank you for reaching out to us.');
	}
	
	create_json_string($return_data, true);
	exit;
}