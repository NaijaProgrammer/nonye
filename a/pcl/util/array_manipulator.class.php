<?php

/*
* @author Michael Orji
*/
class ArrayManipulator
{
	/* 
	* A function that loops through the [N][named_element] element 
	* of a matrix to determine if a given vaulue ($search_value) exitsts
	*
	* @author: Michael Orji
	* @date: June 6, 2010
	* 
	* boolean in_matrix(mixed search_value, matrix matrix, string named_element);
	*
	*******************************************************************************/

	public static function in_matrix($search_value, $matrix, $named_element)
	{
 		$matrix_length = count($matrix);

   		/* check to make sure we're not dealing with an empty matrix */
   		if($matrix_length > 0)
		{
      		for($i = 0; $i < $matrix_length; $i++)
			{

         		if(trim($matrix[$i][$named_element]) == trim($search_value))
				{
          			return true;
         		}
      		}
   		}

		return false;
	} 


	/**
	* copy second array into first array
	* @return array the new array
	* @author: Michael Orji
	* @date: 24 Dec., 2012
	*/
	public static function copy_array(&$default_array, $opts_array, $elements_to_ignore = array())
	{
		foreach($opts_array AS $key => $value)
		{
			$value = is_string($value) ? trim($value) : $value;

			if(!in_array($key, $elements_to_ignore, true))
			{
				if(is_object($default_array))
				{
					$default_array->$key = $value;
				}
				else
				{
					$default_array[$key] = $value;
				}
			}
		}
		return $default_array;
	}
	
	/**
	* converts a redundant matrix (i.e a matrix where each member is an array with just one key which is same key as the others) to array:
	* e. g $names = array( 0=>array('name'=>'mike'), 1=>array('name'=>'john'), 2=>array('name'=>'jude'), ... ), each member is an array with same key of 'name'
	* so, we avoid the redundancy by reducing the matrix to a single array thus: array(0=>'mike', 1=>'john', 2=>'jude') by calling:
	* ArrayManipulator::reduce_redundant_matrix_to_array( $names, 'name');
	* @return array the new (reduced) array
	* @author: Michael Orji
	* @date: Nov. 5, 2013
	*/
	public static function  reduce_redundant_matrix_to_array($matrix, $redundant_key)
	{
		$arr = array();
		foreach($matrix AS $arr_value)
		{
			$arr[] = $arr_value[$redundant_key];
		}
		return $arr;
	}
	
	/**
	* Traverse an array or matrix, getting the next member.
	* After getting the current member, the pointer moves to the next element in the array.
	* Works only on numeric arrays
	* @param array $matrix
	* @param int $offset the offset at which to begin. This must be declared as a variable for it to work, as primitive cannot be passed by reference
	* @return mixed returns a scalar value for a one-dimensional array and an array for a multidimensional array, returns NULL when you reach array's end
	* @author Michael Orji
	* @date Oct. 26, 2014
	*/
	public static function get_next($matrix, &$offset=0)
	{
		while($offset != count($matrix))
		{
			/*
			* redundant conditional test
			if($offset == count($matrix))
			{
				break;
			}*/
			return $matrix[$offset++];
		}
		return NULL;
	}
}