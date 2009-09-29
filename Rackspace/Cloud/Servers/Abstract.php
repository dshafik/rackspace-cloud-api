<?php
/**
 * Rackspace Cloud Servers Abstract
 * 
 * @author Davey Shafik <me@daveyshafik.com>
 * @package Rackspace
 * @subpackage Rackspace_Cloud_Servers
 */

/**
 * Rackspace Cloud Servers Abstract
 * 
 * @package Rackspace
 * @subpackage Rackspace_Cloud_Servers
 */
abstract class Rackspace_Cloud_Servers_Abstract {
	/**
	 * Constructor
	 *
	 * @param array $data Response Data
	 */
	public function __construct($data)
	{
		foreach ($data as $key => $value) {
			$this->{$key} = $value;
		}
	}
}