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
	/**
	 * @const int Unknown Service Error Code
	 */
	const UNKNOWN_SERVICE = 1;

	/**
	 * @const int Authentication Error Code
	 */
	const AUTHENTICATION_ERROR = 2;

	/**
	 * @const int Incomplete Server Instance Error Code
	 */
	const INCOMPLETE_SERVER = 3;

	/**
	 * @const int Bad Request Error Code
	 */
	const BAD_REQUEST = 4;

	const SERVER_ID_MISSING = 5;

	/**
	 * @var array An array of messages that compliment the error codes
	 */
	static public $msg = array(
		self::UNKNOWN_SERVICE => 'Unknown Cloud Service.',
		self::AUTHENTICATION_ERROR => 'Authentication Error.',
		self::INCOMPLETE_SERVER => 'You must use Rackspace_Cloud_Server::getServerDetails() to retrieve the flavorId',
		self::BAD_REQUEST => 'An error occurred with the last request',
		self::SERVER_ID_MISSING => 'You must specify a serverId to image',
	);

	/**
	 * Constructor
	 *
	 * @param int $error One of the self::* constants
	 */
	public function __construct($error)
	{
		parent::__construct(self::$msg[$error], $error);
	}
}