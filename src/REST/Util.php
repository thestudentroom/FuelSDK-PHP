<?php

namespace MarketingCloud\REST;

use stdClass;

/**
 *  This utility class performs all the REST operation over CURL.
 */
class Util {

	/**
	 * Function for calling a Fuel API using GET
	 * @param string      $url    The resource URL for the REST API
	 * @param Client   $authStub 	The ET client object which performs the auth token, refresh token using clientID clientSecret
	 * @return string     The response payload from the REST service
	 */
	public static function restGet($url, $authStub) {

		$ch = curl_init();
		$headers = array("User-Agent: ".self::getSDKVersion());
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
		// Uses the URL passed in that is specific to the API used
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPGET, true);

		static::setCommonOptions($ch, $authStub);

		return static::sendRequest($ch);

	}

	/**
	 * Function for calling a Fuel API using POST
	 * @param string      $url    The resource URL for the REST API
	 * @param string      $content    A string of JSON which will be passed to the REST API
	 * @param Client   $authStub 	The ET client object which performs the auth token, refresh token using clientID clientSecret
	 * @return string     The response payload from the REST service
	 */
	public static function restPost($url, $content, $authStub) {

		$ch = curl_init();

		// Uses the URL passed in that is specific to the API used
		curl_setopt($ch, CURLOPT_URL, $url);

		// When posting to a Fuel API, content-type has to be explicitly set to application/json
		$headers = array("Content-Type: application/json", "User-Agent: ".self::getSDKVersion());
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);

		// The content is the JSON payload that defines the request
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $content);

		static::setCommonOptions($ch, $authStub);

		return static::sendRequest($ch);

	}

	/**
	 * Function for calling a Fuel API using PATCH
	 * @param string      $url    The resource URL for the REST API
	 * @param string      $content    A string of JSON which will be passed to the REST API
	 * @param Client   $authStub 	The ET client object which performs the auth token, refresh token using clientID clientSecret
	 * @return string     The response payload from the REST service
	 */
	public static function restPatch($url, $content, $authStub) {

		$ch = curl_init();

		// Uses the URL passed in that is specific to the API used
		curl_setopt($ch, CURLOPT_URL, $url);

		// When posting to a Fuel API, content-type has to be explicitly set to application/json
		$headers = array("Content-Type: application/json", "User-Agent: ".self::getSDKVersion());
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);

		// The content is the JSON payload that defines the request
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $content);

		static::setCommonOptions($ch, $authStub);

		//Need to set the request to be a PATCH
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH" );

		return static::sendRequest($ch);

	}

	/**
	 * Function for calling a Fuel API using PATCH
	 * @param string      $url    The resource URL for the REST API
	 * @param string      $content    A string of JSON which will be passed to the REST API
	 * @param Client   $authStub 	The ET client object which performs the auth token, refresh token using clientID clientSecret
	 * @return string     The response payload from the REST service
	 */
	public static function restPut($url, $content, $authStub) {

		$ch = curl_init();

		// Uses the URL passed in that is specific to the API used
		curl_setopt($ch, CURLOPT_URL, $url);

		// When posting to a Fuel API, content-type has to be explicitly set to application/json
		$headers = array("Content-Type: application/json", "User-Agent: ".self::getSDKVersion());
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);

		// The content is the JSON payload that defines the request
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $content);

		static::setCommonOptions($ch, $authStub);

		//Need to set the request to be a PATCH
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT" );

		return static::sendRequest($ch);

	}

	/**
	 * Function for calling a Fuel API using DELETE
	 * @param string      $url    The resource URL for the REST API
	 * @param Client   $authStub 	The ET client object which performs the auth token, refresh token using clientID clientSecret
	 * @return string     The response payload from the REST service
	 */
	public static function restDelete($url, $authStub) {

		$ch = curl_init();

		$headers = array("User-Agent: ".self::getSDKVersion());
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);

		// Uses the URL passed in that is specific to the API used
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPGET, true);

		static::setCommonOptions($ch, $authStub);

		// Set CustomRequest up for Delete
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

		return static::sendRequest($ch);

	}

	/**
	 * Set common curl options.
	 * @param resource $ch curl handle that options are set on.
	 */
	protected function setCommonOptions( $ch, $authStub ) {

		//Need to set ReturnTransfer to True in order to store the result in a variable
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// Disable VerifyPeer for SSL
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		//proxy setting
		if (!empty($authStub->proxyHost)) {
			curl_setopt($ch, CURLOPT_PROXY, $authStub->proxyHost);
		}

		if (!empty($authStub->proxyPort)) {
			curl_setopt($ch, CURLOPT_PROXYPORT, $authStub->proxyPort);
		}

		if (!empty($authStub->proxyUserName) && !empty($authStub->proxyPassword)) {
			curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $authStub->proxyUserName.':'.$authStub->proxyPassword);
		}

	}

	/**
	 * Send a CURL request and return the response
	 * @param  resource $ch CURL resource handle containing the request to send
	 * @return stdClass     response object containing the reponse body and status code.
	 */
	protected function sendRequest( $ch ) {

		$response = new stdClass();

		$response->body     = curl_exec($ch);
		$response->httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		return $response;

	}

}
