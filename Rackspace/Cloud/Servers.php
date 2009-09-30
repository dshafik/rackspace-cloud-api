<?php
/**
 * Rackspace Cloud Servers API
 * 
 * @author Davey Shafik <me@daveyshafik.com>
 * @package Rackspace
 * @subpackage Rackspace_Cloud_Servers
 */

/**
 * Zend_Json
 */
require_once 'Zend/Json.php';

/**
 * Rackspace Cloud Servers API Class
 * 
 * @package Rackspace
 * @subpackage Rackspace_Cloud_Servers
 */
class Rackspace_Cloud_Servers {
	/**
	 * Retrieve a list of Servers
	 *
	 * @return array An array of Rackspace_Cloud_Server_Instance objects
	 */
	public function getServers()
	{
		$http = self::getHttpClient();
		$http->setUri(Rackspace::$server_url . '/servers');
		
		$response = $http->request();

		$array = Zend_Json::decode($response->getBody());
		
		if (sizeof($array['servers']) == 0) {
			return false;
		} else {
			require_once 'Rackspace/Cloud/Servers/Instance.php';
			foreach ($array['servers'] as $server) {
				$servers[] = new Rackspace_Cloud_Servers_Instance($server);
			}
		}
		
		return $servers;
	}
	
	/**
	 * Retrieve a list of Servers
	 *
	 * @return array An array of Rackspace_Cloud_Server_Instance objects
	 */
	public function getServerDetails()
	{
		$http = self::getHttpClient();
		$http->setUri(Rackspace::$server_url . '/servers/detail');
		
		$response = $http->request();
		
		$array = Zend_Json::decode($response->getBody());
		
		if (sizeof($array) == 0) {
			return false;
		} else {
			require_once 'Rackspace/Cloud/Servers/Instance.php';
			foreach ($array['servers'] as $server) {
				$servers[] = new Rackspace_Cloud_Servers_Instance($server);
			}
		}
		
		return $servers;
	}
	
	public function getFlavorDetails($id = null)
	{
		$http = self::getHttpClient();
		if (is_null($id)) {
			$http->setUri(Rackspace::$server_url . '/flavors/detail');
		} else {
			$http->setUri(Rackspace::$server_url . "/flavors/$id");
		}
		
		$response = $http->request();
		
		$array = Zend_Json::decode($response->getBody());
		
		if (sizeof($array) == 0) {
			return false;
		} else {
			require_once 'Rackspace/Cloud/Servers/Flavor.php';
			
			if ($id) {
				$i = $array;
			} else {
				$i = $array['flavors'];
			}
			
			foreach ($i as $flavor) {
				$flavors[] = new Rackspace_Cloud_Servers_Flavor($flavor);
			}
		}
		
		return $flavors;
	}
	
	/**
	 * Get a pre-setup HTTP Client
	 *
	 * @return Zend_Http_Client
	 */
	static public function getHttpClient()
	{
		$http = Rackspace::getHttpClient();
		$http->setHeaders('X-Auth-Token', Rackspace::$auth_token);
		
		return $http;
	}
}