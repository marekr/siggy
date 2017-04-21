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
				'scheme' => env('REDIS_SCHEME'),
				'host' => env('REDIS_HOST'),
				'port' => env('REDIS_PORT'),
				'persistent' => env('REDIS_PERSISTENT')
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