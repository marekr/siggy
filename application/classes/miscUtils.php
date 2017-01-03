<?php

use Pheal\Pheal;
use Carbon\Carbon;

final class miscUtils {

	const TIER1COST = 33000;
	const TIER2COST = 29000;
	const TIER3COST = 25000;

	static function computeCostPerDays( $memberCount, $days )
	{
		if( $memberCount == 0 )
		{
			return 0;
		}

		$total = 25420*$memberCount + 283650;
		return $total*$days;
	}


	static function parseIngameSigExport( $string )
	{
		$resultingSigs = array();

		$lines = explode("\n", $string);
		foreach( $lines as $line )
		{
			$rawdata = explode("\t", $line);
			if( count($rawdata) < 2 )
			{
				continue;
			}

			$sigData = array('type' => 'none', 'sig' => '', 'siteID' => 0);

			$matches = array();

			/*eliminate junk items, :CCP: sometimes
			inject mix spaces/tabs that cause the tab split to not be clean */
			$data = array();
			foreach($rawdata as $item)
			{
				$item = trim($item);
				if( empty($item) )
					continue;
				$data[] = $item;
			}

			foreach($data as $k => $item)
			{
				$item = trim($item);
				if( empty($item) )
					continue;

				preg_match("/^([a-zA-Z]{3})-([0-9]{3})$/", $item, $matches );
				if( count($matches) == 3 )	//SIG-NUM, SIG, NUM
				{
					$sigData['sig'] = $matches[1];
					continue;
				}

				$regex = "/^(".__('Wormhole')."|".__('Data Site')."|".__('Gas Site')."|".__('Relic Site')."|".__('Ore Site')."|".__('Combat Site').")$/";

				preg_match($regex, $item, $matches );
				if( count($matches) == 2 )
				{
					switch( $matches[1] )
					{
						case __('Wormhole'):
							$sigData['type'] = 'wh';
							$sigData['siteID'] = 0;
							break;
						case __('Data Site'):
							$sigData['type'] = 'data';
							$sigData['siteID'] = self::siteIDLookupByName( $data[$k+1], $sigData['type'] );
							break;
						case __('Gas Site'):
							$sigData['type'] = 'gas';
							$sigData['siteID'] = self::siteIDLookupByName( $data[$k+1], $sigData['type'] );
							break;
						case __('Relic Site'):
							$sigData['type'] = 'relic';
							$sigData['siteID'] = self::siteIDLookupByName( $data[$k+1], $sigData['type'] );
							break;
						case __('Ore Site'):
							$sigData['type'] = 'ore';
							$sigData['siteID'] = self::siteIDLookupByName( $data[$k+1], $sigData['type'] );
							break;
						case __('Combat Site'):
							$sigData['type'] = 'anomaly';
							$sigData['siteID'] = self::siteIDLookupByName( $data[$k+1], $sigData['type'] );
							break;
					}
					continue;
				}
			}

			if( $sigData['sig'] != '' )
			{
				$resultingSigs[] = $sigData;
			}
		}
		return $resultingSigs;
	}

	static function siteIDLookupByName( $name, $type )
	{
		$sites = DB::query(Database::SELECT, "SELECT * FROM sites WHERE type = :type")
											->param(':type', $type)
					  ->execute()->as_array();

		foreach( $sites as $site )
		{
			if( __($site['name']) == $name )
			{
				return $site['id'];
			}
		}

		return 0;
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

	static function searchEVEEntityByName( $names, $type = 'corp' )
	{
		$results = [];

		PhealHelper::configure();
		$pheal = new Pheal(null,null,'eve');

		$result = $pheal->CharacterID( array( 'names' => $names ) )->toArray();
		$potentialCorps = $result['result']['characters'];

		if( $type == 'corp' )
		{
			$pheal->scope = 'corp';
		}
		else
		{
			$pheal->scope = 'eve';
		}

		foreach( $potentialCorps as $corp )
		{
			$id = (int)$corp['characterID'];

			if( $type == 'corp' )
			{
				$corp = Corporation::find($id);
				if($corp != null)
				{
					$results[$id] = $corp;
				}
			}
			else
			{
				$char = Character::find($id);
				if($char != null)
				{
					$results[$id] = $char;
				}
			}
		}

		return $results;
	}

	static function hash_array_to_string($arr)
	{
		foreach( $arr as $k => $v )
		{
			$arr[$k] = Database::instance()->escape($v);
		}
		return implode(',', $arr);
	}

	static function getDBCacheItem( $key )
	{
		$cache = DB::query(Database::SELECT, "SELECT * FROM cache_store WHERE cacheKey = :key")
											->param(':key', $key)
					  ->execute()->current();

		return $cache['cacheValue'];
	}

	static function storeDBCacheItem( $key, $value )
	{
		DB::query(null, "INSERT INTO cache_store (`cacheKey`,`cacheValue`) VALUES (:key, :value)  ON DUPLICATE KEY UPDATE cacheValue=:value")
			->param(':key', $key )
			->param(':value', $value )
			->execute();
	}

	static function findSystemByName($name)
	{
		$systemID = DB::query(Database::SELECT, 'SELECT id,name FROM solarsystems WHERE LOWER(name) = :name')
															->param(':name', $name )
															->execute()
															->get('id', 0);


		return $systemID;
	}

	static function systemNameByID($id)
	{
		$name = DB::query(Database::SELECT, 'SELECT id,name
												FROM solarsystems WHERE id = :id')
															->param(':id', $id )
															->execute()
															->get('name', '');


		return $name;
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
