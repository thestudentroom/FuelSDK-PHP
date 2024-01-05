<?php
// spl_autoload_register( function($class_name) {
//     include_once 'src/'.$class_name.'.php';
// });
namespace FuelSdk;
use \Exception;
/**
 * This class represents the get operation for REST service.
 */
class ET_GetSupportRest extends ET_BaseObjectRest
{
   /**
    * @var      int   The last page number
    */	
	protected $lastPageNumber;

    // method for calling a Fuel API using GET
    /**
    * @return ET_GetRest     Object of type ET_GetRest which contains http status code, response, etc from the GET REST service 
    */    
	public function get()
	{
		$this->authStub->refreshToken();
		$completeURL = $this->authStub->baseUrl . $this->path;
		$additionalQS = array();
		
		if (!is_null($this->props)) {
			foreach ($this->props as $key => $value){
				if (in_array($key,$this->urlProps)){
					$completeURL = str_replace("{{$key}}",$value,$completeURL);					
				} else {
					$additionalQS[$key] = $value;
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
//		echo $additionalQS["access_token"] . "\n";
		// $queryString = http_build_query($additionalQS);		
		// $completeURL = "{$completeURL}?{$queryString}";
		// $response = new ET_GetRest($this->authStub, $completeURL, $queryString);						
		$response = new ET_GetRest($this->authStub, $completeURL, $this->authStub->getAuthToken());
		
		if (property_exists($response->results, 'page')){
			$this->lastPageNumber = $response->results->page;
			$pageSize = $response->results->pageSize;
			
			$count = null;
			if (property_exists($response->results, 'count')){
				$count = $response->results->count;
			} else if (property_exists($response->results, 'totalCount')){
				$count = $response->results->totalCount;
			}

			if ($count && ($count > ($this->lastPageNumber * $pageSize))){
				$response->moreResults = true;
			}
		}

		return $response;
	}

    /**
    * @return ET_GetRest    returns more response from the GET REST service of type ET_GetRest Object
    */
	public function getMoreResults()
	{
	
		$originalPageValue = 1;
		$removePageFromProps = false;		
		
		if ($this->props && array_key_exists($this->props, '$page')) { 
			$originalPageValue = $this->props['page'];
		} else {
			$removePageFromProps = true		;	
		}
		
		if (!$this->props) { 
			$this->props = array();
		}
		
		$this->props['$page'] = $this->lastPageNumber + 1;
	
		$response = $this->get();
		
		if ($removePageFromProps) {
			unset($this->props['$page']);
		} else {
			$this->props['$page'] = $originalPageValue;
		}			
		
		return $response;
	}
}
?>