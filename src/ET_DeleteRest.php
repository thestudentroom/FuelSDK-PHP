<?php

namespace MarketingCloud;

/**
 * This class represents the DELETE operation for REST service.
 */
class ET_DeleteRest extends ET_Constructor
{
	/** 
	* Initializes a new instance of the class.
	* @param 	ET_Client   $authStub 	The ET client object which performs the auth token, refresh token using clientID clientSecret
	* @param 	string 		$url 		The endpoint URL
	*/	
	function __construct($authStub, $url)
	{
		$restResponse = ET_Util::restDelete($url, $authStub);			
		parent::__construct($restResponse->body, $restResponse->httpcode, true);							
	}
}
