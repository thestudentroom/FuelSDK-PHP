<?php

namespace MarketingCloud;

/**
 * This class represents the put operation for SOAP service.
 */
class CUDWithUpsertSupport extends CUDSupport {

	/**
	 * @return Patch     Object of type Patch which contains http status code, response, etc from the PATCH SOAP service
	 */
	public function put() {

		$originalProps = $this->props;
		if (property_exists($this, 'folderProperty') && !is_null($this->folderProperty) && !is_null($this->folderId)){
			$this->props[$this->folderProperty] = $this->folderId;
		}
		$response = new Patch($this->authStub, $this->obj, $this->props, true);
		$this->props = $originalProps;
		return $response;

	}

}
