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
	 * @var array Undefined Variables end up in this array
	 */
	protected $vars = array();

	/**
	 * Constructor
	 *
	 * @param array $data Response Data
	 */
	public function __construct($data)
	{
		foreach ($data as $key => $value) {
			$this->__set($key, $value);
		}
	}

	public function toJson()
	{
		require_once 'Rackspace/Json.php';
		$container = new Rackspace_Json_Container();

		// Add the sub-object
		$class = get_class($this);
		$parts = explode("_", $class);
		$key = strtolower(array_pop($parts));
		$container->{$key} = new Rackspace_Json_Container();

		foreach ($this as $property => $value) {
			if (is_null($value) || $property == 'vars') {
				continue;
			}
			$value = Rackspace_Json::getValue($value);
			$container->{$key}->{$property} = $value;
		}

		return Zend_Json::encode($container);
	}

	public function __get($key)
	{
		if (isset($this->vars[$key])) {
			return $this->vars[$key];
		}
	}

	/**
	 * Basic Set method
	 *
	 * @param string|int $key
	 * @param mixed $value
	 */
	public function __set($key, $value)
	{
		if (property_exists($this, $key)) {
			$this->{$key} = $value;
		} else {
			$this->vars[$key] = $value;
		}
	}
}