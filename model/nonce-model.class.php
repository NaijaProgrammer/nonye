<?php

require( rtrim(SITE_DIR, '/'). '/lib/nonceutil/NonceUtil.php');

class NonceModel extends BaseModel
{
	private static $nonce_secret = 'jvTGophIQ108Pqw9Hej';
	
	public static function generate_nonce()
	{
		$nonce = NonceUtil::generate( self::_get_nonce_secret(), 60 * 60 * 24 );
		return $nonce;
	}
	
	public static function check_nonce($nonce)
	{
		$r = NonceUtil::check( self::_get_nonce_secret(), $nonce );
		return $r;
	}
	
	private static function _get_nonce_secret()
	{
		return self::$nonce_secret;
	}
}