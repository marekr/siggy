<?php

use Pheal\Core\Config;
use Monolog\Logger;

class PhealHelper {
	
	public static function configure()
	{
		Config::getInstance()->log = new \Pheal\Log\FileStorage(APPPATH.'/logs/api/');
	
		Config::getInstance()->http_ssl_verifypeer = false;
		Config::getInstance()->http_user_agent = 'siggy '.SIGGY_VERSION.' mark.roszko@gmail.com';
		
		if(CACHE_METHOD == 'memcache')
		{
			Config::getInstance()->cache = new \Pheal\Cache\MemcacheStorage( array('host' => 'localhost', 'port' => 11211) );
		}
		else
		{
			Config::getInstance()->cache = new \Pheal\Cache\FileStorage(APPPATH.'cache/api/');
		}
	}
}
