<?php

namespace MarketingCloud;

use MarketingCloud\SOAP\CUDSupport;

/**
 * This class represents a folder in a Marketing Cloud account.
 */
class Folder extends CUDSupport {

	/**
	 * Initializes a new instance of the class and sets the obj property of parent.
	 */
	public function __construct() {
		$this->obj = "DataFolder";
	}

}
