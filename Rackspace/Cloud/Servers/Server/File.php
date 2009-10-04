<?php
/**
 * Rackspace Cloud Server File
 *
 * @package Rackspace
 * @subpackage Rackspace_Cloud_Servers
 */

/**
 * Rackspace_Json_Object
 */
require_once 'Rackspace/Json/Object.php';

/**
 * Rackspace Cloud Server File
 *
 * Represents a file that will be pushed
 * to a new server instance upon creation.
 *
 * @package Rackspace
 * @subpackage Rackspace_Cloud_Servers
 */
class Rackspace_Cloud_Servers_Server_File implements Rackspace_Json_Object {
	protected $path;
	protected $contents;

	public function __construct($path, $contents)
	{
		$this->path = $path;
		$this->contents = $contents;
	}

	public function toObject()
	{
		require_once 'Rackspace/Json.php';
		$obj = new Rackspace_Json_Container();
		$obj->path =& $this->path;
		$obj->contents = base64_encode($this->contents);

		return $obj;
	}
}