<?php
/**
 * Rackspace Cloud Servers Fault
 * 
 * @author Davey Shafik <me@daveyshafik.com>
 * @package Rackspace
 * @subpackage Rackspace_Cloud_Servers
 */

/**
 * Rackspace Cloud Servers Fault
 * 
 * @package Rackspace
 * @subpackage Rackspace_Cloud_Servers
 */
class Rackspace_Cloud_Servers_Fault extends Rackspace_Exception {

	public $type;
	public $code;
	public $message;
	public $details;
	
	public function __construct($data)
	{
		$this->type = array_shift(array_keys($data));
		$this->code = $data[$this->type]['code'];
		$this->message = $data[$this->type]['message'];
		$this->details = $data[$this->type]['details'];
	}

	/* Potential Faults
	cloudServersFault
	serviceUnavailable
	unauthorized
	badRequest
	overLimit
	badMediaType
	badMethod
	itemNotFound
	buildInProgress
	serverCapacityUnavailable
	backupOrResizeInProgress
	resizeNotAllowed
	notImplemented
	*/
}