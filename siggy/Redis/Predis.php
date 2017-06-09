<?php

namespace Siggy\Redis;

use Predis\Connection\ConnectionException;

class Predis {

	private static $client = null;

	public static function setup()
	{
		if(self::$client == null)
		{
			self::$client = new \Predis\Client([
				'scheme' => config('database.redis.default.scheme'),
				'host' => config('database.redis.default.host'),
				'port' => config('database.redis.default.port'),
				'persistent' => config('database.redis.default.persistent')
				]);

			try {
				self::$client->connect();
			} catch (ConnectionException $exception) {
				self::$client = null;
			}
		}
	}

	public static function client(): ?\Predis\Client
	{
		if(self::$client == null)
		{
			self::setup();
		}
		
		return self::$client;
	}
}