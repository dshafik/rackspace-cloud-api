<?php
/**
 * Rackspace Cloud APIs
 * 
 * @author Davey Shafik <me@daveyshafik.com>
 * @package Rackspace
 */

/**
 * Zend_Http_Client
 */
require_once 'Zend/Http/Client.php';

/**
 * Rackspace_Exception
 */
require_once 'Rackspace/Exception.php';

/**
 * Rackspace Cloud API Base Class
 *
 * @package Rackspace
 */
class Rackspace {
	/**
	 * Cloud Servers ID
	 */
	const SERVICE_CLOUD_SERVERS = 1;
	
	/**
	 * @var string Username
	 */
	static public $username;
	
	/**
	 * @var string API Key
	 */
	static public $api_key;
	
	/**
	 * @var string Authentication Token
	 */
	static public $auth_token;
	
	/**
	 * @var string Storage Authentication Token
	 */
	static public $storage_token;
	
	/**
	 * @var string Storage Management URL
	 */
	static public $storage_url;
	
	/**
	 * @var string Storage CDN URL
	 */
	static public $cdn_url;
	
	/**
	 * @var string Server Management URL
	 */
	static public $server_url;
	
	/**
	 * @var Zend_Http_Client
	 */
	static public $http_client;
	
	/**
	 * @var array Array of Rackspace Cloud API Service singletons
	 */
	static public $singletons = array();

	/**
	 * Get an Instance of a specific cloud service object
	 *
	 * @param int $service One of the Rackspace::SERVICE_* constants
	 * @return object
	 */
	static public function getInstance($service)
	{
		switch ($service) {
			case self::SERVICE_CLOUD_SERVERS:
				require_once 'Rackspace/Cloud/Servers.php';
				if (!isset(self::$singletons[self::SERVICE_CLOUD_SERVERS]) || !(self::$singletons[self::SERVICE_CLOUD_SERVERS] instanceof Rackspace_Cloud_Servers)) {
					self::$singletons[self::SERVICE_CLOUD_SERVERS] = new Rackspace_Cloud_Servers;
				}
				return self::$singletons[self::SERVICE_CLOUD_SERVERS];
			default:
				throw new Rackspace_Exception(Rackspace_Exception::UNKNOWN_SERVICE);
		}
	}
	
	/**
	 * Retrieve HTTP Client Singleton
	 *
	 * @return Zend_Http_Client
	 */
	static public function getHttpClient()
	{
		if (!(self::$http_client instanceof Zend_Http_Client)) {
			self::$http_client = new Zend_Http_Client(null, array('UserAgent' => 'PHP/' .PHP_VERSION. '(Rackspace Cloud API by Davey Shafik)'));
		}
		
		self::$http_client->setHeaders('X-Auth-User', null);
		self::$http_client->setHeaders('X-Auth-Key', null);
		
		return self::$http_client;
	}
	
	/**
	 * Authenticate Against the Rackspace Cloud API
	 *
	 * @return bool
	 * @throws Rackspace_Exception Thrown on authentication errors
	 */
	static public function auth()
	{
		$http = new Zend_Http_Client(null, array('UserAgent' => 'PHP/' .PHP_VERSION. ' (Rackspace Cloud API by Davey Shafik)'));
		$http->setUri('https://auth.api.rackspacecloud.com/v1.0');
		$http->setHeaders(array(
			'X-Auth-User' => self::$username,
			'X-Auth-Key' => self::$api_key,
		));
		$response = $http->request("GET");
		
		if ($response->isError()) {
			throw new Rackspace_Exception(Rackspace_Exception::AUTHENTICATION_ERROR);
		}
		
		self::$auth_token = $response->getHeader('X-auth-token');
		self::$server_url = $response->getHeader('X-server-management-url');
		self::$storage_token = $response->getHeader('X-storage-token');
		self::$storage_url = $response->getHeader('X-storage-url');
		self::$cdn_url = $response->getHeader('X-cdn-management-url');
		
		return true;
	}
}
