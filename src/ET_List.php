<?php

namespace MarketingCloud;

/**
 * This class represents a marketing list of subscribers.
 */
class ET_List extends ET_CUDWithUpsertSupport
{
    /**
    * @var int 		Gets or sets the folder identifier.
    */
	public  $folderId;

	/** 
	* Initializes a new instance of the class and set the property obj, folderProperty and folderMediaType to appropriate values.
	*/		
	function __construct()
	{
		$this->obj = "List";
		$this->folderProperty = "Category";
		$this->folderMediaType = "list";
	}
}
