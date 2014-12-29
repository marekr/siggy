<?php 

class Date_Pager
{
	private $week = 0;
	private $month = 0;
	private $day = 0;
	private $year = 0;
	
	private $prevWeek = 0;
	private $prevMonth = 0;
	private $prevDay = 0;
	private $prevYear = 0;
	
	private $nextWeek = 0;
	private $nextMonth = 0;
	private $nextDay = 0;
	private $nextYear = 0;
	
	const MODEWEEKLY = 0;
	const MODEMONTHLY = 1;
	const MODEDAILY = 2;
	const MODEYEARLY = 3;
	
	public $mode = self::MODEWEEKLY;

	public function __construct( $mode, $day, $month, $year, $week )
	{
		$this->mode = $mode;
	
		if( $year != NULL )
		{
			$this->year = $year;
		}
		else
		{
			$this->year = date("o");
		}
		
		if( $this->mode == self::MODEWEEKLY )
		{
			if( $week != NULL )
			{
				$this->week = $week;
			}
			else
			{
				$this->week = date("W");
			}
			
			$now = strtotime($this->year."W".$this->week);
			
			$prevDate = strtotime('-1 week',$now);
			$this->prevWeek = date("W",$prevDate);
			$this->prevYear = date("o",$prevDate);
			
			$nextDate = strtotime('+1 week',$now);
			$this->nextWeek = date("W",$nextDate);
			$this->nextYear = date("o",$nextDate);
		}
		else if( $this->mode == self::MODEMONTHLY )
		{
			if( $month != NULL )
			{
				$this->month = $month;
			}
			else
			{
				$this->month = date("m");
			}
			
			$this->prevMonth = $this->month - 1;
			if( $this->prevMonth < 1 )
			{
				$this->prevMonth = 12;
				$this->prevYear = $this->year - 1;
			}
			else
			{
				$this->prevYear = $this->year;
			}
			
			$this->nextMonth = $this->month + 1;
			if( $this->nextMonth > 12 )
			{
				$this->nextMonth = 1;
				$this->nextYear = $this->year + 1;
			}
			else
			{
				$this->nextYear = $this->year;
			}
		}
		else if( $this->mode == self::MODEYEARLY )
		{
			$this->prevYear = $this->year -1;
			$this->nextYear = $this->year + 1;
		}
		else if( $this->mode == self::MODEDAILY )
		{
			$this->month = $month;
			$this->day = $day;

			$now = strtotime($this->month.'/'.$this->day.'/'.$this->year);

			$prev = getdate(strtotime('-1 day',$now));
			$this->prevDay = $prev['mday'];
			$this->prevMonth = $prev['mon'];
			$this->prevYear = $prev['year'];

			$next =  getdate(strtotime('+1 day',$now));
			$this->nextDay = $next['mday'];
			$this->nextMonth = $next['mon'];
			$this->nextYear = $next['year'];
		}
	}
	
	public function getTimestamps()
	{
		$start = 0;
		$end = 0;
		
		if( $this->mode == self::MODEWEEKLY )
		{
			list($start, $end) = $this->weekTimestamps( $this->week, $this->year );
		}
		else if( $this->mode == self::MODEMONTHLY )
		{
			list($start, $end) = $this->monthTimestamps( $this->month, $this->year );
		}
		else if( $this->mode == self::MODEYEARLY )
		{
			list($start, $end) = $this->yearTimestamps( $this->year );
		}
		else if( $this->mode == self::MODEDAILY )
		{
			list($start, $end) = $this->dayTimestamps( $this->day, $this->month, $this->year );
		}
		
		return array( 'start' => $start, 'end' => $end );
	}

	private function dayTimestamps($day, $month, $year)
	{
		$now = strtotime($month.'/'.$day.'/'.$year);

		$prev = strtotime("midnight", $now);

		$next = strtotime("tomorrow", $now) - 1;

		return array( $prev, $next );
	}
	
	private function monthTimestamps($month, $year)
	{
		$str = $month .'/1/'.$year;
		$dateStart = strtotime( $str );

		$end = strtotime($str. '+ 1 month - 1 day');
		return array( $dateStart, $end );
	}
	
	private function weekTimestamps($week, $year)
	{
		$start = strtotime($year."W".$week);
		return array( $start, strtotime('+6 days 23:59:59', $start) );
	}
	
	private function yearTimestamps($year)
	{
		$start = mktime(0,0,0,1,1,$year);
		$stop = mktime(0,0,0,1,0,$year+1);
		
		return array( $start, $stop );
	}	
	
	public function getPreviousDate()
	{
		if( $this->mode == self::MODEWEEKLY )
		{
			return array ('text' => 'Week ' . $this->prevWeek . ', ' . $this->prevYear, 'urlbit' => 'year/' . $this->prevYear . '/week/' . $this->prevWeek);
		}
		else if( $this->mode == self::MODEMONTHLY )
		{
			return array ('text' => $this->getMonthName( $this->prevMonth ) . ', ' . $this->prevYear, 'urlbit' => 'year/' . $this->prevYear . '/month/' . $this->prevMonth);
		}
		else if( $this->mode == self::MODEYEARLY )
		{
			return array ('text' => $this->prevYear, 'urlbit' => 'year/' . $this->prevYear);
		}
		else if( $this->mode == self::MODEDAILY )
		{
			return array ('text' => $this->getMonthName( $this->prevMonth ) . ' ' . $this->prevDay . ', ' . $this->prevYear, 'urlbit' => 'year/' . $this->prevYear . '/month/' . $this->prevMonth . '/day/' . $this->prevDay);
		}
	}
	
	public function getCurrentDate()
	{
		if( $this->mode == self::MODEWEEKLY )
		{
			return array ('text' => 'Week ' . $this->week . ', ' . $this->year, 'urlbit' => 'year/' . $this->year . '/week/' . $this->week);
		}
		else if( $this->mode == self::MODEMONTHLY )
		{
			return array ('text' => $this->getMonthName( $this->month ) . ', ' . $this->year, 'urlbit' => 'year/' . $this->year . '/month/' . $this->month);
		}
		else if( $this->mode == self::MODEYEARLY )
		{
			return array ('text' => $this->year, 'urlbit' => 'year/' . $this->year);
		}
		else if( $this->mode == self::MODEDAILY )
		{
			return array ('text' => $this->getMonthName( $this->month ) . ' ' . $this->day . ', ' . $this->year, 'urlbit' => 'year/' . $this->year . '/month/' . $this->month . '/day/' . $this->day);
		}
	}
	
	public function getNextDate()
	{
		if( $this->mode == self::MODEWEEKLY )
		{
			return array ('text' => 'Week ' . $this->nextWeek . ', ' . $this->nextYear, 'urlbit' => 'year/' . $this->nextYear . '/week/' . $this->nextWeek);
		}
		else if( $this->mode == self::MODEMONTHLY )
		{
			return array ('text' => $this->getMonthName( $this->nextMonth ) . ', ' . $this->nextYear, 'urlbit' => 'year/' . $this->nextYear . '/month/' . $this->nextMonth);
		}
		else if( $this->mode == self::MODEYEARLY )
		{
			return array ('text' => $this->nextYear, 'urlbit' => 'year/' . $this->nextYear);
		}
		else if( $this->mode == self::MODEDAILY )
		{
			return array ('text' => $this->getMonthName( $this->nextMonth ) . ' ' . $this->nextDay . ', ' . $this->nextYear, 'urlbit' => 'year/' . $this->nextYear . '/month/' . $this->nextMonth . '/day/' . $this->nextDay);
		}
	}
	
	public function getMonthName($monthInt)
	{
		return date( "F", strtotime( date('d-'.$monthInt.'-y') ) );
	}
}