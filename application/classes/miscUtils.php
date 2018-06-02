<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final class miscUtils {

	static function computeCostPerDays( $memberCount, $days )
	{
		if( $memberCount == 0 )
		{
			return 0;
		}

		$total = 200000*$memberCount + 250000;
		return $total*$days;
	}



	static function isWspaceID($id)
	{
		if( $id >= 31000000 )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	static function getDBCacheItem( $key )
	{
		$cache = DB::selectOne("SELECT * FROM cache_store WHERE cacheKey = ?", [$key]);

		return ($cache != null ? $cache->cacheValue : '');
	}

	static function storeDBCacheItem( $key, $value )
	{
		DB::insert("INSERT INTO cache_store (`cacheKey`,`cacheValue`) 
			VALUES (:key, :value)  ON DUPLICATE KEY UPDATE cacheValue=:value2",[
			'key' => $key,
			'value' => $value,
			'value2' => $value
		]);
	}

	static function systemNameByID($id)
	{
		$system = DB::selectOne('SELECT id,name FROM solarsystems WHERE id = ?',[$id]);

		if($system != null)
		{
			return $system->name;
		}

		return '';
	}

	static function getDayStamp()
	{
		date_default_timezone_set('UTC');
		$today = getdate();
		return gmmktime(0,0,0,$today['mon'],$today['mday'],$today['year']);
	}

	static function getHourStamp( $offset=0 )
	{
		date_default_timezone_set('UTC');
		$now = time()+($offset*3600);
		$today = getdate($now);
		return gmmktime($today['hours'],0,0,$today['mon'],$today['mday'],$today['year']);
	}

	static function getDateTimeString( $timestamp )
	{
		return date('Y-m-d g:m', $timestamp);
	}

	static function timeToHourString( $timestamp )
	{
		$date = getdate($timestamp);

		return str_pad( $date['hours'], 2, '0', STR_PAD_LEFT).':00';
	}


	static function week_bounds( $date, &$start, &$end )
	{
		$date = strtotime( $date );

		// Find the start of the week, working backwards
		$start = $date;
		while( date( 'w', $start ) > WEEK_START )
		{
			$start -= 86400; // One day
		}

		// End of the week is simply 6 days from the start
		$end = date( 'Y-m-d', $start + ( 6 * 86400 ) );
		$start = date( 'Y-m-d', $start );
	}


	static function generateString($length = 14)
	{
		$randomString = substr( md5(uniqid(microtime() . rand(), true)), 0 ,14);
		return $randomString;
	}

	static function generateSalt($length = 10)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
		$randomString = '';
		for ($i = 0; $i < $length; $i++)
		{
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}

		return $randomString;
	}
}
