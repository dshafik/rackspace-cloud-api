<?php
ini_set("include_path", "..".PATH_SEPARATOR.ini_get("include_path"));
require_once 'PHPUnit/Framework.php';

/**
 * Rackspace Cloud API Testsuite
 *
 * @author Davey Shafik <me@daveyshafik.com>
 */
class RackspaceTestSuite extends PHPUnit_Framework_TestSuite {
    public function setUp()
	{
		if (!file_exists(dirname(__FILE__) . '/config.php')) {
			$this->markTestSuiteSkipped('Configuration file missing (config.php), please copy config-dist.php and modify to your needs.');
			exit;
		}

		require_once 'config.php';
		Rackspace::$username = RACKSPACE_CLOUD_SERVER_USER;
		Rackspace::$api_key = RACKSPACE_CLOUD_SERVER_API_KEY;
	}
}
?>
