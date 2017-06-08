<?php

use Pheal\Core\Config;
use Monolog\Logger;

class PhealHelper {
	
	public static function configure()
	{
		Config::getInstance()->http_ssl_verifypeer = false;
		Config::getInstance()->http_user_agent = 'siggy mark.roszko@gmail.com';
		
		if(env('PHEAL_CACHE') == 'memcache')
		{
			Config::getInstance()->cache = new \Pheal\Cache\MemcacheStorage( array('host' => 'localhost', 'port' => 11211) );
		}
		else if(env('PHEAL_CACHE') == 'predis')
		{
			Config::getInstance()->cache = new \Pheal\Cache\PredisStorage( ['host' => env('REDIS_HOST'), 
																			'port' => env('REDIS_PORT'), 
																			'persistent' => env('REDIS_PERSISTENT')] );
		}
		else
		{
			Config::getInstance()->cache = new \Pheal\Cache\FileStorage(__DIR__.'/../../storage/cache/pheal/');
		}
	}
}
