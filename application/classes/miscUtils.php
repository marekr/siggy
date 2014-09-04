<?php

final class miscUtils
{
	public static $anomsLookup = array( 	1 => array( 0 => "",
													1 =>  "Perimeter Ambush Point",
													2 =>  "Perimeter Camp",
													3 =>  "Phase Catalyst Node",
													4 =>  "The Line"
													),
										2 => array(
											0 => "",
											1 => "Perimeter Checkpoint",
											2 => "Perimeter Hangar",
											3 => "The Ruins of Enclave Cohort 27",
											4 => "Sleeper Data Sanctuary"
											),
										3 => array(
											0 => "",
											1 => "Fortification Frontier Stronghold",
											2 => "Outpost Frontier Stronghold",
											3 => "Solar Cell",
											4 => "The Oruze Construct"
											),
										4 => array(
											0 => "",
											1 => "Frontier Barracks",
											2 => "Frontier Command Post",
											3 => "Integrated Terminus",
											4 => "Sleeper Information Sanctum"
											),
										5 => array(
											0 => "",
											1 => "Quarantine Area",
											2 => "Core Garrison",
											3 => "Core Stronghold",
											4 => "Oruze Osobnyk"
											),
										6 => array(
											0  => "",
											1 => "Core Citadel",
											2 => "Core Bastion",
											3 => "Strange Energy Readings",
											4 => "The Mirror" 
											),
									);


	public static $magsLookup = array( 	1 => array( 0 => "",
										 1 => "Forgotten Perimeter Coronation Platform",
										 2 => "Forgotten Perimeter Power Array"
										),
							2 => array(
								0  => "",
								1  => "Forgotten Perimeter Gateway",
								2  => "Forgoten Perimeter Habitation Coils"
										),
							3 => array(
								0  => "",
								1  => "Forgotten Frontier Quarantine Outpost",
								2  => "Forgotten Frontier Recursive Depot"
										),
							4 => array(
								0  => "",
								1  => "Forgotten Frontier Conversion Module",
								2  => "Forgotten Frontier Evacuation Center"
										),
							5 => array(
								0  => "",
								1  => "Forgotten Core Data Field",
								2  => "Forgotten Core Information Pen"
										),
							6 => array(
								0  => "",
								1  => "Forgotten Core Assembly Hall",
								2  => "Forgotten Circuitry Disassembler"
										)
						);

	public static $radarsLookup = array( 	1 => array( 0 => "",
												 1 => "Unsecured Perimeter Amplifier",
												 2 => "Unsecured Perimeter Information Center"
												),
											2 => array(
												0  => "",
												1  => "Unsecured Perimeter Comms Relay",
												2  => "Unsecured Transponder Farm"
														),
											3 => array(
												0  => "",
												1  => "Unsecured Frontier Database",
												2  => "Unsecured Frontier Receiver"
														),
											4 => array(
												0  => "",
												1  => "Unsecured Frontier Digital Nexus",
												2  => "Unsecured Frontier Trinary Hub"
														),
											5 => array(
												0  => "",
												1  => "Unsecured Frontier Enclave Relay",
												2  => "Unsecured Frontier Server Bank"
														),
											6 => array(
												0  => "",
												1  => "Unsecured Core Backup Array",
												2  => "Unsecured Core Backup Array"
														)
						);

	public static $gravsLookup = array(
		0 => "",
		1 => "Average Frontier Deposit",
		2 => "Unexceptional Frontier Deposit",
		3 => "Common Perimeter Deposit",
		4 => "Exceptional Core Deposit",
		5 => "Infrequent Core Deposit",
		6 => "Unusual Core Deposit",
		7 => "Rarified Core Deposit",
		8 => "Ordinary Perimeter Deposit",
		9 => "Uncommon Core Deposit",
		10 => "Isolated Core Deposit"
	);

	public static $ladarsLookup = array(
		0 => "",
		1 => "Barren Perimeter Reservoir",
		2 => "Minor Perimeter Reservoir",
		3 => "Ordinary Perimeter Reservoir",
		4 => "Sizeable Perimeter Reservoir",
		5 => "Token Perimeter Reservoir",
		6 => "Bountiful Frontier Reservoir",
		7 => "Vast Frontier Reservoir",
		8 => "Instrumental Core Reservoir",
		9 => "Vital Core Reservoir"
	);


	const TIER1COST = 33000;
	const TIER2COST = 29000;
	const TIER3COST = 25000;

	static function computeCostPerDays( $memberCount, $days )
	{
		$total = 22420*$memberCount + 283650;
		return $total*$days;
	}

	static function parseIngameSigExport( $string )
	{
		$resultingSigs = array();

		$lines = explode("\n", $string);
		foreach( $lines as $line )
		{
			$data = explode("\t", $line);
			if( count($data) < 2 )
			{
				continue;
			}

			$sigData = array('type' => 'none', 'sig' => '', 'siteID' => 0);

			$matches = array();

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
				
				preg_match("/^(Wormhole|Data Site|Gas Site|Relic Site|Ore Site|Combat Site)$/", $item, $matches );
				if( count($matches) == 2 )
				{
					switch( $matches[1] )
					{
						case 'Wormhole':
							$sigData['type'] = 'wh';
							break;
						case 'Data Site':
							$sigData['type'] = 'radar';
							$sigData['siteID'] = self::siteIDLookupByName( $data[$k+1], 'radar' );
							break;
						case 'Gas Site':
							$sigData['type'] = 'ladar';
							$sigData['siteID'] = self::siteIDLookupByName( $data[$k+1], 'ladar' );
							break;
						case 'Relic Site':
							$sigData['type'] = 'mag';
							$sigData['siteID'] = self::siteIDLookupByName( $data[$k+1], 'mag' );
							break;
						case 'Ore Site':
							$sigData['type'] = 'grav';
							$sigData['siteID'] = self::siteIDLookupByName( $data[3], 'grav' );
							break;
						case 'Combat Site':
							$sigData['type'] = 'combat';
							$sigData['siteID'] = self::siteIDLookupByName( $data[$k+1], 'anoms' );
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
		if( $type == 'grav' )
		{
			foreach( self::$gravsLookup as $k => $grav )
			{
				if( $grav == $name )
				{
					return $k;
				}
			}
		}
		else if( $type == 'ladar' )
		{
			foreach( self::$ladarsLookup as $k => $ladar )
			{
				if( $ladar == $name )
				{
					return $k;
				}
			}

		}
		else if( $type == 'radar' )
		{
			foreach( self::$radarsLookup as $c  )
			{
				foreach( $c as $k => $radar )
				{
					if( $radar == $name )
					{
						return $k;
					}
				}
			}
		}
		else if( $type == 'mag' )
		{
			foreach( self::$magsLookup as $c  )
			{
				foreach( $c as $k => $mag )
				{
					if( $mag == $name )
					{
						return $k;
					}
				}
			}
		}
		else if( $type == 'anoms' )
		{
			foreach( self::$anomsLookup as $c  )
			{
				foreach( $c as $k => $mag )
				{
					if( $mag == $name )
					{
						return $k;
					}
				}
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

		require_once( Kohana::find_file('vendor', 'pheal/Pheal') );
		spl_autoload_register( "Pheal::classload" );
		PhealConfig::getInstance()->cache = new PhealFileCache(APPPATH.'cache/api/');
		PhealConfig::getInstance()->http_ssl_verifypeer = false;
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
			catch( PhealException $e )
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
							->param(':charID',  $groupData['charID'] )
							->param(':charName', $groupData['charName'] )
							->param(':groupID', $groupData['groupID'] )
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
		require_once( Kohana::find_file('vendor', 'pheal/Pheal') );
		spl_autoload_register( "Pheal::classload" );
		PhealConfig::getInstance()->cache = new PhealFileCache(APPPATH.'cache/api/');
		PhealConfig::getInstance()->http_ssl_verifypeer = false;
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
