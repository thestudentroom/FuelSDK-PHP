<?php

namespace MarketingCloud\REST;

use MarketingCloud\Constructor;
use MarketingCloud\Util;

/**
 * This class represents the PATCH operation for REST service.
 */
class Patch extends Constructor {

	/**
	 * Initializes a new instance of the class.
	 * @param 	Client   $authStub 	The ET client object which performs the auth token, refresh token using clientID clientSecret
	 * @param 	string 		$url 		The endpoint URL
	 * @param 	array       $props 		Dictionary type array which may hold e.g. array('id' => '', 'key' => '')
	 */
	public function __construct($authStub, $url, $props) {
		$response = $authStub->getHTTP()->patch(
			$url,
			json_encode($props),
			[
				'Content-Type' => 'application/json',
			]
		);
		parent::__construct($response->body, $response->status, true);
	}

}
