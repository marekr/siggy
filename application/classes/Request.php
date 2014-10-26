<?php

class Request extends Kohana_Request {
	public static $trusted_proxies = array('127.0.0.1', 'localhost', 'localhost.localdomain', '104.131.58.157');
}