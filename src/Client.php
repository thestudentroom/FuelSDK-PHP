<?php

namespace MarketingCloud;

use SoapClient;
use Exception;
use DateTime;
use DateInterval;
use stdClass;
use DOMDocument;
use DOMXPath;

use RobRichards\WsePhp\WSSESoap;
use Firebase\JWT;

use MarketingCloud\SOAP\Post;
use MarketingCloud\Util;

/**
* Defines a Client interface class which manages the authentication process.
* This is the main client class which performs authentication, obtains auth token, if expired refresh auth token.
* Settings/Configuration can be passed to this class during construction or set it in config.php file.
* Configuration passed as parameter overrides the values from the configuration file.
*
*/
class Client extends SoapClient {

	/**
	 * @var string $packageName Folder/Package Name
	 */
	public $packageName;

	/**
	 * @var array $packageFolders Array of Folder object properties.
	 */
	public $packageFolders;

	/**
	 * @var Folder Parent folder object.
	 */
	public $parentFolders;

	/**
	 * @var string Proxy host.
	 */
	public $proxyHost;

	/**
	 * @var string Proxy port.
	 */
	public $proxyPort;

	/**
	 * @var string Proxy username.
	 */
	public $proxyUserName;

	/**
	 * @var string Proxy password.
	 */
	public $proxyPassword;

	protected $http;

	private $wsdlLoc, $debugSOAP, $lastHTTPCode, $clientId,
			$clientSecret, $appsignature, $endpoint,
			$tenantTokens, $tenantKey, $xmlLoc,$baseUrl, $baseAuthUrl;

	/**
	 * Initializes a new instance of the Client class.
	 *
	 * @param boolean $getWSDL Flag to indicate whether to load WSDL from source.
	 * 	If true, WSDL is load from the source and saved in to path set in xmlLoc variable.
	 * If false, WSDL stored in the path set in xmlLoc is loaded.
	 * @param boolean $debug Flag to indicate whether debug information needs to be logged.
	 * Logging is enabled when the value is set to true and disabled when set to false.
	 * @param array $params Array of settings as string.</br>
	 * <b>Following are the possible settings.</b></br>
	 * <i><b>defaultwsdl</b></i> - WSDL location/path</br>
	 * <i><b>clientid</b></i> - Client Identifier optained from App Center</br>
	 * <i><b>clientsecred</b></i> - Client secret associated with clientid</br>
	 * <i><b>appsignature</b></i> - Application signature optained from App Center</br>
	 * <i><b>baseUrl</b></i> - ExactTarget SOAP API Url</br>
	 * <i><b>baseAuthUrl</b></i> - ExactTarget authentication rest api resource url</br>
	 * <b>If your application behind a proxy server, use the following setting</b></br>
	 * <i><b>proxyhost</b></i> - proxy server host name or ip address</br>
	 * <i><b>proxyport</b></i> - proxy server prot number</br>
	 * <i><b>proxyusername</b></i> - proxy server user name</br>
	 * <i><b>proxypassword</b></i> - proxy server password</br>
	 */
	public function __construct($getWSDL = false, $debug = false, $params = null) {

		$tenantTokens = array();
		$config = false;

		$this->xmlLoc = 'ExactTargetWSDL.xml';

		if (file_exists(realpath(__DIR__ . "/config.php")))
			$config = include 'config.php';

		if ($config) {
			$this->wsdlLoc = $config['defaultwsdl'];
			$this->clientId = $config['clientid'];
			$this->clientSecret = $config['clientsecret'];
			$this->appsignature = $config['appsignature'];
			$this->baseUrl = $config["baseUrl"];
			$this->baseAuthUrl = $config["baseAuthUrl"];
			if (array_key_exists('xmlloc', $config)){$this->xmlLoc = $config['xmlloc'];}

			if(array_key_exists('proxyhost', $config)){$this->proxyHost = $config['proxyhost'];}
			if (array_key_exists('proxyport', $config)){$this->proxyPort = $config['proxyport'];}
			if (array_key_exists('proxyusername', $config)){$this->proxyUserName = $config['proxyusername'];}
			if (array_key_exists('proxypassword', $config)){$this->proxyPassword = $config['proxypassword'];}
		}
		if ($params) {
			if ($params && array_key_exists('defaultwsdl', $params)){$this->wsdlLoc = $params['defaultwsdl'];}
			else {$this->wsdlLoc = "https://webservice.exacttarget.com/etframework.wsdl";}
			if ($params && array_key_exists('clientid', $params)){$this->clientId = $params['clientid'];}
			if ($params && array_key_exists('clientsecret', $params)){$this->clientSecret = $params['clientsecret'];}
			if ($params && array_key_exists('appsignature', $params)){$this->appsignature = $params['appsignature'];}
			if ($params && array_key_exists('xmlloc', $params)){$this->xmlLoc = $params['xmlloc'];}

			if ($params && array_key_exists('proxyhost', $params)){$this->proxyHost = $params['proxyhost'];}
			if ($params && array_key_exists('proxyport', $params)){$this->proxyPort = $params['proxyport'];}
			if ($params && array_key_exists('proxyusername', $params)) {$this->proxyUserName = $params['proxyusername'];}
			if ($params && array_key_exists('proxypassword', $params)) {$this->proxyPassword = $params['proxypassword'];}
			if ($params && array_key_exists('baseUrl', $params)) {
				$this->baseUrl = $params['baseUrl'];
			}
			else {
				$this->baseUrl = "https://www.exacttargetapis.com";
			}
			if ($params && array_key_exists('baseAuthUrl', $params)) {
				$this->baseAuthUrl = $params['baseAuthUrl'];
			}
			else {
				$this->baseAuthUrl = "https://auth.exacttargetapis.com";
			}
		}

		$this->debugSOAP = $debug;

		if (!property_exists($this,'clientId') || is_null($this->clientId) || !property_exists($this,'clientSecret') || is_null($this->clientSecret)){
			throw new Exception('clientid or clientsecret is null: Must be provided in config file or passed when instantiating Client');
		}

		$this->http = new HTTP([
			'proxy_host' => $this->proxyHost,
			'proxy_port' => $this->proxyPort,
			'proxy_user' => $this->proxyUserName,
			'proxy_pass' => $this->proxyPassword,
		]);

		if ($getWSDL){
			$this->CreateWSDL($this->wsdlLoc);
		}

		if ($params && array_key_exists('jwt', $params)) {
			if (!property_exists($this,'appsignature') || is_null($this->appsignature)) {
				throw new Exception('Unable to utilize JWT for SSO without appsignature: Must be provided in config file or passed when instantiating Client');
			}
			$decodedJWT = JWT::decode($params['jwt'], $this->appsignature);
			$dv = new DateInterval('PT'.$decodedJWT->request->user->expiresIn.'S');
			$newexpTime = new DateTime();
			$this->setAuthToken($this->tenantKey, $decodedJWT->request->user->oauthToken, $newexpTime->add($dv));
			$this->setInternalAuthToken($this->tenantKey, $decodedJWT->request->user->internalOauthToken);
			$this->setRefreshToken($this->tenantKey, $decodedJWT->request->user->refreshToken);
			$this->packageName = $decodedJWT->request->application->package;
		}

		$this->refreshToken();

		try {
			$url = $this->baseUrl."/platform/v1/endpoints/soap?access_token=".$this->getAuthToken($this->tenantKey);
			$endpointResponse = $this->http->get($url);
			$endpointObject = json_decode($endpointResponse->body);
			if ($endpointObject && property_exists($endpointObject,"url")) {
				$this->endpoint = $endpointObject->url;
			}
			else {
				throw new Exception('Unable to determine stack using /platform/v1/endpoints/:'.$endpointResponse->body);
			}
		}
		catch (Exception $e) {
			throw new Exception('Unable to determine stack using /platform/v1/endpoints/: '.$e->getMessage());
		}

		$soapOptions = array(
			'trace'=>1,
			'exceptions'=>0,
			'connection_timeout'=>120,
		);
		if (!empty($this->proxyHost)) {
			$soapOptions['proxy_host'] = $this->proxyHost;
		}
		if (!empty($this->proxyPort)) {
			$soapOptions['proxy_port'] = $this->proxyPort;
		}
		if (!empty($this->proxyUserName)) {
			$soapOptions['proxy_username'] = $this->proxyUserName;
		}
		if (!empty($this->proxyPassword)) {
			$soapOptions['proxy_password'] = $this->proxyPassword;
		}
		parent::__construct($this->xmlLoc, $soapOptions);

		parent::__setLocation($this->endpoint);

	}

	public function getHTTP() {
		return $this->http;
	}

	/**
	 * Gets the refresh token using the authentication URL.
	 *
	 * @param boolean $forceRefresh Flag to indicate a force refresh of authentication toekn.
	 * @return void
	 */
	public function refreshToken($forceRefresh = false) {

		if (property_exists($this, "sdl") && $this->sdl == 0){
			parent::__construct($this->xmlLoc, array('trace'=>1, 'exceptions'=>0));
		}
		try {
			$currentTime = new DateTime();
			if (is_null($this->getAuthTokenExpiration($this->tenantKey))){
				$timeDiff = 0;
			} else {
				$timeDiff = $currentTime->diff($this->getAuthTokenExpiration($this->tenantKey))->format('%i');
				$timeDiff = $timeDiff  + (60 * $currentTime->diff($this->getAuthTokenExpiration($this->tenantKey))->format('%H'));
			}
			if (is_null($this->getAuthToken($this->tenantKey)) || ($timeDiff < 5) || $forceRefresh  ){

				$url = $this->tenantKey == null
						? $this->baseAuthUrl."/v1/requestToken?legacy=1"
						: $this->baseUrl."/provisioning/v1/tenants/{$this->tenantKey}/requestToken?legacy=1";

				$jsonRequest = new stdClass();
				$jsonRequest->clientId = $this->clientId;
				$jsonRequest->clientSecret = $this->clientSecret;
				$jsonRequest->accessType = "offline";
				if (!is_null($this->getRefreshToken($this->tenantKey))){
					$jsonRequest->refreshToken = $this->getRefreshToken($this->tenantKey);
				}
				$authResponse = $this->http->post(
					$url,
					json_encode($jsonRequest),
					[
						'User-Agent'   => Util::getSDKVersion(),
						'Content-Type' => 'application/json',
					]
				);
				$authObject = json_decode($authResponse->body);

				if ($authResponse && property_exists($authObject,"accessToken")){
					$dv = new DateInterval('PT'.$authObject->expiresIn.'S');
					$newexpTime = new DateTime();
					$this->setAuthToken($this->tenantKey, $authObject->accessToken, $newexpTime->add($dv));
					$this->setInternalAuthToken($this->tenantKey, $authObject->legacyToken);
					if (property_exists($authObject,'refreshToken')){
						$this->setRefreshToken($this->tenantKey, $authObject->refreshToken);
					}
				} else {
					throw new Exception('Unable to validate App Keys(ClientID/ClientSecret) provided, requestToken response:'.$authResponse->body );
				}
			}
		} catch (Exception $e) {
			throw new Exception('Unable to validate App Keys(ClientID/ClientSecret) provided.: '.$e->getMessage());
		}
	}

	/**
	 * Returns the  HTTP code return by the last SOAP/Rest call
	 *
	 * @return lastHTTPCode
	 */
	public function __getLastResponseHTTPCode() {
		return $this->lastHTTPCode;
	}

	/**
	 * Create the WSDL file at specified location.
	 * @param  string 		location or path of the WSDL file to be created.
	 * @return void
	 */
	public function CreateWSDL($wsdlLoc) {
		try{

			$getNewWSDL = true;

			$remoteTS = $this->GetLastModifiedDate($wsdlLoc);
			if (file_exists($this->xmlLoc)) {
				$localTS = filemtime($this->xmlLoc);
				if ($remoteTS <= $localTS) {
					$getNewWSDL = false;
				}
			}
			if ($getNewWSDL) {
				$newWSDL = file_get_contents($wsdlLoc);
				file_put_contents($this->xmlLoc, $newWSDL);
			}

		}
		catch (Exception $e) {
			throw new Exception('Unable to store local copy of WSDL file:'.$e->getMessage()."\n");
		}
	}

	/**
	 * Returns last modified date of the URL
	 *
	 * @param [type] $remotepath
	 * @return string Last modified date
	 */
	public function GetLastModifiedDate($remotepath) {

		return $this->http->getLastModifiedDate($remotepath);

	}

	/**
	 * Perfoms an soap request.
	 *
	 * @param string $request Soap request xml
	 * @param string $location Url as string
	 * @param string $saction Soap action name
	 * @param string $version Future use
	 * @param integer $one_way Future use
	 * @return string Soap web service request result
	 */
	public function __doRequest($request, $location, $saction, $version, $one_way = 0) {

		$doc = new DOMDocument();
		$doc->loadXML($request);
		$objWSSE = new WSSESoap($doc);
		$objWSSE->addUserToken("*", "*", FALSE);
		$this->addOAuth($doc, $this->getInternalAuthToken($this->tenantKey));

		$content = $objWSSE->saveXML();
		$content_length = strlen($content);
		if ($this->debugSOAP){
			error_log ('FuelSDK SOAP Request: ');
			error_log (str_replace($this->getInternalAuthToken($this->tenantKey),"REMOVED",$content));
		}

		$response = $this->http->post(
			$location,
			$content,
			[
				'Content-Type' => 'text/xml',
				'SOAPAction'   => $saction,
				'User-Agent'   => Util::getSDKVersion(),
			]
		);

		$this->lastHTTPCode = $response->status;

		return $response->body;

	}

	/**
	 * Add OAuth token to the header of the soap request
	 *
	 * @param string $doc Soap request as xml string
	 * @param string $token OAuth token
	 * @return void
	 */
	public function addOAuth( $doc, $token) {

		$soapDoc = $doc;
		$envelope = $doc->documentElement;
		$soapNS = $envelope->namespaceURI;
		$soapPFX = $envelope->prefix;
		$SOAPXPath = new DOMXPath($doc);
		$SOAPXPath->registerNamespace('wssoap', $soapNS);
		$SOAPXPath->registerNamespace('wswsse', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd');

		$headers = $SOAPXPath->query('//wssoap:Envelope/wssoap:Header');
		$header = $headers->item(0);
		if (! $header) {
			$header = $soapDoc->createElementNS($soapNS, $soapPFX.':Header');
			$envelope->insertBefore($header, $envelope->firstChild);
		}

		$authnode = $soapDoc->createElementNS('http://exacttarget.com', 'oAuth');
		$header->appendChild($authnode);

		$oauthtoken = $soapDoc->createElementNS(null,'oAuthToken',$token);
		$authnode->appendChild($oauthtoken);

	}

	/**
	 * Get the authentication token.
	 * @return string
	 */
	public function getAuthToken($tenantKey = null) {

		$tenantKey = $tenantKey == null ? $this->tenantKey : $tenantKey;
		if ($this->tenantTokens[$tenantKey] == null) {
			$this->tenantTokens[$tenantKey] = array();
		}
		return isset($this->tenantTokens[$tenantKey]['authToken'])
			? $this->tenantTokens[$tenantKey]['authToken']
			: null;

	}

	/**
	 * Set the authentication token in the tenantTokens array.
	 * @param  string $tenantKey Tenant key for which auth toke to be set
	 * @param  string $authToken Authentication token to be set
	 * @param  string $authTokenExpiration Authentication token expiration value
	 */
	public function setAuthToken($tenantKey, $authToken, $authTokenExpiration) {
		if ($this->tenantTokens[$tenantKey] == null) {
			$this->tenantTokens[$tenantKey] = array();
		}
		$this->tenantTokens[$tenantKey]['authToken'] = $authToken;
		$this->tenantTokens[$tenantKey]['authTokenExpiration'] = $authTokenExpiration;
	}

	/**
	 * Get the Auth Token Expiration.
	 * @param  string $tenantKey Tenant key for which authenication token is returned
	 * @return string Authenticaiton token for the tenant key
	 */
	public function getAuthTokenExpiration($tenantKey) {
		$tenantKey = $tenantKey == null ? $this->tenantKey : $tenantKey;
		if ($this->tenantTokens[$tenantKey] == null) {
			$this->tenantTokens[$tenantKey] = array();
		}
		return isset($this->tenantTokens[$tenantKey]['authTokenExpiration'])
			? $this->tenantTokens[$tenantKey]['authTokenExpiration']
			: null;
	}

	/**
	 * Get the internal authentication token.
	 * @param  string $tenantKey
	 * @return string Internal authenication token
	 */
	public function getInternalAuthToken($tenantKey) {
		$tenantKey = $tenantKey == null ? $this->tenantKey : $tenantKey;
		if ($this->tenantTokens[$tenantKey] == null) {
			$this->tenantTokens[$tenantKey] = array();
		}
		return isset($this->tenantTokens[$tenantKey]['internalAuthToken'])
			? $this->tenantTokens[$tenantKey]['internalAuthToken']
			: null;
	}

	/**
	 * Set the internal auth tokan.
	 * @param  string $tenantKey
	 * @param string $internalAuthToken
	 */
	public function setInternalAuthToken($tenantKey, $internalAuthToken) {
		if ($this->tenantTokens[$tenantKey] == null) {
			$this->tenantTokens[$tenantKey] = array();
		}
		$this->tenantTokens[$tenantKey]['internalAuthToken'] = $internalAuthToken;
	}

	/**
	 * Set the refresh authentication token.
	 * @param  string $tenantKey Tenant key to which refresh token is set
	 * @param  string $refreshToken Refresh authenication token
	 */
	public function setRefreshToken($tenantKey, $refreshToken) {
		if ($this->tenantTokens[$tenantKey] == null) {
			$this->tenantTokens[$tenantKey] = array();
		}
		$this->tenantTokens[$tenantKey]['refreshToken'] = $refreshToken;
	}

	/**
	 * Get the refresh token for the tenant.
	 *
	 * @param string $tenantKey
	 * @return string Refresh token for the tenant
	 */
	public function getRefreshToken($tenantKey) {
		$tenantKey = $tenantKey == null ? $this->tenantKey : $tenantKey;
		if ($this->tenantTokens[$tenantKey] == null) {
			$this->tenantTokens[$tenantKey] = array();
		}
		return isset($this->tenantTokens[$tenantKey]['refreshToken'])
			? $this->tenantTokens[$tenantKey]['refreshToken']
			: null;
	}

	/**
	 * Add subscriber to list.
	 *
	 * @param string $emailAddress Email address of the subscriber
	 * @param array $listIDs Array of list id to which the subscriber is added
	 * @param string $subscriberKey Newly added subscriber key
	 * @return mixed post or patch response object. If the subscriber already existing patch response is returned otherwise post response returned.
	 */
	public function AddSubscriberToList($emailAddress, $listIDs, $subscriberKey = null) {

		$newSub = new Subscriber;
		$newSub->authStub = $this;
		$lists = array();

		foreach ($listIDs as $key => $value){
			$lists[] = array("ID" => $value);
		}

		//if (is_string($emailAddress)) {
			$newSub->props = array("EmailAddress" => $emailAddress, "Lists" => $lists);
			if ($subscriberKey != null ){
				$newSub->props['SubscriberKey']  = $subscriberKey;
			}

		// Try to add the subscriber
		$postResponse = $newSub->post();
		if ($postResponse->status == false) {
			// If the subscriber already exists in the account then we need to do an update.
			// Update Subscriber On List
			if ($postResponse->results[0]->ErrorCode == "12014") {
				$patchResponse = $newSub->patch();
				return $patchResponse;
			}
		}

		return $postResponse;

	}

	public function AddSubscribersToLists($subs, $listIDs) {

		//Create Lists
		foreach ($listIDs as $key => $value){
			$lists[] = array("ID" => $value);
		}

		for ($i = 0; $i < count($subs); $i++) {
			$copyLists = array();
			foreach ($lists as $k => $v) {
				$NewProps = array();
				foreach($v as $prop => $value) {
					$NewProps[$prop] = $value;
				}
				$copyLists[$k] = $NewProps;
			}
			$subs[$i]["Lists"] = $copyLists;
		}

		$response = new Post($this, "Subscriber", $subs, true);

		return $response;

	}

	/**
	 * Create a new data extension based on the definition passed
	 *
	 * @param array $dataExtensionDefinitions Data extension definition properties as an array
	 * @return mixed post response object
	 */
	public function CreateDataExtensions($dataExtensionDefinitions) {
		$newDEs = new DataExtension();
		$newDEs->authStub = $this;
		$newDEs->props = $dataExtensionDefinitions;
		$postResponse = $newDEs->post();
		return $postResponse;
	}

	/**
	 * Starts an send operation for the TriggerredSend records
	 *
	 * @param array $arrayOfTriggeredRecords Array of TriggeredSend records
	 * @return mixed Send reponse object
	 */
	public function SendTriggeredSends($arrayOfTriggeredRecords) {
		$sendTS = new TriggeredSend();
		$sendTS->authStub = $this;
		$sendTS->props = $arrayOfTriggeredRecords;
		$sendResponse = $sendTS->send();
		return $sendResponse;
	}

	/**
	 * Create an email send definition, send the email based on the definition and delete the definition.
	 *
	 * @param string $emailID Email identifier for which the email is sent
	 * @param string $listID Send definition list identifier
	 * @param string $sendClassficationCustomerKey Send classification customer key
	 * @return mixed Final delete action result
	 */
	public function SendEmailToList($emailID, $listID, $sendClassficationCustomerKey) {

		$email = new Email_SendDefinition();
		$email->props = array("Name"=> uniqid(), "CustomerKey"=>uniqid(), "Description"=>"Created with FuelSDK");
		$email->props["SendClassification"] = array("CustomerKey"=>$sendClassficationCustomerKey);
		$email->props["SendDefinitionList"] = array("List"=> array("ID"=>$listID), "DataSourceTypeID"=>"List");
		$email->props["Email"] = array("ID"=>$emailID);
		$email->authStub = $this;
		$result = $email->post();

		if ($result->status) {
			$sendresult = $email->send();
			if ($sendresult->status) {
				$deleteresult = $email->delete();
				return $sendresult;
			}
			else {
				throw new Exception("Unable to send using send definition due to: ".print_r($result,true));
			}
		}
		else {
			throw new Exception("Unable to create send definition due to: ".print_r($result,true));
		}

	}

	/**
	 * Create an email send definition, send the email based on the definition and delete the definition.
	 *
	 * @param string $emailID Email identifier for which the email is sent
	 * @param string $sendableDataExtensionCustomerKey Sendable data extension customer key
	 * @param string $sendClassficationCustomerKey Send classification customer key
	 * @return mixed Final delete action result
	 */
	public function SendEmailToDataExtension($emailID, $sendableDataExtensionCustomerKey, $sendClassficationCustomerKey) {

		$email = new Email_SendDefinition();
		$email->props = array("Name"=>uniqid(), "CustomerKey"=>uniqid(), "Description"=>"Created with FuelSDK");
		$email->props["SendClassification"] = array("CustomerKey"=> $sendClassficationCustomerKey);
		$email->props["SendDefinitionList"] = array("CustomerKey"=> $sendableDataExtensionCustomerKey, "DataSourceTypeID"=>"CustomObject");
		$email->props["Email"] = array("ID"=>$emailID);
		$email->authStub = $this;
		$result = $email->post();
		if ($result->status) {
			$sendresult = $email->send();
			if ($sendresult->status) {
				$deleteresult = $email->delete();
				return $sendresult;
			}
			else {
				throw new Exception("Unable to send using send definition due to:".print_r($result,true));
			}
		}
		else {
			throw new Exception("Unable to create send definition due to: ".print_r($result,true));
		}

	}
	/**
	 * Create an import definition and start the import process
	 *
	 * @param string $listId List identifier. Used as the destination object identifier.
	 * @param string $fileName Name of the file to be imported
	 * @return mixed Returns the import process result
	 */
	public function CreateAndStartListImport($listId,$fileName) {

		$import = new Import();
		$import->authStub = $this;
		$import->props = array("Name"=> "SDK Generated Import ".uniqid());
		$import->props["CustomerKey"] = uniqid();
		$import->props["Description"] = "SDK Generated Import";
		$import->props["AllowErrors"] = "true";
		$import->props["DestinationObject"] = array("ID"=>$listId);
		$import->props["FieldMappingType"] = "InferFromColumnHeadings";
		$import->props["FileSpec"] = $fileName;
		$import->props["FileType"] = "CSV";
		$import->props["RetrieveFileTransferLocation"] = array("CustomerKey"=>"ExactTarget Enhanced FTP");
		$import->props["UpdateType"] = "AddAndUpdate";
		$result = $import->post();

		if ($result->status) {
			return $import->start();
		} else {
			throw new Exception("Unable to create import definition due to: ".print_r($result,true));
		}

	}

	/**
	 * Create an import definition and start the import process
	 *
	 * @param string $dataExtensionCustomerKey Data extension customer key. Used as the destination object identifier.
	 * @param string $fileName Name of the file to be imported
	 * @param bool $overwrite Flag to indicate to overwrite the uploaded file
	 * @return mixed Returns the import process result
	 */
	public function CreateAndStartDataExtensionImport($dataExtensionCustomerKey, $fileName, $overwrite) {

		$import = new Import();
		$import->authStub = $this;
		$import->props = array("Name"=> "SDK Generated Import ".uniqid());
		$import->props["CustomerKey"] = uniqid();
		$import->props["Description"] = "SDK Generated Import";
		$import->props["AllowErrors"] = "true";
		$import->props["DestinationObject"] = array("CustomerKey"=>$dataExtensionCustomerKey);
		$import->props["FieldMappingType"] = "InferFromColumnHeadings";
		$import->props["FileSpec"] = $fileName;
		$import->props["FileType"] = "CSV";
		$import->props["RetrieveFileTransferLocation"] = array("CustomerKey"=>"ExactTarget Enhanced FTP");
		if ($overwrite) {
			$import->props["UpdateType"] = "Overwrite";
		}
		else {
			$import->props["UpdateType"] = "AddAndUpdate";
		}

		$result = $import->post();

		if ($result->status) {
			return $import->start();
		}
		else {
			throw new Exception("Unable to create import definition due to: ".print_r($result,true));
		}

	}

	/**
	 * Create a profile attribute
	 *
	 * @param array $allAttributes Profile attribute properties as an array.
	 * @return mixed Post operation result
	 */
	public function CreateProfileAttributes($allAttributes) {
		$attrs = new ProfileAttribute();
		$attrs->authStub = $this;
		$attrs->props = $allAttributes;
		return $attrs->post();
	}

	/**
	 * Create one or more content areas
	 *
	 * @param array $arrayOfContentAreas Content areas properties as an array
	 * @return void
	 */
	public function CreateContentAreas($arrayOfContentAreas) {
		$postC = new ContentArea();
		$postC->authStub = $this;
		$postC->props = $arrayOfContentAreas;
		$sendResponse = $postC->post();
		return $sendResponse;
	}

}
