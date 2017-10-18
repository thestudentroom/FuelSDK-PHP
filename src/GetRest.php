<?php

namespace MarketingCloud;

/**
 * This class represents the GET operation for REST service.
 */
class GetRest extends Constructor {

	/**
	 * Initializes a new instance of the class.
	 * @param 	Client   $authStub 	The ET client object which performs the auth token, refresh token using clientID clientSecret
	 * @param 	string 		$url 		The endpoint URL
	 * @param 	mixed 		$qs 		Reserved for future use
	 */
	public function __construct($authStub, $url, $qs = null) {
		$restResponse = Util::restGet($url, $authStub);
		$this->moreResults = false;
		parent::__construct($restResponse->body, $restResponse->httpcode, true);
	}

}
