<?php

	/**
	* Template engine class (use {tag} tags in your templates).
	* 
	* @link http://www.naijaprogrammer.com/ NaijaProgrammer Developer Tutorials
	* @author Michael Orji <michaelorji@naijaprogrammer.com>
	* @version 1.0
	*
	* @adapted from Simple template engine class by Nuno Freitas
	* @link http://www.broculos.net/
	*/
    class Template 
	{
    	/**
    	* The filename of the template to load.
    	*
    	* @access protected
    	* @var string
    	*/
        protected $file;
        
        /**
        * An array of values for replacing each tag on the template (the key for each value is its corresponding tag).
        *
        * @access protected
        * @var array
        */
        protected $values = array();
        
        /**
        * Creates a new Template object and sets its associated file.
        *
        * @param string $file the full path and filename of the template file to load
        */
        public function __construct($file)
		{
            $this->file = $file;
        }
        
        /**
        * Sets a value for replacing a specific tag.
        *
        * @param string $key the name of the tag to replace
        * @param string $value the value to replace
        */
        public function __set($key, $value)
		{
            $this->set($key, $value); //$this->values[$key] = $value;
        }
		
		/**
        * Sets a value for replacing a specific tag.
        *
        * @param string $key the name of the tag to replace
        * @param string $value the value to replace
        */
        public function set($key, $value)
		{
            $this->values[$key] = $value;
        }
        
        /**
        * Outputs the content of the template, replacing the keys for its respective values.
        *
        * @return string
        */
        public function output() 
		{
        	/**
        	* Tries to verify if the file exists.
        	* If it doesn't return with an error message.
        	* Otherwise load the file contents and loop through the array replacing every key with its value.
        	*/
            if (!file_exists($this->file)) 
			{
				$location = ''; //TO DO: get the location where the template file is being called
            	return "Error loading template file ($this->file) @ $location";
            }
			
            $output = file_get_contents($this->file);
            foreach ($this->values as $key => $value) 
			{
				/** 
				* don't add objects or arrays here, to avoid Array to String Conversion error
				* objects are handled by _do_loops() method
				*/
				if(is_string($value)) 
				{ 
					$tagToReplace = "{". $key. "}";
					$output = str_replace($tagToReplace, $value, $output);
				}
            }
			$output = $this->_do_loops( $output );
			$output = $this->_evaluate_conditions($output);
			
            return $output;
        }
		
		/**
		* Filter method _do_loop()
		* Evaluate and parse template loop condition(s)
		* @param string $output the template output to filter
		* @return string $output the filtered template
		* @date Oct. 22, 2014
		*/
		private function _do_loops( $output )
		{ 
			/**
			* pattern = 
			* containing the opening delimiter '{'
			* followed by zero or more space characters
			* followed by the keyword 'loop'
			* followed by zero or more space characters
			* followed by the colon punctuation ':'
			*
			* THE FOLLOWING TWO LINES make up the loop data name, that is the data to loop through
			* followed by zero or more space characters
			* followed by one or more alphabets or underscore
			* 
			* followed by zero or more alphabets, underscore or numeral
			* followed by zero or more space characters
			* followed by the closing delimiter '}'
			* 
			* The parenthesized section helps us capture the string representing the object to perform the loop on, using the $matches array.
			*
			* This regex pattern will match: {loop:user_data}, { loop: user_data }, {loop : user_data1}, etc in the tpl file.
			* 
			* The parenthesized portion is then captured in $matches[1] and represents: user_data or user-data1
			* which are the place-holders for the object to perform the loop on
			*
			* the php script can then say Template-instance->user_data or Template-instance->user-data1 = object/array
			* e.g:
			* $tpl = new Template(/path/to/template/file);
			* $object = new StdClass();
			* $object->firstname = 'Michael';
			* $object->lastname  = 'Orji';
			* $tpl->user_data = $object
			* OR
			* $tpl->user_data = array('firstname'=>'John', 'lastname'=>'Doe'); 
			*/
			$loop_pattern = '/\{\s*loop\s*:\s*([a-zA-Z_]+[a-zA-Z_1-9]*)\s*\}/';
			$loop_string  = preg_match($loop_pattern, $output, $matches); //e.g {loop : user_data}
			
			//if the current template does not include a {loop:object-name} condition, don't attempt to loop
			//if(empty($matches))
			if( !$loop_string )
			{
				return $output;
			}
			
			$tpl_string = $matches[1]; //the string from the {loop:object-name} statement in the template that should be replaced with an object e.g: user_data, user-data1
			
			if( $tpl_string && isset($this->values[$tpl_string]) )
			{ 
			
				/**
				* extract the object/array value from the string representing the object to perform the loop on
				* for e.g in {loop:object-name}, Template-instance->user_data = object/array 
				* the key $tpl_string = user_data, the value = object/array
				*/
				$loop_object = $this->values[$tpl_string]; 
				
		
				/**
				* if user passed a scalar value, just replace the {loop:object-name} with the scalar value in the output
				* and replace the supposed data members of the object with empty strings
				*/
				if( !is_object($loop_object) && !is_array($loop_object) )
				{
					/**
					* the pattern for the members of the object/array
					* there must be no space between the object, the object-member access operator (.) and the member name
					* e.g user_data.firstname
					*/
					$object_member_pattern = "{". $tpl_string. ".". $key. "}"; 
					$output = preg_replace($loop_pattern, $loop_object, $output);
					$output = str_replace($object_member_pattern, '', $output);
					return $output;
				}
			
				/**
				* otherwise, replace the {loop:object-name} with an empty string, since it shouldn't display in the output
				*/
				$output = preg_replace($loop_pattern, '', $output);
				
				/**
				* loop through the loop object and replace the data members of the object with their real values
				*/
				foreach($loop_object AS $key => $value)
				{
					/**
					* the pattern for the members of the object/array
					* there must be no space between the object, the object-member access operator (.) and the member name
					* e.g user_data.firstname
					*/
					$object_member_pattern = "{". $tpl_string. ".". $key. "}"; 
					
					/**
					* set this so we can access the member values with the __get magic function
					*/
					$this->values[$key] = $value;
					
					$output = str_replace($object_member_pattern, $value, $output); 
				}
			}
			
			return $output;
		}
		
		/**
		* Filter method _evaluate_condition()
		* Evaluate and parse template if/else condition(s)
		* @param string $output the template output to filter
		* @return string $output the filtered template
		*/
		private function _evaluate_conditions($output)
		{
		
			/**
			* pattern = 
			* containing the opening of the condition open delimiter '{'
			* followed by zero or more space characters
			* followed by the keyword 'condition'
			* followed by zero or more space characters
			* followed by the colon punctuation ':'
			* followed by zero or more space characters
			*
			* THE FOLLOWING TWO LINES make up the condition name, that is the condition to test for
			* followed by one or more alphabets or underscore
			* followed by zero or more alphabets, underscore or numeral
			*
			* followed by zero or more space characters
			* followed by the closing of the condition open delimiter '}'
			* followed by zero or more space characters
			*
			* THE FOLLOWING TWO LINES make up the string that represent the actions to perform is condition is true 
			* followed by zero or more alphabets or underscore
			* followed by zero or more alphabets, underscore or numeral 
			* 
			* followed by zero or more space characters
			* followed by the opening of the condition close delimiter '{'
			* followed by zero or more space characters
			* followed by the punctuation '\' that signals the condition close flag
			* followed by the keyword 'condition'
			* followed by zero or more space characters
			* followed by the closing of the condition close delimiter '}'
			* 
			* The parenthesized section helps us capture the string representing:
			* 1. the data to test for
			* 2. the action(s) to perform if the condition evaluates to true, using the $matches array.
			*
			* This regex pattern will match: 
			* {condition:user_is_logged_in} action_to_perform {/condition}, 
			* { condition: user_is_admin } admin_action_to_perform {/condition}
			* {condition : user_is_admin_1} ... {/condition}, etc in the tpl file.
			* 
			* The parenthesized portion is then captured in:
			* $matches[1] representing: user_is_logged_in or user_is_admin or user_is_admin_1
			* $matches[2] representing: action_to_perform, admin_action_to_perform, etc
			*
			* which are the place-holders for the condition and the actions to perform respectively.
			*
			* the php script can then say:
			* Template-instance->user_is_logged_in or Template-instance->user_is_admin = true/false
			* Template-instance->action_to_perform = 'edit_profile' or Template-instance->admin_action_to_perform = 'delete_user'
			* e.g:
			* $tpl = new Template(/path/to/template/file);
			* $tpl->user_is_logged_out = true;
			* $tpl->log_in_form = get_login_form();
			*/
			$condition_pattern = '/\{\s*condition\s*:\s*([a-zA-Z_]+[a-zA-Z_1-9]*)\s*\}\s*([a-zA-Z_]+[a-zA-Z_1-9]*)\s*\{\s*\\condition\s*\}/';
			$condition_string  = preg_match($condition_pattern, $output, $matches);
			
			//if the current template does not include a {condition:condition_name} condition
			if( !$condition_string )
			{
				return $output;
			}
			
			/**
			* Get the string from the {condition:condition_to_test}, i.e the 'condition_to_test' part statement in the template 
			* that should be replaced with a PHP condition 
			*/
			$tpl_string_of_condition_to_test  = $matches[1]; 
			
			/**
			* Get the string from the {condition:condition_condition_to_test} actions_to_perform {/condition}, 
			* i.e the 'actions_to_perform' part statement in the template 
			* that should be replaced with PHP actions
			*/
			$tpl_string_of_actions_to_perform = $matches[2];
			
			if( $tpl_string_of_condition_to_test && isset($this->values[$tpl_string_of_condition_to_test]) )
			{ 
			
				/**
				* extract the php condition from the string representing the condition_name part
				* for e.g in {condition:condition_to_test}, Template-instance->user_is_logged_in = <?php get_user_logged_in_status(); ?>
				* the key $tpl_string = user_is_logged_in, the value = true / false, based on call to get_user_logged_in_status().
				*/
				$condition_to_test = $this->values[$tpl_string_of_condition_to_test];
			}
			
			/**
			* if the template 'condition_to_test' evaluates to true
			*/
			if($condition_to_test === true)
			{
				if( $tpl_string_of_actions_to_perform && isset($this->values[$tpl_string_of_actions_to_perform]) )
				{ 
			
					/**
					* extract the php actions to perform from the string representing the actions_to_perform part
					* for e.g in {condition:condition_to_test} login_form {/condition}, 
					* Template-instance->login_form = <?php get_login_form(); ?>
					* the key $tpl_string_of_actions_to_perform = login_form, 
					* the value = the call to get_login_form()
					*/
					$actions_to_perform = $this->values[$tpl_string_of_actions_to_perform];
					
					/**
					* replace the 'action_to_perform', e.g: login_form 
					* with the set php values, e.g the result from the call to get_login_form()
					*/
					$output = str_replace($tpl_string_of_actions_to_perform, $actions_to_perform, $output);
				}
				
			}
			else
			{
				/**
				* replace the 'actions_to_perform' with an empty string
				*/
				$output = str_replace($tpl_string_of_actions_to_perform, '', $output);
			}
			
            
			return $output;
		} 
        
        /**
        * Merges the content from an array of templates and separates it with $separator.
        *
        * @param array $templates an array of Template objects to merge
        * @param string $separator the string that is used between each Template object
        * @return string
        */
        static public function merge($templates, $separator = "\n") 
		{
            $output = "";
            
			/**
        	* Loops through the array concatenating the outputs from each template, separating with $separator.
        	* If a type different from Template is found we provide an error message. 
        	*/
            foreach ($templates as $template)
			{
            	$content = (get_class($template) !== "Template") ? "Error, incorrect type - expected Template." : $template->output();
            	$output .= $content . $separator;
            }
            
            return $output;
        }
    }