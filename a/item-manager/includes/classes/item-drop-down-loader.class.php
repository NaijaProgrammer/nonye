<?php
/**
* @author: Michael Orji
* @date: November 4, 2011
* @modified to add success_callback May 2, 2016
*/
class ItemDropDownLoader
{
	public static function load_dropdown_list( $opts = array() )
	{
		$defs = array(
			'type'                 => 'dropdown-list', 
			'id'                   => '', 
			'parent_id'            => '', 
			'name'                 => '',
			'default_value'        => '', 
			'default_text'         => '', 
			'selected_text'        => '', 
			'selected_value'       => '', 
			'options'              => '', 
			'onchange_handler'     => '', 
			'ready_state_callback' => '', 
			'success_callback'     => ''
		);
		
		ArrayManipulator::copy_array($defs, $opts);
		
		foreach($defs AS $key => $value)
		{ 
			$$key = $value; 
		}
		
		$optnx = '';
		
		if( !empty($default_text) )
		{ 
			$optnx .= self::load_dropdown_list_option(array('name'=>$name, 'text'=>$default_text, 'value'=>$default_value, 'state'=>''));
		}
		
		if(!empty($selected_text))
		{
			$optnx .= self::load_dropdown_list_option(array('name'=>$name, 'text'=>$selected_text, 'value'=>$selected_value, 'state'=>'selected="selected"'));
		}
		
		$defs['options']              = $optnx. $defs['options'];
		$defs['onchange_handler']     = !empty($onchange_handler)     ? $onchange_handler     : 'emptyFunction';
		$defs['ready_state_callback'] = !empty($ready_state_callback) ? $ready_state_callback : 'emptyFunction';
		$defs['success_callback']     = !empty($success_callback)     ? $success_callback     : 'emptyFunction';
		
		$defs['app_http_path'] = ITEM_MANAGER_APP_HTTP_PATH;
		return self::_load_template_component($defs); 
	}
	
	/**
	* the 'name' member of the array is the name of the <select> element
	* it is required for sticky value to work on form submission
	*/
	public static function load_dropdown_list_option( $opts = array() )
	{
		$defs = array('type'=>'dropdown-list-option', 'text'=>'', 'value'=>'', 'state'=>'', 'name'=>'');
		
		ArrayManipulator::copy_array($defs, $opts);
		
		if( ($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST[$defs['name']]) && ($_POST[$defs['name']] == $defs['value']) )
		{
			$defs['state'] = 'selected="selected"';
		}
		
		return self::_load_template_component($defs);
	}
	
	private static function _load_template_component($opts = array())
	{
		$defs   = array('type'=>'dropdown-list', 'name'=>'', 'id'=>'', 'class'=>'', 'value'=>'', 'title'=>'', 'style_rules'=>array());
		$styles = '';
		
		ArrayManipulator::copy_array($defs, $opts);
		
		foreach($defs AS $key => $value)
		{ 
			$$key = $value; 
		}
		
		$tpl = new TemplateLoader( ITEM_MANAGER_APP_FILE_PATH. '/includes/tpl/'. $type. '.tpl' );
		
		foreach($defs AS $key => $value) 
		{  
			if( ($key == 'style_rules') && is_array($defs['style_rules']))
			{ 
				foreach($defs['style_rules'] AS $style_key => $style_value)
				{
					$styles .= "{$style_key}:{$style_value};";
				}
				$tpl->$key = $styles;
			}
			else
			{
				$tpl->$key = is_string($value) ? trim($value) : $value; 
			}
		}
		
		return $tpl->load();
	}
}