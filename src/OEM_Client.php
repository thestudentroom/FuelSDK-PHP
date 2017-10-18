<?php

namespace MarketingCloud;

use MarketingCloud\REST\Get;
use MarketingCloud\REST\Put;

/**
 * The class can create and retrieve specific tenant.
 */
class OEM_Client extends Client {

	/**
	 * @param array   $tenantInfo   Dictionary type array which may hold e.g. array('key' => '')
	 */
	public function CreateTenant($tenantInfo) {
		$key = $tenantInfo['key'];
		unset($tenantInfo['key']);
		$additionalQS = array();
		$additionalQS["access_token"] = $this->getAuthToken();
		$queryString = http_build_query($additionalQS);
		$completeURL = "https://www.exacttargetapis.com/provisioning/v1/tenants/{$key}?{$queryString}";
		return new Put($this, $completeURL, $tenantInfo);
	}

	/**
	 * @return GetRest     Object of type GetRest which contains http status code, response, etc from the GET REST service
	 */
	public function GetTenants() {
		$additionalQS = array();
		$additionalQS["access_token"] = $this->getAuthToken();
		$queryString = http_build_query($additionalQS);
		$completeURL = "https://www.exacttargetapis.com/provisioning/v1/tenants/?{$queryString}";
		return new Get($this, $completeURL, $queryString);
	}

}
