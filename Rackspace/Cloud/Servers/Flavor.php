<?php
/**
 * Rackspace Cloud Servers Flavor
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
 * Rackspace Cloud Servers Flavor
 * 
 * @package Rackspace
 * @subpackage Rackspace_Cloud_Servers
 */
class Rackspace_Cloud_Servers_Flavor extends Rackspace_Cloud_Servers_Abstract implements Rackspace_Json_Int, Rackspace_Json_Object {
	public $id;
	public $ram;
	public $disk;
	public $name;

	/**
	 * Return int representation of this object
	 *
	 * @return int
	 */
	public function toInt()
	{
		return (int) $this->id;
	}
}