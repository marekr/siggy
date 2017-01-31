<?php

use Pheal\Core\Config;
use Monolog\Logger;

class ESIHelper {
	
	public static function configure()
	{
		$config = ESI\Configuration::getDefaultConfiguration();
		$config->setCurlTimeout(10);	//10 second timeout should be reasonable
		$config->setUserAgent('siggy '.SIGGY_VERSION.' mark.roszko@gmail.com');
	}
}
