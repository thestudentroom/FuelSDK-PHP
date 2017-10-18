<?php

namespace MarketingCloud\REST;

use MarketingCloud\Constructor;

/**
 * This class represents the DELETE operation for REST service.
 */
class Delete extends Constructor {

	/**
	 * Initializes a new instance of the class.
	 * @param   Client   $authStub  The ET client object which performs the auth token, refresh token using clientID clientSecret
	 * @param   string      $url        The endpoint URL
	 */
	public function __construct($authStub, $url) {
		$restResponse = Util::restDelete($url, $authStub);
		parent::__construct($restResponse->body, $restResponse->httpcode, true);
	}

}
