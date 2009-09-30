<?php
class RackspaceTestAbstract extends PHPUnit_Framework_TestCase {
	protected function checkConfig()
	{
		$this->assertEquals(file_exists(dirname(__FILE__) . '/config.php'), true, 'Configuration file missing (config.php), please copy config-dist.php and modify to your needs.');
		require_once dirname(__FILE__) . '/config.php';
	}
}
?>
