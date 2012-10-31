<?php

final class miscUtils
{

		const TIER1COST = 38000;
		const TIER2COST = 35000;
		const TIER3COST = 30000;

		static function computeCostPerDays( $memberCount, $days )
		{
			$total = 0;
			$membersInTier = 0;
			
			if ( $memberCount > 200 )
			{
				$membersInTier = $memberCount - 200;
				$memberCount -= $membersInTier;
				
				$total += $membersInTier * self::TIER3COST;
				//print "tier3count: ". $membersInTier ."<br />"; 
				//print "tier3cost: ". $membersInTier * self::TIER3COST ."<br />"; 
			}
			
			if ( $memberCount > 100 )
			{
				$membersInTier = $memberCount - 100;
				$memberCount -= $membersInTier;
				
				$total += $membersInTier * self::TIER2COST;
			//	print "tier2count: ". $membersInTier ."<br />"; 
				//print "tier2cost: ". $membersInTier * self::TIER2COST ."<br />"; 
			}
			
			$total += $memberCount * self::TIER1COST;
			//	print "tier1count: ". $memberCount ."<br />"; 
				//print "tier1cost: ". $memberCount * self::TIER1COST ."<br />"; 
			
			return $total*$days;
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
				return FALSE;
		}
		
		static function generateString($length = 14) 
		{
			$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
			$randomString = '';
			for ($i = 0; $i < $length; $i++) 
			{
				$randomString .= $characters[rand(0, strlen($characters) - 1)];
			}
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
					 if (strtolower($_SERVER['HTTP_EVE_TRUSTED'])=='no') 
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