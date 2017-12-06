<?php

namespace MarketingCloud\SOAP;

use SoapVar;

use MarketingCloud\Constructor;

/**
 * This class represents the DELETE operation for SOAP service.
 */
class MultiDelete extends Constructor {

	/**
	 * Initializes a new instance of the class.
	 * @param 	Client   $authStub 	The ET client object which performs the auth token, refresh token using clientID clientSecret
	 * @param   string   $objType 	Object name, e.g. "ImportDefinition", "DataExtension", etc
	 * @param 	array    $props     Dictionary type array which may hold e.g. array('id' => '', 'key' => '')
	 */
	public function __construct($authStub, $objType, $props) {

        $authStub->refreshToken();
        $cr      = [];
        $objects = [];
        $object  = $props;

        // Foreach delete item to process, create the structure for Soap Call
        foreach ($props['Keys'] as $delta => $itemProps) {

            // Ensure required values are there
            if(!empty($itemProps) && !empty($props['CustomerKey'])) {
                $currentProp['CustomerKey'] = $props['CustomerKey'];
                $currentProp['Keys']        = $itemProps;

                // Create SoapVar
                $objects["Objects"][$delta] = new SoapVar($currentProp, SOAP_ENC_OBJECT, $objType, "http://exacttarget.com/wsdl/partnerAPI");
            }
        }

        // Set Options
        $objects["Options"]  = "";
        $cr["DeleteRequest"] = $objects;

		$return = $authStub->__soapCall("Delete", $cr, null, null , $out_header);
		parent::__construct($return, $authStub->__getLastResponseHTTPCode());

		if ($this->status){
			if (property_exists($return, "Results")) {
				// We always want the results property when doing a retrieve to be an array
				if (is_array($return->Results)) {
					$this->results = $return->Results;
				}
				else {
					$this->results = array($return->Results);
				}
			}
			else {
				$this->status = false;
			}
			if ($return->OverallStatus != "OK") {
				$this->status = false;
			}
		}

	}

}
