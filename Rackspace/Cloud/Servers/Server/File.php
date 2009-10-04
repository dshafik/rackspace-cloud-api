<?php
require_once 'Rackspace/Json/Object.php';

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