<?php

/*
* @dependencies : ArrayManipulator
*/
class DateManipulator
{
	public static function get_fancy_date($datetime, $format = "F d, Y")
	{
		$package_date_array = explode(' ', $datetime);
		$package_date       = $package_date_array[0];
		$package_time       = $package_date_array[1];
		
		$arr_date_array = explode('-', $package_date);
		$year           = $arr_date_array[0];
		$month          = $arr_date_array[1];
		$day            = $arr_date_array[2];
		
		$date_arr = array( 'day'=>$day, 'month'=>$month, 'year'=>$year );
		return self::format_date($date_arr, $format);
	}
	
	public static function datetime_to_timestamp($datetime, $datetime_separator = ' ', $date_separator = '-', $time_separator = ':')
	{
		$datetime_array = explode($datetime_separator, $datetime);  // [0] = date, [1] = time
		$date_array     = explode($date_separator, $datetime_array[0]); // [0] = yr,   [1] = month, [2] = day
		$time_array     = explode($time_separator, $datetime_array[1]); // [0] = hr,   [1] = min, [2] = sec

		$yr  = $date_array[0];
		$mth = $date_array[1];
		$day = $date_array[2];
		$hr  = $time_array[0];
		$min = $time_array[1];
		$sec = $time_array[2];
		$t2s = mktime($hr, $min, $sec, date($mth), date($day), date($yr));

   		return $t2s;
	}

	public static function format_date($date_arr, $format = "F d, Y")
	{
		$default_arr = array('day'=>date('d'), 'month'=>date('m'), 'year'=>date('Y'), 'hour'=>0, 'minute'=>0, 'second'=>0);
		ArrayManipulator::copy_array($default_arr, $date_arr);
	
		$day   = $date_arr['day'];
		$month = $date_arr['month'];
		$year  = $date_arr['year'];
		$hr    = $date_arr['hour'];
		$m     = $date_arr['minute'];
		$s     = $date_arr['second'];
  		return date($format, mktime($hr,$m,$s, $month, $day, $year)); 
	}
	
	public static function month_int_to_month_str($month, $short = false){
	
		switch(intval($month)) {
			case 1:  return $short ? "Jan" : "January"; 
			case 2:  return $short ? "Feb" : "February";
			case 3:  return $short ? "Mar" : "March";   
			case 4:  return $short ? "Apr" : "April";
			case 5:  return $short ? "May" : "May";
			case 6:  return $short ? "Jun" : "June";
			case 7:  return $short ? "Jul" : "July";
			case 8:  return $short ? "Aug" : "August";
			case 9:  return $short ? "Sep" : "September";
			case 10: return $short ? "Oct" : "October";
			case 11: return $short ? "Nov" : "November";
			case 12: return $short ? "Dec" : "December";
		}
	}

	public static function date_difference($str_start, $str_end) 
	{ 
 		$str_start = strtotime($str_start);
 		$str_end   = strtotime($str_end);
 		$nseconds  = $str_end - $str_start;
 		$ndays     = floor($nseconds / 86400);
 		$nseconds  = $nseconds % 86400;
	 	$nhours    = floor($nseconds / 3600);
 		$nseconds  = $nseconds % 3600;
 		$nminutes  = floor($nseconds / 60);
   		$nseconds  = $nseconds % 60;
 		$retString = "";
   		if ($ndays > 0) 
		{
    		$retString .= " ". $ndays.   ( ($ndays > 1) ? " days" : " day" );
   		}
   		if ($nhours > 0) 
		{
    		$retString .= " ". $nhours.  ( ($nhours > 1) ? " hours" : " hour" );
   		}
   		if ($nminutes > 0) 
		{
    		$retString .= " ". $nminutes. ( ($nminutes > 1) ? " minutes" : " minute" );
   		}
		if (strcmp($retString, "") == 0)
		{
    		$retString = $nseconds; //"less than 1 minute";
   		}
		return $retString;
	}
	
	
	/**
	* Generate a more user friendly time
	* @param int $time - timestamp
	* @return String - friendly time
	*/
	private static function generate_friendly_time( $time )
	{
		$current_time = time();
		if( $current_time < ( $time + 60 ) )
		{
			// the update was in the past minute
			return "less than a minute ago";
		}
		else if( $current_time < ( $time + 120 ) )
		{
			// it was less than 2 minutes ago, more than 1, but we don't wantto say 1 minute ago do we?
			return "just over a minute ago";
		}
		else if( $current_time < ( $time + ( 60*60 ) ) )
		{
			// it was less than 60 minutes ago: so say X minutes ago
			return round( ( $current_time - $time ) / 60 ) . " minutes ago";
		}
		else if( $current_time < ( $time + ( 60*120 ) ) )
		{
			// it was more than 1 hour ago, but less than two, again we dont want to say 1 hourS do we?
			return "just over an hour ago";
		}
		else if( $current_time < ( $time + ( 60*60*24 ) ) )
		{
			// it was in the last day: X hours
			return round( ( $current_time - $time ) / (60*60) ) . " hours ago";
		}
		else
		{
			// longer than a day ago: give up, and display the date / time
			return "at " . date( 'h:ia \o\n l \t\h\e jS \o\f M',$time);
		}
	}

	//public static function compute_age($birthyear, $birthmonth, $birthday)
	public static function compute_age($birthday, $birthmonth, $birthyear)
	{
   		$birthday   = ( ($birthday < 10)   ? "0". $birthday   : $birthday );
   		$birthmonth = ( ($birthmonth < 10) ? "0". $birthmonth : $birthmonth );
		return self::age($birthyear. "-". $birthmonth. "-". $birthday); 
	}
	
	public static function get_age($birthday, $birthmonth, $birthyear){ return self::compute_age($birthday, $birthmonth, $birthyear); }

	//calculate years of age (input string: YYYY-MM-DD)
  	protected static function age($age)
	{
    	list($year,$month,$day) = explode("-",$age);

    	$year_diff  = date("Y") - $year;
    	$month_diff = date("m") - $month;
    	$day_diff   = date("d") - $day;

    	if ($day_diff < 0 || $month_diff < 0)
		{
      		$year_diff--;
		}

    	return $year_diff;
  	}	
}