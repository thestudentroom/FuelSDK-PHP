<?php

namespace MarketingCloud;

/**
 * This class represents the PUT operation for REST service.
 */
class PutRest extends Constructor
{
	/**
	* Initializes a new instance of the class.
	* @param 	Client   $authStub 	The ET client object which performs the auth token, refresh token using clientID clientSecret
	* @param 	string 		$url 		The endpoint URL
	* @param 	array       $props 		Dictionary type array which may hold e.g. array('id' => '', 'key' => '')
	*/	
	function __construct($authStub, $url, $props)
	{
		$restResponse = Util::restPut($url, json_encode($props), $authStub);			
		parent::__construct($restResponse->body, $restResponse->httpcode, true);							
	}
}
