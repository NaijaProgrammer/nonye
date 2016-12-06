<?php
/*
* @dependencies: ArrayManipulator, DateManipulator
*/
class HtmlElement
{

	/**
	* @params: array
	* @array_members:
	* 1. options [array of the options array]
	* 2. default_selected_text [string]
	* 3. default_selected_value [string]
	* 4. use_only_values [boolean] whether or not to use the options' array's values as both the select element's index(value) and display text(true) 
          OR
		 only as the select element's display text while using the array's keys as the select element's index(value)(false)
	* 5. convert_keyed_value_to_text [boolean] whether or not to convert the array's keys to the array's values and use these values as the display texts    of  the select element, while still using the array's keys as the select element's index(value); This applies in cases where the array's values are numeric and enables us to specify the '$use_only_values' option as true, and still be able to use these values as the select element options's indexes while having a meaningful text (obtained by a user defined conversion function) as the display text, for e.g in birthday requiring numeric values as the index of the month and text values as the display text as in the 'birthday_builder.php' script
	* 6. conversion_function [string] used in conjunction with $convert_keyed_value_to_text; a pointer to, or a string representing, the name of the function to use  in converting an array's numeric values to a printable representation/format
	**/
	public static function build_select_options( $supplied_options = array() )
	{
		$default_options = array(
									'options'                     => array(),
									'default_selected_text'       => '', 
									'default_selected_value'      => '', 
									'use_only_values'             => true,
									'convert_keyed_value_to_text' => false,
									'conversion_function'         => ''
								);
								
		$setup_options = ArrayManipulator::copy_array($default_options, $supplied_options);
		
		$options_array           = $setup_options['options'];
		$default_selected_text   = $setup_options['default_selected_text'];
		$default_selected_value  = $setup_options['default_selected_value'];
		$use_values_only         = $setup_options['use_only_values']; 
		$convert_keyedValue2text = $setup_options['convert_keyed_value_to_text'];
		$conversion_function     = $setup_options['conversion_function'];
		
		$options  = '';
		$options .= '<option value="'. $default_selected_value. '" selected="selected">'. $default_selected_text. '</option>';

		$options_array_len = count($options_array);
   
		if($options_array_len > 0)
		{
			foreach($options_array as $key => $value)
			{
				if($use_values_only == true)
				{
					if($convert_keyedValue2text == true)
					{
						$options.= '<option value="'. $value. '">'. $conversion_function($value). '</option>';       
					}
       
					else
					{
						$options.= '<option value="'. $value. '">'. $value. '</option>';
					}       
				}

				else if($use_values_only == false)
				{
					if(is_numeric($key) && array_key_exists(0, $options_array))
					{
						$use_key = $key+1;
					}
         
					else
					{
						$use_key = $key;
					}

					$options.= '<option value="'. $use_key. '">'. $value. '</option>';
				}
			}
		}
		
		return $options;
	}

	public static function build_select($params = array(), $options_array = array())
	{
		$select_name  = $params['select_name'];
		$select_id    = $params['select_id'];
		$label_for    = $params['label_for'];
		$label_text   = $params['label_text'];
		$select_class = $params['select_class'];
		$options      = isset($params['options']) ? $params['options'] : ( !empty($options_array) ? self::build_select_options($options_array) : '' );
		$select       = "";

		if(!empty($label_for) && !empty($label_text))
		{
			$select.= '<label for="'. $label_for. '">'. $label_text. '</label>';
		}
    
		$select.= '<select name="'. $select_name. '" id="'. $select_id. '" class="'. $select_class. '">';
		$select.= $options;

		$select.= '</select>';

		return $select;
	}
	
	public static function build_day_options($opts = array())
	{
		$selected_day          = isset($opts['selected_day'])          ? $opts['selected_day'] :  -1;
		$opening_selected_text = isset($opts['opening_selected_text']) ? $opts['opening_selected_text'] : 'Day';
	
		$bd_params['options']                     = range(1, 31);
		$bd_params['use_only_values']             = true;
		$bd_params['default_selected_text']       = $selected_day;
		$bd_params['default_selected_value']      = $selected_day;
		$bd_params['convert_keyed_value_to_text'] = false;
		$bd_params['conversion_function']         = '';
		
		if(intval($selected_day) <= 0)
		{
			$bd_params['default_selected_text']  = $opening_selected_text;
			$bd_params['default_selected_value'] = $selected_day;
		}
		return self::build_select_options($bd_params);
	}
	
	public static function build_month_options($opts = array())
	{
		if(!function_exists('month_int_to_month_str'))
		{
			function month_int_to_month_str($month)
			{
				return DateManipulator::month_int_to_month_str($month);
			}
		}
		
		$selected_month        = isset($opts['selected_month'])        ? $opts['selected_month'] :  -1;
		$opening_selected_text = isset($opts['opening_selected_text']) ? $opts['opening_selected_text'] : 'Month';
		
		$bm_params['options']                     = range(1, 12);
		$bm_params['use_only_values']             = true;
		$bm_params['convert_keyed_value_to_text'] = true;
		$bm_params['conversion_function']         = "month_int_to_month_str";
		$bm_params['default_selected_text']       = $bm_params['conversion_function']($selected_month);
		$bm_params['default_selected_value']      = $selected_month;

		if(intval($selected_month) <= 0)
		{
			$bm_params['default_selected_text']  = $opening_selected_text;
			$bm_params['default_selected_value'] = $selected_month;
		}
		
		return self::build_select_options($bm_params);
	}
	
	public static function build_year_options($opts = array())
	{
		$selected_year         = isset($opts['selected_year'])         ? $opts['selected_year'] :  -1;
		$opening_selected_text = isset($opts['opening_selected_text']) ? $opts['opening_selected_text'] : 'Year';
		
		$by_params['options']                     = range(date("Y"), 1900);
		$by_params['use_only_values']             = true;
		$by_params['default_selected_text']       = $selected_year;
		$by_params['default_selected_value']      = $selected_year;
		$bd_params['convert_keyed_value_to_text'] = false;
		$bd_params['conversion_function']         = '';

		if(intval($selected_year) <= 0)
		{
			$by_params['default_selected_text']  = $opening_selected_text;
			$by_params['default_selected_value'] = $selected_year;
		}
    
		return self::build_select_options($by_params);
   }
   
	public static function build_hour_options($opts = array())
	{
		$selected_hour         = isset($opts['selected_hour'])         ? $opts['selected_hour'] :  -1;
		$opening_selected_text = isset($opts['opening_selected_text']) ? $opts['opening_selected_text'] : 'Hour';
	
		$bm_params['options']                     = range(1, 12);
		$bm_params['use_only_values']             = true;
		$bm_params['convert_keyed_value_to_text'] = false;
		$bm_params['conversion_function']         = "";
		$bm_params['default_selected_text']       = $selected_hour;
		$bm_params['default_selected_value']      = $selected_hour;

		if(intval($selected_hour) <= 0)
		{
			$bm_params['default_selected_text']  = $opening_selected_text;
			$bm_params['default_selected_value'] = $selected_hour;
		}
		
		return self::build_select_options($bm_params);
	
	}
	
	public static function build_minute_options($opts = array())
	{
		if(!function_exists('add_zero_to_int_less_than_ten'))
		{
			function add_zero_to_int_less_than_ten($num)
			{
				return $num < 10 ? "0". $num : $num;
			}
		}
		
		$selected_minute       = isset($opts['selected_minute'])       ? $opts['selected_minute'] :  -1;
		$opening_selected_text = isset($opts['opening_selected_text']) ? $opts['opening_selected_text'] : 'Minute';
	
		$bm_params['options']                     = range(0, 59);
		$bm_params['use_only_values']             = true;
		$bm_params['convert_keyed_value_to_text'] = true;
		$bm_params['conversion_function']         = "add_zero_to_int_less_than_ten";
		$bm_params['default_selected_text']       = add_zero_to_int_less_than_ten($selected_minute);
		$bm_params['default_selected_value']      = $selected_minute;

		if(intval($selected_minute) < 0)
		{
			$bm_params['default_selected_text']  = $opening_selected_text;
			$bm_params['default_selected_value'] = $selected_minute;
		}
		
		return self::build_select_options($bm_params);
	
	}

	/*
	function build_input($input_types, $text, $input_names=array(), $input_ids=array()){

		$names_length = count($input_names);
		$ids_length = count($input_ids);

		for($i = 0; $i < $names_length; $i++){
			$input_name = $input_names[$i];
		}

		for($j = 0; $j < $ids_length; $j++){
			$input_id = $input_ids[$j];
		}

		foreach($input_types as $key => $value){
			$inputs = '<input type="'. $value. '" value="'. $key. '" name="'. $input_name. '" id="'. $input_id. '">';
		}

		echo($text. "<br />");
		echo($inputs);

	}
	*/
	
}