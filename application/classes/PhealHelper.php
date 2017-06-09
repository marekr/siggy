<?php

use Pheal\Core\Config;
use Monolog\Logger;

class PhealHelper {
	
	public static function configure()
	{
		Config::getInstance()->http_ssl_verifypeer = false;
		Config::getInstance()->http_user_agent = 'siggy mark.roszko@gmail.com';
		
		$type = config('pheal.cache.type');
		if($type == 'memcache')
		{
			Config::getInstance()->cache = new \Pheal\Cache\MemcacheStorage( array('host' => 'localhost', 'port' => 11211) );
		}
		else if($type == 'predis')
		{
			Config::getInstance()->cache = new \Pheal\Cache\PredisStorage( ['host' => config('database.redis.default.host'), 
																			'port' => config('database.redis.default.port'), 
																			'persistent' => config('database.redis.default.persistent')] );
		}
		else
		{
			Config::getInstance()->cache = new \Pheal\Cache\FileStorage(__DIR__.'/../../storage/cache/pheal/');
		}
	}
}
