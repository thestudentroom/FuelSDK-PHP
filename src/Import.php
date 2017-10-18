<?php

namespace MarketingCloud;

/**
 * This class defines a reusable pattern of import options.
 */
class Import extends CUDSupport {

	/*
	 * @var string|null 	 contains last import task ID if available
	 */
	public $lastTaskID;

	/**
	 * Initializes a new instance of the class and sets the obj property of parent.
	 */
	public function __construct() {
		$this->obj = "ImportDefinition";
	}

	/**
	 * This method is used to create/post the instance
	 * @return Post     Object of type Post which contains http status code, response, etc from the POST SOAP service
	 */
	function post() {

		$originalProp = $this->props;

		// If the ID property is specified for the destination then it must be a list import
		if (array_key_exists('DestinationObject', $this->props)) {
			if (array_key_exists('ID', $this->props['DestinationObject'])){
				$this->props['DestinationObject'] = new SoapVar($this->props['DestinationObject'], SOAP_ENC_OBJECT, 'List', "http://exacttarget.com/wsdl/partnerAPI");
			}
		}

		$obj = parent::post();
		$this->props = $originalProp;
		return $obj;

	}

	/**
	 * This method start this import process.
	 * @return Perform     Object of type Perform which contains http status code, response, etc from the Perform SOAP service
	 */
	public function start() {

		$originalProps = $this->props;
		$response = new Perform($this->authStub, $this->obj, 'start', $this->props);
		if ($response->status) {
			$this->lastTaskID = $response->results[0]->Task->ID;
		}
		$this->props = $originalProps;

		return $response;

	}

	/**
	 * This method is used to get Property Definition for a subscriber
	 * @return Get     Object of type Get which contains http status code, response, etc from the GET SOAP service
	 */
	public function status() {
		$this->filter = array('Property' => 'TaskResultID','SimpleOperator' => 'equals','Value' => $this->lastTaskID);
		$response = new Get($this->authStub, 'ImportResultsSummary', array('ImportDefinitionCustomerKey','TaskResultID','ImportStatus','StartDate','EndDate','DestinationID','NumberSuccessful','NumberDuplicated','NumberErrors','TotalRows','ImportType'), $this->filter);
		$this->lastRequestID = $response->request_id;
		return $response;
	}

}
