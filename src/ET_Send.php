<?php

namespace MarketingCloud;

/**
 * Used to send email and retrieve aggregate data based on a JobID.
 */
class ET_Send extends ET_CUDSupport
{
	/** 
	* Initializes a new instance of the class and sets the obj property of parent.
	*/	
	function __construct()
	{
		$this->obj = "Send";
	}
}
