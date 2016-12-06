<?php

class System
{
	public static function is_windows()
	{
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') 
		{
    		return true;
		} 
		return false;
	}
}