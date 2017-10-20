<?php

namespace MarketingCloud;

use stdClass;

use MarketingCloud\REST\CUDSupport;

/**
 *	An asset is an instance of any kind of content in the CMS.
 */
class Asset extends CUDSupport {

	/**
	* The constructor will assign endpoint, urlProps, urlPropsRequired fields of parent BaseObject
	*/
	public function __construct() {
		$this->endpoint = "https://www.exacttargetapis.com/guide/v1/contentItems/portfolio/{id}";
		$this->urlProps = array("id");
		$this->urlPropsRequired = array();
	}

	// method for calling a Fuel API using POST
	/**
	* @return object     The stdClass object with property httpcode and body from the REST service after upload is finished
	*/
	public function upload() {

		return $this->authStub->getHTTP()->upload(
			'https://www.exacttargetapis.com/guide/v1/contentItems/portfolio/fileupload?access_token='. $this->authStub->getAuthToken(),
			$this->props['filePath']
		);

	}

	/**
	* @return null
	*/
	public function patch() {
		return null;
	}

	/**
	* @return null
	*/
	public function delete() {
		return null;
	}

}
