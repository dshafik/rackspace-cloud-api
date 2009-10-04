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
 * Rackspace Cloud Servers Instance
 * 
 * @package Rackspace
 * @subpackage Rackspace_Cloud_Servers
 */
class Rackspace_Cloud_Servers_Server extends Rackspace_Cloud_Servers_Abstract {
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
	 * @var bool Whether the instance is new or not
	 */
	protected $is_new;

	/**
	 * @var array An array of custom files when creating a new instance
	 */
	protected $personality;

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
		$reponse = $http->request();
		
		$addr = Zend_Json::decode($reponse->getBody());
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
		
		$response = $http->request();
		
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
		
		$response = $http->request();
		
		$decoded = Zend_Json::decode($response->getBody());
		$this->addresses['private'] = $decoded['private'];
		
		return $decoded['private'];
	}
	
	public function getFlavor()
	{
		if (is_null($this->flavorId)) {
			throw new Rackspace_Exception(Rackspace_Exception::INCOMPLETE_SERVER);
		}
		return $this->flavorId;
	}

	public function create()
	{
		if (!$this->isNew()) {
			throw new Rackspace_Exception(Rackspace_Exception::SERVER_ALREADY_EXISTS);
		}

		$json = Zend_Json::encode($this);

		/*$http = Rackspace_Cloud_Servers::getHttpClient();
		$http->setUri(Rackspace::$server_url . "/servers");*/
	}

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

	public function removeMetadata($name)
	{
		unset($this->metadata[$name]);
	}

	public function addFile($path, $contents)
	{
		if (!$this->isNew()) {
			throw new Rackspace_Exception(Rackspace_Exception::SERVER_ALREADY_EXISTS);
		}

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
		unset($this->files[$path]);
	}
	
	public function reboot($type = 'SOFT')
	{
		$http = Rackspace_Cloud_Servers::getHttpClient();
		$http->setUri(Rackspace::$server_url . "/servers/$this->id/action");
		
		$body = new stdClass();
		$body->reboot = new StdClass();
		$body->reboot->type = $type;
		
		$json = Zend_Json::encode($body);
		
		$http->setRawData($json);
		
		$http->setEncType("application/json");
		
		$response = $http->request("POST");
		
		if ($response->isSuccessful()) {
			return true;
		} else {
			return false;
		}
	}

	public function __clone()
	{
		$this->id = null;
		$this->progress = null;
		$this->status = null;
		$this->name .= "(Cloned)";
		$this->hostId = null;
	}
	
	public function __get($key)
	{
		if (property_exists($this, $key)) {
			return $this->{$key};
		}
	}
	
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

			$value->serverId = $this->id;
		}

		/* While $this->id isn't always set first, if it is, we can skip this costly call for existing server instances */
		if ($key == 'name' && !isset($this->id)) {
			$value = $this->sanitizeName($value);
		}
		
		$this->{$key} = $value;
	}

	protected function isNew()
	{
		if (isset($this->id)) {
			return false;
		}

		return true;
	}

	protected function sanitizeName($value)
	{
		if (preg_match('/[^0-9a-zA-Z]+/', $value)) {
			$value = strtolower($value);
			$value = preg_replace('/[^0-9a-zA-Z]+/', '-', $value);
			$value = str_replace(" ", "-", $value);
		}
		return $value;
	}
}