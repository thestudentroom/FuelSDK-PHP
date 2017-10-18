<?php

namespace MarketingCloud;

/**
 * This class defines any additional attribute for a subscriber.
 */
class ProfileAttribute extends BaseObject {

	/**
	 * Initializes a new instance of the class and set the since last batch to true.
	 */
	public function __construct() {
		$this->obj = "PropertyDefinition";
	}

	/**
	 * This method is used to create Property Definition for a subscriber
	 * @return Configure     Object of type Configure which contains http status code, response, etc from the POST SOAP service
	 */
	public function post() {
		return new Configure($this->authStub, $this->obj, "create", $this->props);
	}

	/**
	 * This method is used to get Property Definition for a subscriber
	 * @return Info     Object of type Info which contains http status code, response, etc from the GET SOAP service
	 */
	public function get() {
		return new Info($this->authStub, 'Subscriber', true);
	}

	/**
	 * This method is used to update Property Definition for a subscriber
	 * @return Configure     Object of type Configure which contains http status code, response, etc from the UPDATE SOAP service
	 */
	public function patch() {
		return new Configure($this->authStub, $this->obj, "update", $this->props);
	}

	/**
	 * This method is used to delete Property Definition for a subscriber
	 * @return Configure     Object of type Configure which contains http status code, response, etc from the DELETE SOAP service
	 */
	public function delete() {
		return new Configure($this->authStub, $this->obj, "delete", $this->props);
	}

}
