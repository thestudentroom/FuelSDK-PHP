<?php

namespace MarketingCloud;

use Exception;

/**
 * This class represents the create, update, delete operation for SOAP service.
 */
class CUDSupport extends GetSupport
{

    /**
    * @return Post     Object of type Post which contains http status code, response, etc from the POST SOAP service 
    */
	public function post()
	{
		$originalProps = $this->props;
		if (property_exists($this, 'folderProperty') && !is_null($this->folderProperty) && !is_null($this->folderId)){
			$this->props[$this->folderProperty] = $this->folderId;
		} else if (property_exists($this, 'folderProperty') && !is_null($this->authStub->packageName)){
			if (is_null($this->authStub->packageFolders)) {
				$getPackageFolder = new Folder();
				$getPackageFolder->authStub = $this->authStub;
				$getPackageFolder->props = array("ID", "ContentType");
				$getPackageFolder->filter = array("Property" => "Name", "SimpleOperator" => "equals", "Value" => $this->authStub->packageName);
				$resultPackageFolder = $getPackageFolder->get();
				if ($resultPackageFolder->status){
					$this->authStub->packageFolders = array();
					foreach ($resultPackageFolder->results as $result){
						$this->authStub->packageFolders[$result->ContentType] = $result->ID;
					}	
				} else {
					throw new Exception('Unable to retrieve folders from account due to: '.$resultPackageFolder->message);
				}
			}
			
			if (!array_key_exists($this->folderMediaType,$this->authStub->packageFolders )){
				if (is_null($this->authStub->parentFolders)) {
					$parentFolders = new Folder();
					$parentFolders->authStub = $this->authStub;
					$parentFolders->props = array("ID", "ContentType");
					$parentFolders->filter = array("Property" => "ParentFolder.ID", "SimpleOperator" => "equals", "Value" => "0");
					$resultParentFolders = $parentFolders->get();
					if ($resultParentFolders->status) { 
						$this->authStub->parentFolders = array();
						foreach ($resultParentFolders->results as $result){
							$this->authStub->parentFolders[$result->ContentType] = $result->ID;
						}	
					} else {
						throw new Exception('Unable to retrieve folders from account due to: '.$resultParentFolders->message);
					}
				}
				$newFolder = new Folder();
				$newFolder->authStub = $this->authStub;
				$newFolder->props = array("Name" => $this->authStub->packageName, "Description" => $this->authStub->packageName, "ContentType"=> $this->folderMediaType, "IsEditable"=>"true", "ParentFolder" => array("ID" => $this->authStub->parentFolders[$this->folderMediaType]));
				$folderResult = $newFolder->post();
				if ($folderResult->status) {
					$this->authStub->packageFolders[$this->folderMediaType] = $folderResult->results[0]->NewID;
				} else {
					throw new Exception('Unable to create folder for Post due to: '.$folderResult->message);
				}
			}
			$this->props[$this->folderProperty] = $this->authStub->packageFolders[$this->folderMediaType];
		} 
		
		$response = new Post($this->authStub, $this->obj, $this->props);
		$this->props = $originalProps;
		return $response;
	}

    /**
    * @return Patch     Object of type Patch which contains http status code, response, etc from the PATCH SOAP service 
    */
	public function patch()
	{
		$originalProps = $this->props;
		if (property_exists($this, 'folderProperty') && !is_null($this->folderProperty) && !is_null($this->folderId)){
			$this->props[$this->folderProperty] = $this->folderId;
		} 
		$response = new Patch($this->authStub, $this->obj, $this->props);
		$this->props = $originalProps;
		return $response;
	}
	
    /**
    * @return Delete     Object of type Delete which contains http status code, response, etc from the DELETE SOAP service 
    */ 	
	public function delete()
	{	
		$response = new Delete($this->authStub, $this->obj, $this->props);
		return $response;
	}	
}
