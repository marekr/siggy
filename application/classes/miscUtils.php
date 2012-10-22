<?php

final class miscUtils
{

		static function findSystemByName($name)
		{
				$systemID = DB::query(Database::SELECT, 'SELECT id,name FROM solarsystems WHERE LOWER(name) = :name')
																	->param(':name', $name )->execute()->get('id', 0);
																	
				
				return $systemID;
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