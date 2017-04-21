<?php

namespace Siggy\Redis;

use Siggy\Redis\Predis;

class RedisTtlCounter
{
	private $queueName;
	private $ttl = 3600;
	private $predis = null;

	public function __construct($queueName, $ttl = 3600)
	{
		$this->predis = Predis::client();
		$this->queueName = $queueName;
		$this->ttl = $ttl;
	}

	public function add($value)
	{
		if($this->predis == null)
		{
			return;
		}

		$value = serialize($value);
		$this->predis->zadd($this->queueName, time(), $value);
	}
	
	public function cleanup()
	{
		if($this->predis == null)
		{
			return;
		}

		$this->predis->zremrangebyscore($this->queueName, 0, (time() - $this->ttl));
	}

	public function count()
	{
		if($this->predis == null)
		{
			return;
		}
		
		$this->cleanup();
		return $this->predis->zcard($this->queueName);
	}
}