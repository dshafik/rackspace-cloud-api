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

/**
 * Rackspace Cloud Servers Image
 * 
 * @package Rackspace
 * @subpackage Rackspace_Cloud_Servers
 */
class Rackspace_Cloud_Servers_Image extends Rackspace_Cloud_Servers_Abstract  {
	public $id;
	public $ram;
	public $disk;
	public $name;
}