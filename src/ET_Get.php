<?php
// spl_autoload_register( function($class_name) {
//     include_once 'src/'.$class_name.'.php';
// });
namespace FuelSdk;
use \stdClass;
use \SoapVar;
/**
 * This class represents the GET operation for SOAP service.
 */
class ET_Get extends ET_Constructor
{
	/** 
	* Initializes a new instance of the class.
	* @param 	ET_Client   $authStub 	The ET client object which performs the auth token, refresh token using clientID clientSecret
	* @param    string      $objType 	Object name, e.g. "ImportDefinition", "DataExtension", etc
	* @param 	array       $props 		Dictionary type array which may hold e.g. array('id' => '', 'key' => '')
	* @param 	array    	$filter 	Dictionary type array which may hold e.g. array("Property"=>"", "SimpleOperator"=>"","Value"=>"")
	* @param 	bool		$getSinceLastBatch 	Gets or sets a boolean value indicating whether to get since last batch. true if get since last batch; otherwise, false.
	*/	
	function __construct($authStub, $objType, $props, $filter, $getSinceLastBatch = false)
	{
		$authStub->refreshToken();
		$rrm = array();
		$request = array();
		$retrieveRequest = array();
		
		// If Props is not sent then Info will be used to find all retrievable properties
		if (is_null($props)){	
			$props = array();
			$info = new ET_Info($authStub, $objType);
			if (is_array($info->results)){	
				foreach ($info->results as $property){	
					if($property->IsRetrievable){	
						$props[] = $property->Name;
					}
				}	
			}
		}
		
		if (ET_Util::isAssoc($props)){
			$retrieveProps = array();
			foreach ($props as $key => $value){	
				if (!is_array($value))
				{
					$retrieveProps[] = $key;
				}
				$retrieveRequest["Properties"] = $retrieveProps;
			}
		} else {
			$retrieveRequest["Properties"] = $props;	
		}
		
		$retrieveRequest["ObjectType"] = $objType;
		if ("Account" == $objType) {
			$retrieveRequest["QueryAllAccounts"] = true;
		}
		if ($filter){
			if (array_key_exists("LogicalOperator",$filter )){				
				$cfp = new stdClass();
				$cfp->LeftOperand = new SoapVar($filter["LeftOperand"], SOAP_ENC_OBJECT, 'SimpleFilterPart', "http://exacttarget.com/wsdl/partnerAPI");
				$cfp->RightOperand = new SoapVar($filter["RightOperand"], SOAP_ENC_OBJECT, 'SimpleFilterPart', "http://exacttarget.com/wsdl/partnerAPI");				
				$cfp->LogicalOperator = $filter["LogicalOperator"];
				$retrieveRequest["Filter"] = new SoapVar($cfp, SOAP_ENC_OBJECT, 'ComplexFilterPart', "http://exacttarget.com/wsdl/partnerAPI");
				
			} else {
				$retrieveRequest["Filter"] = new SoapVar($filter, SOAP_ENC_OBJECT, 'SimpleFilterPart', "http://exacttarget.com/wsdl/partnerAPI");
			}
		}
		if ($getSinceLastBatch) {
			$retrieveRequest["RetrieveAllSinceLastBatch"] = true;
		}
		
		
		$request["RetrieveRequest"] = $retrieveRequest;
		$rrm["RetrieveRequestMsg"] = $request;
		
		$out_header = [];
		$return = $authStub->__soapCall("Retrieve", $rrm, null, null , $out_header);
		parent::__construct($return, $authStub->__getLastResponseHTTPCode());
		
		if ($this->status){
			if (property_exists($return, "Results")){
				// We always want the results property when doing a retrieve to be an array
				if (is_array($return->Results)){
					$this->results = $return->Results;
				} else {
					$this->results = array($return->Results);
				}
			} else {
				$this->results = array();
			}
			if ($return->OverallStatus != "OK" && $return->OverallStatus != "MoreDataAvailable")
			{
				$this->status = false;
				$this->message = $return->OverallStatus;
			}

			$this->moreResults = false;
			
			if ($return->OverallStatus == "MoreDataAvailable") {				
				$this->moreResults = true;
			}
				
			$this->request_id = $return->RequestID;
		}	
	}
}
?>