<?php

namespace MarketingCloud;

/**
* Contains information about the opening of a message send by a subscriber.
*/
class OpenEvent extends GetSupport
{
    /**
    * @var bool 	Gets or sets a boolean value indicating whether this object get since last batch. true if get since last batch; otherwise, false.
    */	
	public  $getSinceLastBatch;

	/** 
	* Initializes a new instance of the class and set the since last batch to true.
	*/	
	function __construct() 
	{
		$this->obj = "OpenEvent";
		$this->getSinceLastBatch = true;
	}
}
