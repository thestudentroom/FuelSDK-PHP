<?php

namespace MarketingCloud\REST;

use Exception;

/**
 * This class represents the create, update, delete operation for REST service.
 */
class CUDSupport extends GetSupport {

	/**
	 * @var      string      Folder property e.g. "Category", "CategoryID", etc.
	 */
	protected $folderProperty;

	/**
	 * @var      string      Folder Media Type e.g. "dataextension", "triggered_send", etc.
	 */
	protected $folderMediaType;

	/**
	 * @return Post     Object of type Post which contains http status code, response, etc from the POST REST service
	 */
	public function post() {

		$this->authStub->refreshToken();
		$completeURL = $this->endpoint;
		$additionalQS = array();

		if (!is_null($this->props)) {
			foreach ($this->props as $key => $value){
				if (in_array($key,$this->urlProps)){
					$completeURL = str_replace("{{$key}}",$value,$completeURL);
				}
			}
		}

		foreach($this->urlPropsRequired as $value){
			if (is_null($this->props) || in_array($value,$this->props)){
				throw new Exception("Unable to process request due to missing required prop: {$value}");
			}
		}

		// Clean up not required URL parameters
		foreach ($this->urlProps as $value){
			$completeURL = str_replace("{{$value}}","",$completeURL);
		}

		$additionalQS["access_token"] = $this->authStub->getAuthToken();
		$queryString = http_build_query($additionalQS);
		$completeURL = "{$completeURL}?{$queryString}";
		$response = new PostRest($this->authStub, $completeURL, $this->props);

		return $response;

	}

	/**
	 * method for calling a Fuel API using PATCH
	 * @return Patch     Object of type Patch which contains http status code, response, etc from the PATCH REST service
	 */
	public function patch() {

		$this->authStub->refreshToken();
		$completeURL = $this->endpoint;
		$additionalQS = array();

		// All URL Props are required when doing Patch
		foreach($this->urlProps as $value){
			if (is_null($this->props) || !array_key_exists($value,$this->props)){
				throw new Exception("Unable to process request due to missing required prop: {$value}");
			}
		}


		if (!is_null($this->props)) {
			foreach ($this->props as $key => $value){
				if (in_array($key,$this->urlProps)){
					$completeURL = str_replace("{{$key}}",$value,$completeURL);
				}
			}
		}
		$additionalQS["access_token"] = $this->authStub->getAuthToken();
		$queryString = http_build_query($additionalQS);
		$completeURL = "{$completeURL}?{$queryString}";
		$response = new Patch($this->authStub, $completeURL, $this->props);

		return $response;

	}

	/**
	 * method for calling a Fuel API using DELETE
	 * @return Delete     Object of type Delete which contains http status code, response, etc from the DELETE REST service
	 */
	public function delete() {

		$this->authStub->refreshToken();
		$completeURL = $this->endpoint;
		$additionalQS = array();

		// All URL Props are required when doing Delete
		foreach($this->urlProps as $value){
			if (is_null($this->props) || !array_key_exists($value,$this->props)){
				throw new Exception("Unable to process request due to missing required prop: {$value}");
			}
		}

		if (!is_null($this->props)) {
			foreach ($this->props as $key => $value){
				if (in_array($key,$this->urlProps)){
					$completeURL = str_replace("{{$key}}",$value,$completeURL);
				}
			}
		}
		$additionalQS["access_token"] = $this->authStub->getAuthToken();
		$queryString = http_build_query($additionalQS);
		$completeURL = "{$completeURL}?{$queryString}";
		$response = new Delete($this->authStub, $completeURL);

		return $response;

	}

}
