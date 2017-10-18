<?php

namespace MarketingCloud;

/**
 * This class represents an Account User.
 */
class User extends CUDSupport
{
	/** 
	* Initializes a new instance of the class and sets the obj property of parent.
	*/	
	function __construct()
	{
		$this->obj = "AccountUser";
	}
}
