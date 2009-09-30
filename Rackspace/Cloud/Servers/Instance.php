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
class Rackspace_Cloud_Servers_Instance extends Rackspace_Cloud_Servers_Abstract {
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
	protected $addresses = array('public' => null, 'private' => null);
	
	/**
	 * @var array An array of metadata
	 */
	protected $metadata;

	public function __construct($data) {
		foreach ($data as $key => $value) {
			$this->__set($key, $value);
		}
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
			throw new Rackspace_Exception(Rackspace_Exception::INCOMPLETE_SERVER_INSTANCE);
		}
		return $this->flavorId;
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
	
	public function __get($key)
	{
		if (property_exists($this, $key)) {
			return $this->{$key};
		}
	}
	
	public function __set($key, $value)
	{
		if ($key == 'flavorId') {
			$servers = Rackspace::getInstance(Rackspace::SERVICE_CLOUD_SERVERS);
			$value = $servers->getFlavorDetails($value);
		}
		
		$this->{$key} = $value;
	}
}