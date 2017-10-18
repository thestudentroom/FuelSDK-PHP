<?php

namespace MarketingCloud;

/**
 * The class can get, convert, render, send messages.
 */
class Message_Guide extends CUDSupportRest
{
    /**
    * The constructor will assign endpoint, urlProps, urlPropsRequired fields of parent BaseObjectRest
    */ 
	function __construct()
	{
		$this->endpoint = "https://www.exacttargetapis.com/guide/v1/messages/{id}";
		$this->urlProps = array("id");
		$this->urlPropsRequired = array();
	}

    // method for calling a Fuel API using GET
    /**
    * @return GetRest     Object of type GetRest which contains http status code, response, etc from the GET REST service 
    */    
	function get()
	{
		$origEndpoint = $this->endpoint;
		$origProps = $this->urlProps;
		if (count($this->props) == 0) {
			$this->endpoint = "https://www.exacttargetapis.com/guide/v1/messages/f:@all";
		} elseif (array_key_exists('key',$this->props)){
			$this->endpoint = "https://www.exacttargetapis.com/guide/v1/messages/key:{key}";
			$this->urlProps = array("key");
		}
		$response = parent::get();
		$this->endpoint = $origEndpoint;
		$this->urlProps = $origProps;
		
		return $response;
	}

    // method for calling a Fuel API using POST
    /**
    * @return PostRest     Object of type PostRest which contains http status code, response, etc from the POST REST service 
    */
	function convert()
	{
		$completeURL = "https://www.exacttargetapis.com/guide/v1/messages/convert?access_token=" . $this->authStub->getAuthToken();

		$response = new PostRest($this->authStub, $completeURL, $this->props);
		return $response;
	}

    // method for calling a Fuel API using POST
    /**
    * @return Post     Object of type Post which contains http status code, response, etc from the POST SOAP (not REST) service 
    */	
	function sendProcess()
	{
		$renderMG = new Message_Guide();
		$renderMG->authStub = $this->authStub;
		$renderMG->props = array("id" => $this->props['messageID']);	
		$renderResult = $renderMG->render();
		if(!$renderResult->status){
			return $renderResult;
		}
		
		$html = $renderResult->results->emailhtmlbody;
		$send = array();
		$send["Email"] = array("Subject"=> $this->props['subject'], "HTMLBody"=> $html);
		$send["List"] = array("ID"=> $this->props['listID']);		
		$response = new Post($this->authStub, "Send", $send);
		return $response;
	}

    // method for calling a Fuel API using GET or POST
    /**
    * @return GetRest|PosttRest     Object of type GetRest or PostRest if props field is an array and holds id as a key
    */	
	function render()
	{
		$completeURL = null;
		$response = null;
		
		if (is_array($this->props) && array_key_exists("id", $this->props)) {
			$completeURL = "https://www.exacttargetapis.com/guide/v1/messages/render/{$this->props['id']}?access_token=" . $this->authStub->getAuthToken();
			$response = new GetRest($this->authStub, $completeURL, null);
		} else {
			$completeURL = "https://www.exacttargetapis.com/guide/v1/messages/render?access_token=" . $this->authStub->getAuthToken();
			$response = new PostRest($this->authStub, $completeURL, $this->props);			
		}
		return $response;
	}
}
