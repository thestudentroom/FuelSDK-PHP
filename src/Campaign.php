<?php

namespace MarketingCloud;

use MarketingCloud\REST\CUDSupport;

/**
 * Represents a program in an account
 */
class Campaign extends CUDSupport {

	/**
	* Initializes a new instance of the class and will assign endpoint, urlProps, urlPropsRequired fields of parent BaseObjectRest
	*/
	public function __construct() {
		$this->endpoint = "https://www.exacttargetapis.com/hub/v1/campaigns/{id}";
		$this->urlProps = array("id");
		$this->urlPropsRequired = array();
	}

}
