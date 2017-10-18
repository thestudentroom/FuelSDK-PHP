<?php

namespace MarketingCloud;

use MarketingCloud\SOAP\CUDWithUpsertSupport;

/**
 * A person subscribed to receive email or SMS communication.
 */
class Subscriber extends CUDWithUpsertSupport {

	/**
	 * Initializes a new instance of the class and sets the obj property of parent.
	 */
	public function __construct() {
		$this->obj = "Subscriber";
	}

}
