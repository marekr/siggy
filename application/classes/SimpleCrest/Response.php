<?php

namespace SimpleCrest;

class Response
{
	private $statusCode = 0;

	private $body = null;

	public function __construct(int $statusCode, $body)
	{
		$this->statusCode = $statusCode;
		$this->body = $body;
	}

	public function getStatusCode()
	{
		return $this->statusCode;
	}

	public function getBody()
	{
		return $this->body;
	}
}