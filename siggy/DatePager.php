<?php

namespace Siggy;

use Carbon\Carbon;

class DatePager
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

		$dtNow = Carbon::now();

		if( $year != NULL )
		{
			$this->year = $year;
		}
		else
		{
			$this->year = $dtNow->year;
		}

		if( $this->mode == self::MODEWEEKLY )
		{
			if( $week != NULL )
			{
				$this->week = $week;
			}
			else
			{
				$this->week = $dtNow->weekOfYear;
			}

			$this->prevWeek = $this->week - 1;
			if( $this->prevWeek < 1 )
			{
				$this->prevYear = $this->year -1;
				$this->prevWeek = 53;
			}
			else
			{
				$this->prevYear = $this->year;
			}

			$this->nextWeek = $this->week + 1;
			if( $this->nextWeek > 53 )
			{
				$this->nextWeek = 1;
				$this->nextYear = $this->year + 1;
			}
			else
			{
				$this->nextYear = $this->year;
			}
		}
		else if( $this->mode == self::MODEMONTHLY )
		{
			if( $month != NULL )
			{
				$this->month = $month;
			}
			else
			{
				$this->month = $dtNow->month;
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

			$dtNow = Carbon::createFromDate($this->year, $this->month, $this->day);

			$prev = $dtNow->copy()->subDay();

			$this->prevDay = $prev->day;
			$this->prevMonth = $prev->month;
			$this->prevYear = $prev->year;

			$next = $dtNow->copy()->addDay();
			$this->nextDay = $next->day;
			$this->nextMonth = $next->month;
			$this->nextYear = $next->year;
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
		$base = Carbon::create($year, $month, $day, 0, 0);
		$start = $base->startOfDay()->timestamp;
		$stop = $base->endOfDay()->timestamp;

		return [$start, $stop];
	}

	private function monthTimestamps($month, $year)
	{
		$base = Carbon::create($year, $month, 1, 0, 0);
		$start = $base->startOfMonth()->startOfDay()->timestamp;
		$stop = $base->endOfMonth()->endOfDay()->timestamp;

		return [$start, $stop];
	}

	private function weekTimestamps($week, $year)
	{
		$base = Carbon::now();
		$base->setISODate($year,$week);
		$start = $base->copy()->startOfWeek()->startOfDay()->timestamp;
		$stop = $base->copy()->endOfWeek()->endOfDay()->timestamp;
		return [$start, $stop];
	}

	private function yearTimestamps($year)
	{
		$base = Carbon::create($year, 1, 1, 0, 0);
		$start = $base->startOfYear()->startOfDay()->timestamp;
		$stop = $base->endOfYear()->endOfDay()->timestamp;

		return [$start, $stop];
	}

	public function getPreviousDate()
	{
		if( $this->mode == self::MODEWEEKLY )
		{
			return  [
					'text' => 'Week ' . $this->prevWeek . ', ' . $this->prevYear, 
					'urlbit' => 'year/' . $this->prevYear . '/week/' . $this->prevWeek,
					'urlargs' => ['year' => $this->prevYear, 'week' => $this->prevWeek]
					];
		}
		else if( $this->mode == self::MODEMONTHLY )
		{
			return  [
				'text' => 'Month ' . $this->getMonthName( $this->prevMonth ) . ', ' . $this->prevYear, 
				'urlbit' => 'year/' . $this->prevYear . '/month/' . $this->prevMonth,
				'urlargs' => ['year' => $this->prevYear, 'month' => $this->prevMonth]
				];
		}
		else if( $this->mode == self::MODEYEARLY )
		{
			return  [
				'text' => $this->prevYear, 
				'urlbit' => 'year/' . $this->prevYear, 
				'urlargs' => ['year' => $this->prevYear]
				];
		}
		else if( $this->mode == self::MODEDAILY )
		{
			return  [
				'text' => $this->getMonthName( $this->prevMonth ) . ' ' . $this->prevDay . ', ' . $this->prevYear, 
				'urlbit' => 'year/' . $this->prevYear . '/month/' . $this->prevMonth . '/day/' . $this->prevDay,
				'urlargs' => ['year' => $this->prevYear, 'month' => $this->prevMonth,'day'=> $this->prevDay]
				];
		}
	}

	public function getCurrentDate()
	{
		if( $this->mode == self::MODEWEEKLY )
		{
			return  ['text' => 'Week ' . $this->week . ', ' . $this->year, 
								'urlbit' => 'year/' . $this->year . '/week/' . $this->week,
								'urlargs' => ['year' => $this->year, 'week' => $this->week]
								];
		}
		else if( $this->mode == self::MODEMONTHLY )
		{
			return  [
				'text' => 'Month ' . $this->getMonthName( $this->month ) . ', ' . $this->year, 
				'urlbit' => 'year/' . $this->year . '/month/' . $this->month,
				'urlargs' => ['year' => $this->year, 'month' => $this->month]
				];
		}
		else if( $this->mode == self::MODEYEARLY )
		{
			return  [
				'text' => $this->year, 
				'urlbit' => 'year/' . $this->year, 
				'urlargs' => ['year' => $this->year]
				];
		}
		else if( $this->mode == self::MODEDAILY )
		{
			return  [
				'text' => $this->getMonthName( $this->month ) . ' ' . $this->day . ', ' . $this->year, 
				'urlbit' => 'year/' . $this->year . '/month/' . $this->month . '/day/' . $this->day,
				'urlargs' => ['year' => $this->year, 'month' => $this->month,'day'=> $this->day]
				];
		}
	}

	public function getNextDate()
	{
		if( $this->mode == self::MODEWEEKLY )
		{
			return  [
				'text' => 'Week ' . $this->nextWeek . ', ' . $this->nextYear, 
				'urlbit' => 'year/' . $this->nextYear . '/week/' . $this->nextWeek,
				'urlargs' => ['year' => $this->nextYear, 'week' => $this->nextWeek]
				];
		}
		else if( $this->mode == self::MODEMONTHLY )
		{
			return  [
				'text' => 'Month ' . $this->getMonthName( $this->nextMonth ) . ', ' . $this->nextYear, 
				'urlbit' => 'year/' . $this->nextYear . '/month/' . $this->nextMonth,
				'urlargs' => ['year' => $this->nextYear, 'month' => $this->nextMonth]
				];
		}
		else if( $this->mode == self::MODEYEARLY )
		{
			return  [
				'text' => $this->nextYear, 
				'urlbit' => 'year/' . $this->nextYear, 
				'urlargs' => ['year' => $this->nextYear]
				];
		}
		else if( $this->mode == self::MODEDAILY )
		{
			return  [
				'text' => $this->getMonthName( $this->nextMonth ) . ' ' . $this->nextDay . ', ' . $this->nextYear, 
				'urlbit' => 'year/' . $this->nextYear . '/month/' . $this->nextMonth . '/day/' . $this->nextDay,
				'urlargs' => ['year' => $this->nextYear, 'month' => $this->nextMonth,'day'=> $this->nextDay]
			];
		}
	}

	public function getMonthName($monthInt)
	{
		return date( "F", strtotime( date('d-'.$monthInt.'-y') ) );
	}
}
