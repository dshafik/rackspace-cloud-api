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
	 * Create a new Cloud Server
	 *
	 * @param string $name Server Name
	 * @param Rackspace_Cloud_Servers_Image|int $imageId A Rackspace_Cloud_Servers_Image object or an Image ID
	 * @param Rackspace_Cloud_Servers_Flavor|int $flavorId A Rackspace_Cloud_Servers_Flavor object or a Flavor ID
	 * @return Rackspace_Cloud_Servers_Server
	 */
	public function createServer($name, $imageId, $flavorId)
	{
		require_once 'Rackspace/Cloud/Servers/Server.php';

		$data = array('name' => $name, 'imageId' => $imageId, 'flavorId' => $flavorId);
		$instance = new Rackspace_Cloud_Servers_Server($data);
		return $instance;
	}

	/**
	 * Retrieve a list of Servers
	 *
	 * @param int Server ID
	 * @return array An array of Rackspace_Cloud_Servers_Server objects
	 */
	public function getServers($id = null)
	{
		$http = self::getHttpClient();

		if (is_null($id)) {
			$http->setUri(Rackspace::$server_url . '/servers/detail');
		} else {
			$http->setUri(Rackspace::$server_url . "/servers/$id");
		}
		
		$response = $http->request();

		if ($response->isError()) {
			throw new Rackspace_Exception(Rackspace_Exception::BAD_REQUEST);
		}

		$array = Zend_Json::decode($response->getBody());

		if (isset($array['servers']) && sizeof($array['servers']) == 0 && is_null($id)) {
			return false;
		} else {
			require_once 'Rackspace/Cloud/Servers/Server.php';

			if (!is_null($id)) {
				$array['servers'][0] = $array['server'];
			}

			foreach ($array['servers'] as $server) {
				$servers[] = new Rackspace_Cloud_Servers_Server($server);
			}
		}

		if (!is_null($id)) {
			return $servers[0];
		}
		
		return $servers;
	}

	/**
	 * Retrieve a list of Flavors
	 *
	 * @param int $id Flavor ID
	 * @return array An array of Rackspace_Cloud_Servers_Flavor objects
	 */
	public function getFlavors($id = null)
	{
		$http = self::getHttpClient();
		if (is_null($id)) {
			$http->setUri(Rackspace::$server_url . '/flavors/detail');
		} else {
			$http->setUri(Rackspace::$server_url . "/flavors/$id");
		}
		
		$response = $http->request();
		
		$array = Zend_Json::decode($response->getBody());
		
		if (isset($array['flavors']) && sizeof($array['flavors']) == 0 && is_null($id)) {
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

		if (!is_null($id)) {
			return $flavors[0];
		}
		
		return $flavors;
	}

	/**
	 * Retrieve a list of Images
	 *
	 * @param int $id Image ID
	 * @return array An array of Rackspace_Cloud_Servers_Flavor objects
	 */
	public function getImages($id = null)
	{
		$http = self::getHttpClient();
		if (is_null($id)) {
			$http->setUri(Rackspace::$server_url . '/images/detail');
		} else {
			$http->setUri(Rackspace::$server_url . "/images/$id");
		}

		$response = $http->request();

		if ($response->isError()) {
			throw new Rackspace_Exception(Rackspace_Exception::BAD_REQUEST);
		}

		$array = Zend_Json::decode($response->getBody());

		if (sizeof($array) == 0) {
			return false;
		} else {
			require_once 'Rackspace/Cloud/Servers/Image.php';

			if ($id) {
				$i = $array;
			} else {
				$i = $array['images'];
			}

			foreach ($i as $image) {
				$images[] = new Rackspace_Cloud_Servers_Image($image);
			}
		}

		if (!is_null($id)) {
			return $images[0];
		}

		return $images;
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