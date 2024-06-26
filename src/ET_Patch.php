<?php
// spl_autoload_register( function($class_name) {
//     include_once 'src/'.$class_name.'.php';
// });
namespace FuelSdk;
use \SoapVar;
/**
 * This class represents the PATCH operation for SOAP service.
 */
class ET_Patch extends ET_Constructor
{
	/**
	* Initializes a new instance of the class.
	* @param 	ET_Client   $authStub 	The ET client object which performs the auth token, refresh token using clientID clientSecret
	* @param    string      $objType 	Object name, e.g. "ImportDefinition", "DataExtension", etc
	* @param 	array       $props 		Dictionary type array which may hold e.g. array('id' => '', 'key' => '')
	* @param 	bool 		$upsert 	If true SaveAction is UpdateAdd, otherwise not. By default false.
	*/
	function __construct($authStub, $objType, $props,$upsert = false)
	{
		$authStub->refreshToken();	
		$cr = array(); 
		$objects = array(); 
		$object = $props; 				
		
		$objects["Objects"] = new SoapVar($props, SOAP_ENC_OBJECT, $objType, "http://exacttarget.com/wsdl/partnerAPI");
		if ($upsert) {
			$objects["Options"] = array('SaveOptions' => array('SaveOption' => array('PropertyName' => '*', 'SaveAction' => 'UpdateAdd' )));
		} else {
			$objects["Options"] = "";
		}
		$cr["UpdateRequest"] = $objects;
		
		$out_header = [];
		$return = $authStub->__soapCall("Update", $cr, null, null , $out_header);
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
				$this->status = false;
				
			}
			if ($return->OverallStatus != "OK")
			{
				$this->status = false;
			}
		}	
	}
}
?>