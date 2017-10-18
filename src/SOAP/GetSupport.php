<?php

namespace MarketingCloud\SOAP;

use MarketingCloud\Info;

/**
 * This class represents the get operation for SOAP service.
 */
class GetSupport extends BaseObject {

	/**
	 * @return Get     Object of type Get which contains http status code, response, etc from the GET SOAP service
	 */
	public function get() {
		$lastBatch = false;
		if (property_exists($this,'getSinceLastBatch' )){
			$lastBatch = $this->getSinceLastBatch;
		}
		$response = new Get($this->authStub, $this->obj, $this->props, $this->filter, $lastBatch);
		$this->lastRequestID = $response->request_id;
		return $response;
	}

	/**
	 * @return Continue    returns more response from the SOAP service
	 */
	public function getMoreResults() {
		$response = new ContinueRequest($this->authStub, $this->lastRequestID);
		$this->lastRequestID = $response->request_id;
		return $response;
	}

	/**
	 * @return Info    returns information from the SOAP service
	 */
	public function info() {
		$response = new Info($this->authStub, $this->obj);
		return $response;
	}

}
