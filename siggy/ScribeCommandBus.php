<?php

namespace Siggy;

use \ZMQSocket;
use \ZMQContext;
use \ZMQ;

class ScribeCommandBus {

	public static $connected = false;
	private static $socket = null;

	public static function Connect(): void
	{
		if(self::$connected)
		{
			return;
		}

		$dsn = config('scribe.connection_string');

		self::$socket = new ZMQSocket(new ZMQContext(1, true), ZMQ::SOCKET_PUSH, 'scribeCommandBus');

		/* Get list of connected endpoints */
		$endpoints = self::$socket->getEndpoints();

		/* Check if the socket is connected */
		if (!in_array($dsn, $endpoints['connect'])) {
			self::$socket->connect($dsn);
		}
		
		self::$connected = true;
	}

	public static function RefreshSession(int $userId, int $characterId): void
	{
		self::Connect();

		if(!self::$connected)
		{
			return;
		}

		$payload = [
			'type' => 'update_session',
			'parameters' => [
				'user_id' => $userId,
				'character_id' => $characterId,
			]
		];

		/* Send and receive */
		self::$socket->send(json_encode($payload));
	}

	public static function UnfreezeCharacter(string $characterOwnerHash): void
	{
		self::Connect();

		if(!self::$connected)
		{
			return;
		}

		$payload = [
			'type' => 'unfreeze_character',
			'parameters' => [
				'character_owner_hash' => $characterOwnerHash
			]
		];

		/* Send and receive */
		self::$socket->send(json_encode($payload));
	}
	
	public static function UpdateSSOCharacter(string $characterOwnerHash): void
	{
		self::Connect();

		if(!self::$connected)
		{
			return;
		}

		$payload = [
			'type' => 'update_sso_character',
			'parameters' => [
				'character_owner_hash' => $characterOwnerHash
			]
		];

		/* Send and receive */
		self::$socket->send(json_encode($payload));
	}
}

