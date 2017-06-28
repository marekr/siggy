<?php

namespace Siggy\Redis;

use Illuminate\Support\Facades\Redis;

class RedisTtlCounter
{
	private $queueName;
	private $ttl = 3600;

	public function __construct($queueName, $ttl = 3600)
	{
		$this->queueName = $queueName;
		$this->ttl = $ttl;
	}

	public function add($value)
	{
		$value = serialize($value);
		
		Redis::zadd($this->queueName, time(), $value);
	}
	
	public function cleanup()
	{
		Redis::zremrangebyscore($this->queueName, 0, (time() - $this->ttl));
	}

	public function count()
	{
		$this->cleanup();
		return Redis::zcard($this->queueName);
	}
}