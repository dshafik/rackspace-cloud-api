<?php
/**
 * Rackspace Cloud Servers Instance
 * 
 * @author Davey Shafik <me@daveyshafik.com>
 * @package Rackspace
 * @subpackage Rackspace_Cloud_Servers
 */

/**
 * Rackspace_Cloud_Servers_Abstract
 */
require_once 'Rackspace/Cloud/Servers/Abstract.php';

/**
 * Rackspace_Json_Int
 */
require_once 'Rackspace/Json/Int.php';

/**
 * Rackspace Cloud Servers Instance
 * 
 * @package Rackspace
 * @subpackage Rackspace_Cloud_Servers
 */
class Rackspace_Cloud_Servers_Server extends Rackspace_Cloud_Servers_Abstract implements Rackspace_Json_Int {
	/**
	 * @var string Progress
	 */
	protected $progress;
	
	/**
	 * @var int Cloud Server ID
	 */
	protected $id;
	
	/**
	 * @var int Image ID
	 */
	protected $imageId;
	
	/**
	 * @var int Flavor ID
	 */
	protected $flavorId;
	
	/**
	 * @var string Status
	 */
	protected $status;
	
	/**
	 * @var string Cloud Name
	 */
	protected $name;
	
	/**
	 * @var string Host ID
	 */
	protected $hostId;
	
	/**
	 * @var array A multi-dimensional array of public and private IPs
	 */
	protected $addresses;
	
	/**
	 * @var array An array of metadata
	 */
	protected $metadata;

	/**
	 * @var array An array of custom files when creating a new instance
	 */
	protected $personality;

	/**
	 * @var array The original data used to create the instance
	 */
	protected $originalData;

	/**
	 * @var string The admin password
	 */
	protected $adminPass;

	const FILE_CONTENTS = 0;
	const FILE_PATH = 1;

	/**
	 * Constructor
	 *
	 * @param array $data Constructor
	 */
	public function __construct($data)
	{
		$this->originalData = $data;
		parent::__construct($data);
	}

	/**
	 * Get Private and Public IPs
	 *
	 * Will also update the IPs associated with the instance
	 * 
	 * @return array
	 */
	public function getIPs()
	{
		$http = Rackspace_Cloud_Servers::getHttpClient();
		$http->setUri(Rackspace::$server_url . "/servers/$this->id/ips");

		$response = $http->request("GET");
		
		/* @var $response Zend_Http_Client_Reponse */
		if ($response->isError()) {
			$data = Zend_Json::decode($response->getBody());
			throw new Rackspace_Cloud_Servers_Fault($data);
		}
		
		$addr = Zend_Json::decode($response->getBody());
		$this->addresses = $addr['addresses'];
		return $this->addresses;
	}
	
	/**
	 * Get Public IPs
	 *
	 * Will also update the IPs associated with the instance
	 * 
	 * @return array
	 */
	public function getPublicIPs()
	{
		$http = Rackspace_Cloud_Servers::getHttpClient();
		$http->setUri(Rackspace::$server_url . "/servers/$this->id/ips/public");
		
		$response = $http->request("GET");

		/* @var $response Zend_Http_Client_Reponse */
		if ($response->isError()) {
			$data = Zend_Json::decode($response->getBody());
			throw new Rackspace_Cloud_Servers_Fault($data);
		}
		
		$decoded = Zend_Json::decode($response->getBody());
		$this->addresses['public'] = $decoded['public'];
		
		return $decoded['public'];
	}
	
	/**
	 * Get Private IPs
	 * 
	 * Will also update the IPs associated with the instance
	 *
	 * @return array
	 */
	public function getPrivateIPs()
	{
		$http = Rackspace_Cloud_Servers::getHttpClient();
		$http->setUri(Rackspace::$server_url . "/servers/$this->id/ips/private");
		
		$response = $http->request("GET");

		/* @var $response Zend_Http_Client_Reponse */
		if ($response->isError()) {
			$data = Zend_Json::decode($response->getBody());
			throw new Rackspace_Cloud_Servers_Fault($data);
		}
		
		$decoded = Zend_Json::decode($response->getBody());
		$this->addresses['private'] = $decoded['private'];
		
		return $decoded['private'];
	}

	/**
	 * Get Current Instance Flavor
	 * @return Rackspace_Cloud_Servers_Flavor
	 */
	public function getFlavor()
	{
		if (is_null($this->flavorId)) {
			throw new Rackspace_Exception(Rackspace_Exception::INCOMPLETE_SERVER);
		}
		return $this->flavorId;
	}

	/**
	 * Save the server
	 *
	 * If this is a new instance, it will be created,
	 * otherwise the server name and/or password will
	 * be updated, if they have changed.
	 *
	 * @return bool
	 */
	public function save()
	{
		if ($this->isNew()) {
			$json = Zend_Json::encode($this);

			$http = Rackspace_Cloud_Servers::getHttpClient();
			$http->setUri(Rackspace::$server_url . "/servers");
			$http->setRawData($json);

			$http->setEncType("application/json");

			$response = $http->request("POST");

			/* @var $response Zend_Http_Client_Reponse */
			if ($response->isError()) {
				$data = Zend_Json::decode($response->getBody());
				throw new Rackspace_Cloud_Servers_Fault($data);
			}

			if ($response->isSuccessful()) {
				$array = Zend_Json::decode($response->getBody());
				// Setup this object now we have all our data
				$this->__construct($array['server']);
				return true;
			}
		} else {
			$body = new Rackspace_Json_Container();
			$body->server = new Rackspace_Json_Container();
			if (isset($this->adminPass) && $this->originalData['adminPass'] != $this->adminPass) {
				$body->server->adminPass = $this->adminPass;
			}
			if (isset($this->name) && $this->originalData['name'] != $this->name) {
				$body->server->name = $this->name;
			}

			$json = Zend_Json::encode($body);

			$http = Rackspace_Cloud_Servers::getHttpClient();
			$http->setUri(Rackspace::$server_url . "/servers/$this->id");

			$http->setRawData($json);

			$http->setEncType("application/json");

			$response = $http->request("PUT");

			/* @var $response Zend_Http_Client_Reponse */
			if ($response->isError()) {
				$data = Zend_Json::decode($response->getBody());
				throw new Rackspace_Cloud_Servers_Fault($data);
			}

			if ($response->isSuccessful()) {
				return true;
			}
		}
	}

	/**
	 * Delete Server
	 *
	 * @return bool
	 */
	public function delete()
	{
		$http = Rackspace_Cloud_Servers::getHttpClient();
		$http->setUri(Rackspace::$server_url . "/servers/$this->id");

		$response = $http->request('DELETE');

		/* @var $response Zend_Http_Client_Reponse */
		if ($response->isError()) {
			$data = Zend_Json::decode($response->getBody());
			throw new Rackspace_Cloud_Servers_Fault($data);
		}

		if ($response->isSuccessful()) {
			return true;
		}
	}

	/**
	 * Add metadata to this instance
	 *
	 * @param string $name Metadata name
	 * @param string $value Metadata vaule
	 */
	public function addMetadata($name, $value)
	{
		if (!$this->isNew()) {
			throw new Rackspace_Exception(Rackspace_Exception::SERVER_ALREADY_EXISTS);
		}

		if (strlen($value) > 255) {
			throw new Rackspace_Exception(Rackspace_Exception::METADATA_TOO_LARGE);
		}

		if (strlen($name) > 255) {
			throw new Rackspace_Exception(Rackspace_Exception::METADATA_NAME_TOO_LARGE);
		}

		if (sizeof($this->metadata) == 5) {
			throw new Rackspace_Exception(Rackspace_Exception::METADATA_LIMIT_REACHED);
		}

		$this->metadata[$name] = $value;
	}

	/**
	 * Remove metadata from this instance
	 *
	 * @param string $name Metadata name
	 */
	public function removeMetadata($name)
	{
		unset($this->metadata[$name]);
	}

	/**
	 * Add file to this server
	 *
	 * This is only possible when creating
	 * a new server.
	 *
	 * @param string $path Server file path
	 * @param string $contents File contents or path, file must be under 10KB (will be base64 encoded when sent)
	 * @param bool $autodetect Whether to auto-detect if a file path or file contents was passed. You may pass Rackspace_Cloud_Servers_Server::FILE_CONTENTS/FILE_PATH to force either way.
	 */
	public function addFile($path, $contents, $autodetect = true)
	{
		if (!$this->isNew()) {
			throw new Rackspace_Exception(Rackspace_Exception::SERVER_ALREADY_EXISTS);
		}

		if ($autodetect && file_exists($contents)) {
			// Auto-detected or explict file, grab the file contents
			$contents = file_get_contents($contents);
		} elseif ($autodetect === 1) {
			// File doesn't exist and should
			throw new Rackspace_Exception(Rackspace_Exception::FILE_NOT_FOUND);
		} /* else {
			//File contents were passed in, no need to do anything
		}*/

		if (strlen($contents) > 10*1024) {
			throw new Rackspace_Exception(Rackspace_Exception::FILE_TOO_LARGE);
		}

		if (strlen($path) > 255) {
			throw new Rackspace_Exception(Rackspace_Exception::FILE_PATH_TOO_LARGE);
		}

		if (sizeof($this->personality) == 5) {
			throw new Rackspace_Exception(Rackspace_Exception::FILE_LIMIT_REACHED);
		}

		require_once 'Rackspace/Cloud/Servers/Server/File.php';
		$this->personality[] = new Rackspace_Cloud_Servers_Server_File($path, $contents);
	}

	public function removeFile($path)
	{
		if (sizeof($this->personality) == 0) {
			throw new Rackspace_Exception(Rackspace_Exception::FILE_NOT_FOUND);
		}

		foreach ($this->personality as $key => $value) {
			if ($value instanceof Rackspace_Cloud_Servers_Server_File && $value->path == $path) {
				unset($this->personality[$key]);
			}
		}

		throw new Rackspace_Exception(Rackspace_Exception::FILE_NOT_FOUND);
	}


	/**
	 * Reboot the server
	 *
	 * @param string $type Whether to perform a SOFT or HARD reboot
	 * @return bool
	 */
	public function reboot($type = 'SOFT')
	{
		$http = Rackspace_Cloud_Servers::getHttpClient();
		$http->setUri(Rackspace::$server_url . "/servers/$this->id/action");
		
		$body = new Rackspace_Json_Container();
		$body->reboot = new Rackspace_Json_Container();
		$body->reboot->type = strtoupper($type);
		
		$json = Zend_Json::encode($body);
		
		$http->setRawData($json);
		
		$http->setEncType("application/json");
		
		$response = $http->request("POST");

		/* @var $response Zend_Http_Client_Reponse */
		if ($response->isError()) {
			$data = Zend_Json::decode($response->getBody());
			throw new Rackspace_Cloud_Servers_Fault($data);
		}
		
		if ($response->isSuccessful()) {
			return true;
		}
	}

	/**
	 * Return int representation of this object
	 *
	 * @return int
	 */
	public function toInt()
	{
		return (int) $this->id;
	}

	/**
	 * Clone the server
	 *
	 * @return void
	 */
	public function __clone()
	{
		$this->id = null;
		$this->progress = null;
		$this->status = null;
		$this->name .= "-clone";
		$this->hostId = null;
		$this->addresses = null;
		if (is_array($this->metadata) && sizeof($this->metadata) == 0) {
			$this->metadata = null;
		}
	}

	/**
	 * __get()
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function __get($key)
	{
		if (property_exists($this, $key)) {
			return $this->{$key};
		}
	}

	/**
	 * __set()
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function __set($key, $value)
	{
		if ($key == 'flavorId' && !($value instanceof Rackspace_Cloud_Servers_Flavor)) {
			$servers = Rackspace::getInstance(Rackspace::SERVICE_CLOUD_SERVERS);
			try {
				$flavor = $servers->getFlavors($value);
				$value = $flavor;
			} catch (Rackspace_Exception $e) {
				// Lets just create an object here
				require_once 'Rackspace/Cloud/Servers/Flavor.php';
				$flavor = new Rackspace_Cloud_Servers_Flavor(array('id' => $value));
				$value = $flavor;
			}
		}

		if ($key == 'imageId' && !($value instanceof Rackspace_Cloud_Servers_Image)) {
			$servers = Rackspace::getInstance(Rackspace::SERVICE_CLOUD_SERVERS);
			try {
				$image = $servers->getImages($value);
				$value = $image;
			} catch (Rackspace_Exception $e) {
				// Lets just create an object here
				require_once 'Rackspace/Cloud/Servers/Image.php';
				$image = new Rackspace_Cloud_Servers_Image(array('id' => $value));
				$value = $image;
			}

			$value->setServerId($this->id);
		}

		/* While $this->id isn't always set first, if it is, we can skip this costly call for existing server instances */
		if ($key == 'name') {
			$value = $this->sanitizeName($value);
		}
		
		parent::__set($key, $value);
	}

	/**
	 * Check if the current instance is a new server
	 *
	 * @return bool
	 */
	protected function isNew()
	{
		if (isset($this->id)) {
			return false;
		}

		return true;
	}

	protected function sanitizeName($value)
	{
		$value = str_replace(" ", "", $value);
		if (preg_match('/[^0-9a-zA-Z]+/', $value)) {
			$value = preg_replace('/[^0-9a-zA-Z]+/', '', $value);
		}
		return $value;
	}
}