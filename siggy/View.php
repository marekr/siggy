<?php

namespace Siggy;

use Siggy\Blade;

use Illuminate\Support\ViewErrorBag;

class View 
{
	private static $blade = null;

	public static function getInstance()
	{
		if(self::$blade == null)
		{
			self::$blade = new Blade(APPPATH.'views', APPPATH.'cache/blade');
			self::$blade->container['view']->share('errors', new ViewErrorBag);
		}

		return self::$blade;
	}

	public static function getViewFactory()
	{
		return self::getInstance()->container['view'];
	}
	
	public static function __callStatic($method, $params)
	{
		return forward_static_call_array([self::getInstance()->container['view'], $method], $params);
	}
}