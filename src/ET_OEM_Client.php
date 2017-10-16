<?php

namespace MarketingCloud;

/**
 * The class can create and retrieve specific tenant.
 */
class ET_OEM_Client extends ET_Client
{
    /**
    * @param array   $tenantInfo   Dictionary type array which may hold e.g. array('key' => '')
    */
	function CreateTenant($tenantInfo)
	{
		$key = $tenantInfo['key'];
		unset($tenantInfo['key']);
		$additionalQS = array();
		$additionalQS["access_token"] = $this->getAuthToken();
		$queryString = http_build_query($additionalQS);		
		$completeURL = "https://www.exacttargetapis.com/provisioning/v1/tenants/{$key}?{$queryString}";
		return new ET_PutRest($this, $completeURL, $tenantInfo);
	}

    /**
    * @return ET_GetRest     Object of type ET_GetRest which contains http status code, response, etc from the GET REST service 
    */
	function GetTenants()
	{
		$additionalQS = array();
		$additionalQS["access_token"] = $this->getAuthToken();
		$queryString = http_build_query($additionalQS);		
		$completeURL = "https://www.exacttargetapis.com/provisioning/v1/tenants/?{$queryString}";
		return new ET_GetRest($this, $completeURL, $queryString);
	}
}
