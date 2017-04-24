<?php


use Illuminate\Translation\FileLoader;
use Illuminate\Container\Container;
use Illuminate\Translation\Translator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Validation\Factory;

class Validator {

	public static $instance = null;

	public static function getInstance()
	{
		if(self::$instance == null)
		{
			$loader = new FileLoader(new Filesystem, DOCROOT.'/resources/lang');
			$translator = new Translator($loader, 'en');
			self::$instance = new Factory($translator, new Container);
		}

		return self::$instance;
	}
	
	public static function __callStatic($method, $params)
	{
		return forward_static_call_array([self::getInstance(), $method], $params);
	}
}