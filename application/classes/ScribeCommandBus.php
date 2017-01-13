<?php

class ScribeCommandBus {

	public static $connected = false;
	private static $socket = null;

	public static function Connect(): void
	{
		$dsn = '';
		if( Kohana::$environment == Kohana::PRODUCTION )
		{
			$dsn = Kohana::$config->load('scribe.production.connection_string');
		}
		else
		{
			$dsn = Kohana::$config->load('scribe.development.connection_string');
		}


		if(self::$connected)
		{
			return;
		}

		self::$socket = new ZMQSocket(new ZMQContext(), ZMQ::SOCKET_PUSH, 'scribeCommandBus');

		/* Get list of connected endpoints */
		$endpoints = self::$socket->getEndpoints();

		/* Check if the socket is connected */
		if (!in_array($dsn, $endpoints['connect'])) {
			self::$socket->connect($dsn);
		}
		
		self::$connected = true;
	}

	public static function UnfreezeCharacter(int $characterId): void
	{
		self::Connect();

		if(!self::$connected)
		{
			return;
		}

		$payload = [
			'type' => 'unfreeze_character',
			'parameters' => [
				'character_id' => $characterId
			]
		];

		/* Send and receive */
		self::$socket->send(json_encode($payload));
	}
}

