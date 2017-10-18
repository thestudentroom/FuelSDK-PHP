<?php

namespace MarketingCloud;

/**
 * A person subscribed to receive email or SMS communication.
 */
class Subscriber extends CUDWithUpsertSupport
{
	/** 
	* Initializes a new instance of the class and sets the obj property of parent.
	*/	
	function __construct()
	{
		$this->obj = "Subscriber";
	}	
}
