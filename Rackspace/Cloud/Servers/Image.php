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

		return parent::toJson();
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
	 * @param string $name Backup name
	 */
	public function create($name)
	{
		$this->name = $name;
		echo Zend_Json::encode($this);
	}
}