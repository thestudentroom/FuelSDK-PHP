<?php
// spl_autoload_register( function($class_name) {
//     include_once 'src/'.$class_name.'.php';
// });
namespace FuelSdk;

/**
 *	An asset is an instance of any kind of content in the CMS.
 */
class ET_Asset extends ET_CUDSupportRest
{
    /**
    * The constructor will assign endpoint, urlProps, urlPropsRequired fields of parent ET_BaseObjectRest
    */ 
	function __construct()
	{
		$this->path = "/guide/v1/contentItems/portfolio/{id}";
		$this->urlProps = array("id");
		$this->urlPropsRequired = array();
	}

    // method for calling a Fuel API using POST
    /**
    * @return object     The stdClass object with property httpcode and body from the REST service after upload is finished
    */ 	
	public function upload()
	{
		// TODO create unit test for this function
		$completeURL = $this->authStub->baseUrl . "/guide/v1/contentItems/portfolio/fileupload";

		$post = array('file_contents'=>'@'.$this->props['filePath']);

        $ch = curl_init();
        
		$headers = array("User-Agent: ".ET_Util::getSDKVersion(), "Authorization: Bearer ".$this->authStub->getAuthToken());
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);	

		curl_setopt($ch, CURLOPT_URL, $completeURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

		// Disable VerifyPeer for SSL
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		$outputJSON = curl_exec($ch);
		//curl_close ($ch);
		
		$responseObject = new stdClass(); 
		$responseObject->body = $outputJSON;
		$responseObject->httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close ($ch);	
		
		return $responseObject;
	}

    /**
    * @return null
    */
	public function patch()
	{
		return null;
	}

    /**
    * @return null
    */
	public function delete()
	{
		return null;
	}
}
?>