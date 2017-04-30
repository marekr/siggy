<?php

namespace Siggy;

use \URL;
use Illuminate\Support\Facades\Blade;

class BladeHelpers
{
	public static $config = null;

	/**
	 * Asset Js
	 * @param $name
	 * @param $version
	 * @return bool|string
	 */
	public static function assetJs(string $name, string $version = "", bool $unbundled = false): string
	{
		if(!$unbundled)
		{
			$url = \Siggy\Assets\Helpers::jsAssetUrl($name, $version);
			
			return "<script type=\"text/javascript\" src=\"{$url}\"></script>\n";
		}
		else
		{
			if(self::$config == null)
			{
				$path = base_path("config/assets.php");
				self::$config = include($path);
			}

			if(isset(self::$config['assets'][$name]))
			{
				$asset = self::$config['assets'][$name];
				$ret = "";
				foreach($asset['files'] as $file)
				{
					$url = \Siggy\Assets\Helpers::joinPaths(url('/'),$asset['publicPath'],$file."?".$version);
					
					$ret .= "<script type=\"text/javascript\" src=\"{$url}\"></script>\n";
				}
				
				return $ret;
			}
		}

		return "";
	}

	/**
	* Asset Css
	* @param $name
	* @param $rel
	* @param $version
	* @return bool|string
	*/
	public static function assetCss($name, $rel = 'stylesheet', $version = false)
	{
		$url = self::assetUrl($name, $version);
		// Return
		return "<link href=\"{$url}\" rel=\"{$rel}\" type=\"text/css\" />";
	}

	public static function register()
	{
		Blade::extend(function($view)
		{
			$pattern = self::createBladeMatcher('siggy_asset_js');
			return preg_replace($pattern, '$1<?php $var1 = \Siggy\BladeHelpers::assetJs$2; echo $var1; ?>', $view);
		});
	}
	
	public static function createBladeMatcher($function)
	{
		return '/(?<!\w)(\s*)@'.$function.'(\s*\(.*\))/';
	}

}