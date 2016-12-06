<?php
function get_external_app_data($app_name = '')
{
	$app_keys = array(
		'embedly'        => array('key'=>'d3b0372a109c4a47817821f61ddb8d73', 'secret'=>''),
		'facebook'       => array('key'=>'1100758689958372', 'secret'=>'8671767fb664b3a659b6bdbd8727283e', 'permissions'=>['email', 'public_profile']),
		'google'         => array('key'=>'7215268348-nmt8h2s9v6m0rpiacpulgcj1n1h0bqvg.apps.googleusercontent.com', 'secret'=>'Ew7xnwrpZVN9TE3PCSx_WRfG', 'permissions'=>''),
		'linkedin-share' => array('key'=>'774x9dguwvb7wr', 'secret'=>'WanUol3YmlRVB2Ez'),
		'linkedin-login' => array('key'=>'7768tor21b8d7p', 'secret'=>'BbpS2CrdxzENrN79'),
		'twitter'        => array(
							'key'=>'vM5jlUrLgy7vy0fSMPsgMhYti', 
							'secret'=>'pPx4wI3vJeezMpVv6NX0bi0LKofhhy5XzyYNp0aP9zwvDp5nvh', 
							'access_token'=>'905589421-jne2XEw8zX3KkBi9HTjs0m8qR5Gxee81yf1hSQTN',
							'access_token_secret'=>'4maLr9XCkbx8wuOX7TNWMBzcstw7pVJqk4XwjCHdSVaHe'
							)
	);
	
	return ( empty($app_name) ? $app_keys : $app_keys[$app_name] );
}
