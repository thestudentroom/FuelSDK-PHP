<?php

namespace MarketingCloud;

/**
 *  Represents ClickEvent Class.
 *  Contains information regarding a click on a link contained in a message.
 */
class ClickEvent extends GetSupport {

	/**
	* @var bool	Gets or sets a boolean value indicating whether to get since last batch. true if get since last batch; otherwise, false.
	*/
	public  $getSinceLastBatch;

	/**
	* Initializes a new instance of the class and set the since last batch to true.
	*/
	public function __construct() {
		$this->obj = "ClickEvent";
		$this->getSinceLastBatch = true;
	}

}
