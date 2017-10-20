<?php

namespace MarketingCloud;

/**
 * This class represents the contructor for all web service (SOAP/REST) operation and holds HTTP status code, response, result, etc.
 */
class Constructor {

	/**
	 * @var 	bool         	holds the status of the web service operation: true=success, false=failure
	 */
	public $status;

	/**
	 * @var 	int 			holds the HTTP status code e.g. 200, 404, etc
	 */
	public $code;

	/**
	 * @var 	string 			holds error message for SOAP call, else holds raw response if json_decode fails
	 */
	public $message;

	/**
	 * @var stdClass Object		contains the complete result of the web service operation
	 */
	public $results;

	/**
	 * @var string        		the request identifier
	 */
	public $request_id;

	/**
	 * @var bool 			    whether more results are available or not
	 */
	public $moreResults;

	/**
	 * Initializes a new instance of the class.
	 * @param 	string 		$response The response from the request
	 * @param 	int 		$status   The HTTP status code e.g. 200, 404. etc
	 * @param 	bool 		$isREST   Whether to make REST or SOAP call, default is false i.e. SOAP calls
	 */
	public function __construct( $response, $status, $isREST = false ) {

		$this->code = $status;

		if( $isREST ) {
			$this->restResponse($response);
		}
		else {
			$this->soapResponse($response);
		}

	}

	protected function restResponse( $response ) {

		$this->status = false;

		if( in_array($this->code, [200, 201, 202]) ) {
			$this->status = true;
		}

		$this->results = json_decode($response);

		if( $this->results === null ){
			$this->message = $response;
		}

	}

	protected function soapResponse( $response ) {

		$this->status = true;

		if( is_soap_fault($response) ) {
			$this->status = false;
			$this->message = "SOAP Fault: (faultcode: {$response->faultcode}, faultstring: {$response->faultstring})";
			$this->message = "{$response->faultcode} {$response->faultstring})";
		}

	}

}
