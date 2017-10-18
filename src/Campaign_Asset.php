<?php

namespace MarketingCloud;

/**
* Represents an asset associated with a campaign.
*/ 
class Campaign_Asset extends CUDSupportRest 
{
    /**
    * Initializes a new instance of the class and will assign endpoint, urlProps, urlPropsRequired fields of parent BaseObjectRest
    */ 
	function __construct()
	{
		$this->endpoint = "https://www.exacttargetapis.com/hub/v1/campaigns/{id}/assets/{assetId}";		
		$this->urlProps = array("id", "assetId");
		$this->urlPropsRequired = array("id");
	}
}
