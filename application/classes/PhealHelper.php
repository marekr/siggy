<?php

use Pheal\Core\Config;

class PhealHelper
{
	public static function configure()
	{
		Config::getInstance()->cache = new \Pheal\Cache\FileStorage(APPPATH.'cache/api/');
		Config::getInstance()->http_ssl_verifypeer = false;
		Config::getInstance()->http_user_agent = 'siggy '.SIGGY_VERSION.' borkedlabs@gmail.com';
	}
}
