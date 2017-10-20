<?php

namespace MarketingCloud;

use stdClass;
use Exception;

/**
 * Utility functions.
 */
class HTTP {

	const METHOD_GET    = 'GET';
	const METHOD_POST   = 'POST';
	const METHOD_PUT    = 'PUT';
	const METHOD_PATCH  = 'PATCH';
	const METHOD_DELETE = 'DELETE';

	/**
	 * Proxy config, etc.
	 * @var array
	 */
	protected $config = [];

	/**
	 * Default headers.
	 * @var array
	 */
	protected $headers = [];

	public function __construct( array $config = [], array $headers = [] ) {

		$this->config = $config + [
			'proxy_host' => '',
			'proxy_port' => '',
			'proxy_user' => '',
			'proxy_pass' => '',
		];

		$this->headers = $headers + [
			'User-Agent' => 'Fuel-SDK',
		];

	}

	/**
	 * Perform a GET request
	 * @param  string $url
	 * @param  array  $headers
	 * @return stdClass
	 */
	public function get( $url, array $headers = [] ) {
		return $this->sendRequest(static::METHOD_GET, $url, null, $headers);
	}

	/**
	 * Perform a POST request
	 * @param  string $url
	 * @param  string|array $body
	 * @param  array  $headers
	 * @return stdClass
	 */
	public function post( $url, $body, array $headers = [] ) {
		return $this->sendRequest(static::METHOD_POST, $url, $body, $headers);
	}

	/**
	 * Perform a PUT request
	 * @param  string $url
	 * @param  string|array $body
	 * @param  array  $headers
	 * @return stdClass
	 */
	public function put( $url, $body, array $headers = [] ) {
		return $this->sendRequest(static::METHOD_PUT, $url, $body, $headers);
	}

	/**
	 * Perform a PATCH request
	 * @param  string $url
	 * @param  string|array $body
	 * @param  array  $headers
	 * @return stdClass
	 */
	public function patch( $url, $body, array $headers = [] ) {
		return $this->sendRequest(static::METHOD_PATCH, $url, $body, $headers);
	}

	/**
	 * Perform a DELETE request
	 * @param  string $url
	 * @param  string|array $body
	 * @param  array  $headers
	 * @return stdClass
	 */
	public function delete( $url, $body, array $headers = [] ) {
		return $this->sendRequest(static::METHOD_DELETE, $url, $body, $headers);
	}

	/**
	 * Attempt to return the last modified date for a url.
	 * @param  string $url
	 * @return string
	 */
	public function getLastModifiedDate( $url ) {

		$ch = $this->getCURL([
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_NOBODY         => true,
			CURLOPT_FILETIME       => true,

		]);

		$result = curl_exec($ch);

		if( $result === false ) {
			throw new Exception(curl_error($ch));
		}

		return curl_getinfo($ch, CURLINFO_FILETIME);

	}

	public function upload( $url, $path ) {

		$ch = $this->getCURL([
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => [
				'file_contents' => new CURLFile(
					$this->props['filePath'],
					mime_content_type($this->props['filePath'])
				)
			]
		]);

		$response = new stdClass();

		$response->body   = curl_exec($ch);
		$response->status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		return $response;

	}

	/**
	 * Send a CURL request and return the response
	 * @param  resource $ch CURL resource handle containing the request to send
	 * @return stdClass     response object containing the reponse body and status code.
	 */
	
	/**
	 * Send a cURL request and return the response
	 * @param  string $method  one of the class METHOD_* constants
	 * @param  string $url
	 * @param  string|array $body
	 * @param  array  $headers
	 * @return stdClass
	 */
	protected function sendRequest( $method, $url, $body, array $headers = [] ) {

		$options = [
			CURLOPT_CUSTOMREQUEST  => $method,
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTPHEADER     => $this->getHeaders($headers),

		];

		if( isset($body) ) {
			$options[CURLOPT_POSTFIELDS] = $body;
		}

		$ch = $this->getCURL($options);

		$response = new stdClass();

		$response->body   = curl_exec($ch);
		$response->status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		return $response;

	}

	/**
	 * Create a cURL resource with the specific options
	 * @param  array  $options
	 * @return resource
	 */
	protected function getCURL( array $options ) {

		$ch = curl_init();

		foreach( $options as $k =>$v ) {
			curl_setopt($ch, $k, $v);
		}

		// proxy settings
		if( !empty($this->config['proxy_host']) ) {
			$this->setProxy($ch);
		}

		return $ch;

	}

	/**
	 * Get headers in cURL format.
	 * @param array    $headers
	 * @return array
	 */
	protected function getHeaders( array $headers ) {

		$combined = [];

		foreach( $headers + $this->headers as $k => $v ) {
			$combined[] = "{$k}: {$v}";
		}

		return $combined;

		curl_setopt($ch, CURLOPT_HTTPHEADER, $combined);

	}

	/**
	 * Set the proxy options on a cURL resource
	 * @param resource $ch
	 */
	protected function setProxy( $ch ) {

		curl_setopt($ch, CURLOPT_PROXY, $this->config['proxy_host']);

		if( !empty($this->config['proxy_port']) ) {
			curl_setopt($ch, CURLOPT_PROXYPORT, $this->config['proxy_port']);
		}

		if( !empty($this->config['proxy_user']) ) {
			curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->config['proxy_user']. ':'. $this->config['proxy_pass']);
		}

	}

}
