<?php

/**
* @author Michael Orji
* @version 1.0
*/

class RegistryMySql extends MySqlExtended{

	/**
	* Reference to a registry object
	*/
	protected $registry;

	/**
	* Construct our database object
	*/
	public function __construct($registry_object, $host='', $user='', $password='', $database='')
	{
		$this->registry = $registry_object;
		return parent::get_instance($host, $user, $password, $database);
		//return $this->get_instance($host, $user, $password, $database);
	}
}