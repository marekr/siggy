<?php

namespace Siggy;
use \URL;

class AssetHelpers
{
	public static function baseUrl()
	{
		return self::joinPaths(URL::base(TRUE), "assets/");
	}

	public static function jsAssetUrl(string $name, string $version): string
	{
		$file = self::assetHash($name, $version);
		
		return self::baseUrl() . self::jsAssetFilename($name, $version);
	}

	public static function jsAssetFilename(string $name, string $version): string
	{
		return self::assetHash($name, $version).".js";
	}

	public static function assetHash(string $name, string $version): string
	{
		return sha1($name . $version);
	}

	public static function joinPaths() 
	{
		$path = '';
		$arguments = func_get_args();
		$args = [];
		foreach($arguments as $a) 
		{
			if($a !== '') 
			{
				$args[] = $a;//Removes the empty elements
			}
		}

		$arg_count = count($args);
		for($i=0; $i<$arg_count; $i++) {
			$folder = $args[$i];
			
			if($i != 0 && $folder[0] == '/') 
			{
				$folder = substr($folder,1); //Remove the first char if it is a '/' - and its not in the first argument
			}

			if($i != $arg_count-1 && substr($folder,-1) == '/') 
			{
				$folder = substr($folder,0,-1); //Remove the last char - if its not in the last argument
			}

			$path .= $folder;
			if($i != $arg_count-1)
			{
				$path .= '/'; //Add the '/' if its not the last element.
			}
		}
		return $path;
	}
}