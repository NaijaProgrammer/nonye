<?php
namespace pcl\interfaces;

interface Serializable 
{
	function serialize();
	function unserialize($serialized_obj);

}

?>