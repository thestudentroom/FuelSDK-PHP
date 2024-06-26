<?php
namespace FuelSdk;


/**
 * This class represents the PERFORM operation for SOAP service.
 */
class ET_PerformExtract extends ET_Constructor
{
	/**
	* Initializes a new instance of the class.
	* @param 	ET_Client   $authStub 	The ET client object which performs the auth token, refresh token using clientID clientSecret
	* @param 	array       $props 		Dictionary type array of properties which may hold e.g. array('ID' => '', 'Parameters' => '')
	*/
	function __construct($authStub, $props)
	{
		$authStub->refreshToken();
		$perform = array();
		$performRequest = array();
		$performRequest["Requests"] = $props;
		$perform['ExtractRequestMsg'] = $performRequest;
		$out_header = [];
		$return = $authStub->__soapCall("Extract", $perform, null, null , $out_header);
		parent::__construct($return, $authStub->__getLastResponseHTTPCode());
        print_r($return);

        $this->status = $return->OverallStatus;
        $this->request_id = $return->RequestID;
	}
}
?>