<?php
/**
 * Rackspace Cloud API Exception
 * 
 * @author Davey Shafik <me@daveyshafik.com>
 * @package Rackspace
 */

/**
 * Rackspace Cloud API Exception
 * 
 * @package Rackspace
 */
class Rackspace_Exception extends Exception {
	const UNKNOWN_SERVICE = 1;
	const AUTHENTICATION_ERROR = 2;
	const INCOMPLETE_SERVER_INSTANCE = 3;
	
	static public $msg = array(
		self::UNKNOWN_SERVICE => 'Unknown Cloud Service.',
		self::AUTHENTICATION_ERROR => 'Authentication Error.',
		self::INCOMPLETE_SERVER_INSTANCE => 'You must use Rackspace_Cloud_Server::getServerDetails() to retrieve the flavorId',
	);
	
	public function __construct($error)
	{
		parent::__construct(self::$msg[$error], $error);
	}
}