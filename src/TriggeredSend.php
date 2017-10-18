<?php

namespace MarketingCloud;

/**
* Defines a triggered send in the account.
*/
class TriggeredSend extends CUDSupport
{
	/**
	* @var array    		Gets or sets the subscribers. e.g. array("EmailAddress" => "", "SubscriberKey" => "")
	*/
	public  $subscribers;

	/**
	* @var int      		Gets or sets the folder identifier.
	*/
	public $folderId;
	/** 
	* Initializes a new instance of the class.
	*/
	function __construct()
	{
		$this->obj = "TriggeredSendDefinition";
		$this->folderProperty = "CategoryID";
		$this->folderMediaType = "triggered_send";
	}

    /**
	* Send this instance.
    * @return Post     Object of type Post which contains http status code, response, etc from the POST SOAP service
    */	
	public function Send()
	{
		$tscall = array("TriggeredSendDefinition" => $this->props , "Subscribers" => $this->subscribers);
		$response = new Post($this->authStub, "TriggeredSend", $tscall);
		return $response;
	}
}
