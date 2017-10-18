<?php

namespace MarketingCloud;

/**
 * This class represents a folder in a Marketing Cloud account.
 */
class Folder extends CUDSupport
{
	/** 
	* Initializes a new instance of the class and sets the obj property of parent.
	*/
	function __construct()
	{
		$this->obj = "DataFolder";
	}
}
