<?php

namespace Siggy;

use \URL;
use Illuminate\Support\Facades\Blade;
use \App;
use \Siggy\Assets\Helpers as AssetHelpers;

class BladeHelpers
{
	public static $config = null;

	/**
	 * Asset Js
	 * @param $name
	 * @param $version
	 * @return bool|string
	 */
	public static function assetJs(string $name, bool $unbundled = false): string
	{
		if( App::environment('production') )
		{
			$url = AssetHelpers::jsAssetUrl($name, SIGGY_VERSION);
			
			return "<script type=\"text/javascript\" src=\"{$url}\"></script>\n";
		}
		else
		{
			$webpack = env('WEBPACK_ADDRESS','');
			if( $webpack != '')
			{
				$url = AssetHelpers::joinPaths($webpack,$name);
			}
			else
			{
				$url = AssetHelpers::joinPaths(AssetHelpers::baseUrl(),$name."?".SIGGY_VERSION);
			}

			return "<script type=\"text/javascript\" src=\"{$url}\"></script>\n";
		}
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