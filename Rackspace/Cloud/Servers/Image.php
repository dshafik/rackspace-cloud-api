<?php
/**
 * Rackspace Cloud Servers Image
 *
 * @author Davey Shafik <me@daveyshafik.com>
 * @package Rackspace
 * @subpackage Rackspace_Cloud_Servers
 */

/**
 * Rackspace_Cloud_Servers_Abstract
 */
require_once 'Rackspace/Cloud/Servers/Abstract.php';
require_once 'Rackspace/Json/Int.php';
require_once 'Rackspace/Json/Object.php';

/**
 * Rackspace Cloud Servers Image
 *
 * @package Rackspace
 * @subpackage Rackspace_Cloud_Servers
 */
class Rackspace_Cloud_Servers_Image extends Rackspace_Cloud_Servers_Abstract implements Rackspace_Json_Int {
	/**
	 * @var int Image ID
	 */
	protected $id;

	/**
	 * @var int Image RAM
	 */
	protected $ram;

	/**
	 * @var int Image Disk Space
	 */
	protected $disk;

	/**
	 * @var string Image Name
	 */
	protected $name;

	/**
	 * @var Rackspace_Cloud_Servers_Server Server instance for which this is the image
	 */
	protected $serverId;

	/**
	 * @var int Saving Progress
	 */
	protected $progress;

	/**
	 * @var Creation date
	 */
	protected $created;

	/**
	 * Return JSON representation of this object
	 *
	 * @return string
	 */
	public function toJson()
	{
		require_once 'Rackspace/Json.php';
		if (is_null($this->serverId)) {
			throw new Rackspace_Exception(Rackspace_Exception::SERVER_ID_MISSING);
		}

		if (is_null($this->name)) {
			throw new Rackspace_Exception(Rackspace_Exception::IMAGE_NAME_MISSING);
		}

		return parent::toJson();
	}

    public function setServerId($serverId) {
        $this->serverId = $serverId; 
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
	 * Create a new image (backup) of the server of which
	 * this is the image for. {@see Rackspace_Cloud_Servers_Server->imageId}
	 *
	 * @return bool
	 */
	public function save()
	{
		$http = Rackspace_Cloud_Servers::getHttpClient();
		$http->setUri(Rackspace::$server_url . "/images");

		$json = Zend_Json::encode($this);

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
			$this->__construct($array['image']);
			return true;
		}
	}

	public function delete()
	{
		if (is_null($this->id)) {
			throw new Rackspace_Exception(Rackspace_Exception::IMAGE_ID_MISSING);
		}


		$http = Rackspace_Cloud_Servers::getHttpClient();
		$http->setUrl(Rackspace::$server_url . "/images/$this->id");

		$response = $http->request("DELETE");

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