<?php

namespace Siggy;

use Siggy\Blade;

class View 
{
	private static $blade = null;

	public static function getInstance()
	{
		if(self::$blade == null)
		{
			self::$blade = new Blade(APPPATH.'views', APPPATH.'cache/blade');
		}

		return self::$blade;
	}
	
    public static function __callStatic($method, $params)
    {
        return forward_static_call_array([self::getInstance()->container['view'], $method], $params);
    }
}