<?php

use Pheal\Pheal;

final class miscUtils
{
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

	static function searchEVEEntityByName( $names, $type = 'corp' )
	{
		if( $type == 'corp' )
		{
			$nameArray = explode(',', $names);
			$queryArray = array();
			foreach($nameArray as $name)
			{
				$name = trim($name);
				if(!empty($name))
				{
					$queryArray[] = "corporationName LIKE ".Database::instance()->escape("%".$name."%");
				}
			}
			$querySQL = implode(" OR ", $queryArray);
			$results = DB::query(Database::SELECT, 'SELECT * FROM corporations WHERE '.$querySQL)->execute()->as_array();

			if( count( $results ) )
			{
				return $results;
			}
		}

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

		$resultArray = array();
		foreach( $potentialCorps as $corp )
		{
			try
			{
				if( $type == 'corp' )
				{
					$result = $pheal->CorporationSheet( array( 'corporationID' => (int)$corp['characterID'] ) )->toArray();
					//print 'found corp, storing locally!';
					$result = $result['result'];
					DB::query(Database::INSERT, 'INSERT INTO corporations (`corporationID`, `corporationName`, `memberCount`, `ticker`, `description`, `lastUpdate`) VALUES(:corporationID, :corporationName, :memberCount, :ticker, :description, :lastUpdate)'
											   .' ON DUPLICATE KEY UPDATE description = :description, memberCount = :memberCount, lastUpdate = :lastUpdate')
											->param(':memberCount', $result['memberCount'] )
											->param(':corporationID', $result['corporationID'] )
											->param(':corporationName', $result['corporationName'] )
											->param(':description', $result['description'] )
											->param(':ticker', $result['ticker'] )
											->param(':lastUpdate', time() )
											->execute();
					$resultArray[] = $result;
				}
				else
				{
					$result = $pheal->CharacterInfo( array( 'characterID' => (int)$corp['characterID'] ) )->toArray();
					$result = $result['result'];

					$resultArray[] = $result;
				}
			}
			catch (\Pheal\Exceptions\PhealException $e)
			{
				if( $e->getCode() == 523 || $e->getCode() == 522 )	//not a corp error
				{
					continue;
				}
			}
		}

		return $resultArray;
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

	static function increment_stat($stat, $groupData)
	{
		if( !$groupData['statsEnabled'] )
		{
			return;
		}

		if( !in_array( $stat, array('adds','updates','wormholes','pos_adds','pos_updates') ) )
		{
			throw new Exception("invalid stat key");
		}

		$duplicate_update_string = $stat .'='. $stat .'+1';

		DB::query(Database::INSERT, 'INSERT INTO stats (`charID`,`charName`,`groupID`,`chainmap_id`,`dayStamp`,`'.$stat.'`)
												VALUES(:charID, :charName, :groupID, :chainmap, :dayStamp, 1)
												ON DUPLICATE KEY UPDATE '.$duplicate_update_string)
							->param(':charID',  Auth::$session->charID )
							->param(':charName', Auth::$session->charName )
							->param(':groupID', Auth::$session->groupID )
							->param(':chainmap', $groupData['active_chain_map'] )
							->param(':dayStamp', miscUtils::getDayStamp() )
							->execute();
	}

	static function findSystemByName($name)
	{
		$systemID = DB::query(Database::SELECT, 'SELECT id,name FROM solarsystems WHERE LOWER(name) = :name')
															->param(':name', $name )->execute()->get('id', 0);


		return $systemID;
	}

	static function apiFetchCorp( $corpID )
	{
		PhealHelper::configure();
		$pheal = new Pheal(null,null,'corp');

		$result = $pheal->CorporationSheet( array( 'corporationID' => (int)$gm->eveID ) );
		$count = $result->memberCount;
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
		return date('Y-m-d H:i:s', $timestamp);
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


	static function isIGB()
	{
		if ( isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],'EVE-IGB') !== false )
		{
			return TRUE;
		}

		if ( isset($_SERVER['HTTP_EVE_TRUSTED']) )
		{
			return TRUE;
		}

		return FALSE;
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

	static function getTrust()
	{
		if ( self::isIGB() )
		{
			//because CCP cant use integers.
			if (!isset($_SERVER['HTTP_EVE_TRUSTED']) || strtolower($_SERVER['HTTP_EVE_TRUSTED']) == 'no')
			{
				return false;
			}
			else
			{
				return true;
			}
		}

		return false;
	}
}
