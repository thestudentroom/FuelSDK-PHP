<?php

namespace MarketingCloud;

use MarketingCloud\REST\CUDSupport;

/**
 * Represents an asset associated with a campaign.
 */
class Campaign_Asset extends CUDSupport {

	/**
	* Initializes a new instance of the class and will assign endpoint, urlProps, urlPropsRequired fields of parent BaseObjectRest
	*/
	public function __construct() {
		$this->endpoint = "https://www.exacttargetapis.com/hub/v1/campaigns/{id}/assets/{assetId}";
		$this->urlProps = array("id", "assetId");
		$this->urlPropsRequired = array("id");
	}

}
