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
class Rackspace_Cloud_Servers_Image extends Rackspace_Cloud_Servers_Abstract implements Rackspace_Json_Int, Rackspace_Json_Object {
	protected $id;
	protected $ram;
	protected $disk;
	protected $name;
	protected $serverId;

	public function toJson()
	{
		require_once 'Rackspace/Json.php';
		if (is_null($this->serverId)) {
			throw new Rackspace_Exception(Rackspace_Exception::SERVER_ID_MISSING);
		}

		return parent::toJson();
	}

	public function toInt()
	{
		return (int) $this->id;
	}

	public function create($name)
	{
		$this->name = $name;
		echo Zend_Json::encode($this);
	}
}