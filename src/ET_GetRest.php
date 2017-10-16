<?php

namespace MarketingCloud;

/**
 * This class represents the GET operation for REST service.
 */
class ET_GetRest extends ET_Constructor
{
	/** 
	* Initializes a new instance of the class.
	* @param 	ET_Client   $authStub 	The ET client object which performs the auth token, refresh token using clientID clientSecret
	* @param 	string 		$url 		The endpoint URL
	* @param 	mixed 		$qs 		Reserved for future use
	*/
	function __construct($authStub, $url, $qs = null)
	{
		$restResponse = ET_Util::restGet($url, $authStub);
		$this->moreResults = false;
		parent::__construct($restResponse->body, $restResponse->httpcode, true);
	}
}
