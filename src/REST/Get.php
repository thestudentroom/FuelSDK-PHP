<?php

namespace MarketingCloud\REST;

use MarketingCloud\Constructor;
use MarketingCloud\Util;

/**
 * This class represents the GET operation for REST service.
 */
class Get extends Constructor {

	/**
	 * Initializes a new instance of the class.
	 * @param 	Client   $authStub 	The ET client object which performs the auth token, refresh token using clientID clientSecret
	 * @param 	string 		$url 		The endpoint URL
	 * @param 	mixed 		$qs 		Reserved for future use
	 */
	public function __construct($authStub, $url, $qs = null) {
		$response = $authStub->getHTTP()->get(
			$url,
			null
		);
		$this->moreResults = false;
		parent::__construct($response->body, $response->status, true);
	}

}
