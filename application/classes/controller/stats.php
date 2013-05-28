<?php defined('SYSPATH') or die('No direct script access.');

require_once APPPATH.'classes/access.php';
require_once APPPATH.'classes/groupUtils.php';
require_once APPPATH.'classes/mapUtils.php';
require_once APPPATH.'classes/miscUtils.php';

class Controller_Stats extends FrontController
{
	private $auth;
	private $user;
	public $template = 'template/public';

	function __construct(Kohana_Request $request, Kohana_Response $response)
	{
		$this->auth = simpleauth::instance();
		
		parent::__construct($request, $response);
	}
		
	public function action_index()
	{
		$this->template->title = "siggy: stats";
		$this->template->selectedTab = 'home';
		$this->template->loggedIn = $this->auth->logged_in();
		$this->template->user = Auth::$user->data;
		$this->template->layoutMode = 'blank';
		
		$view = View::factory('stats/stats');
		
		
		$week = intval($this->request->param('week'));
		$month = intval($this->request->param('month'));
		$year = intval($this->request->param('year'));
		
		if( empty($week) && !empty($year) && empty($month) )
		{
			$dateRange = array( 0 => strtotime("1.1.".$year),
								1 => strtotime("1.1.".($year+1))-1
								);
								
			$statsMode = 'yearly';
			
			$yearlyPrevYear = $year - 1;
			$yearlyNextYear = $year + 1;
			
			$view->yearlyPrevYear = $yearlyPrevYear;
			$view->yearlyNextYear = $yearlyNextYear;
			
		}
		else if( !empty($year) && !empty($month) )
		{
			$dateRange = $this->monthTimestamps($month, $year);
		
			if( $month - 1 == 0 )
			{
				$monthlyPrevMonth = 12;
				$monthlyPrevYear = $year-1;
			}
			else
			{
				$monthlyPrevMonth = $month - 1;
				$monthlyPrevYear = $year;
			}
			$view->monthlyPrevMonth = $monthlyPrevMonth;
			$view->monthlyPrevMonthName = date("F", mktime(0, 0, 0, $monthlyPrevMonth, 10));
			$view->monthlyPrevYear = $monthlyPrevYear;
		
			if( $month + 1 == 13 )
			{
				$monthlyNextMonth = 1;
				$monthlyNextYear = $year+1;
			}
			else
			{
				$monthlyNextMonth = $month + 1;
				$monthlyNextYear = $year;
			}
			
			$view->monthlyNextMonth = $monthlyNextMonth;
			$view->monthlyNextMonthName = date("F", mktime(0, 0, 0, $monthlyNextMonth, 10));
			$view->monthlyNextYear = $monthlyNextYear;
		
			$statsMode = 'monthly';	
		}
		else
		{
			if( empty($week) && empty($year) )
			{
				$year = date("Y");
				$week = date("W");
			}
			
			$statsMode = 'weekly';
			
			$dateRange = $this->weekTimestamps($week, $year);
			
			if( $week-1 == 0 )
			{
				$weeklyPrevWeek = $this->getIsoWeeksInYear($year-1);
				$weeklyPrevYear = $year-1;
			}
			else
			{
				$weeklyPrevWeek = $week-1;
				$weeklyPrevYear = $year;
			}
			$view->weeklyPrevWeek = $weeklyPrevWeek;
			$view->weeklyPrevYear = $weeklyPrevYear;
			
			if( $week+1 >= $this->getIsoWeeksInYear($year) )
			{
				$weeklyNextWeek = 1;
				$weeklyNextYear = $year+1;
			}
			else
			{
				$weeklyNextWeek = $week+1;
				$weeklyNextYear = $year;
			}
			$view->weeklyNextWeek = $weeklyNextWeek;
			$view->weeklyNextYear = $weeklyNextYear;
		}
		
		$top10Adds = $this->getTop10Adds($dateRange[0],$dateRange[1]);
		$addsHTML = View::factory('stats/top10');
		$addsHTML->max = $top10Adds['max'];
		$addsHTML->data = $top10Adds['top10'];
		$addsHTML->title = "Signatures added";
		

		$top10Edits = $this->getTop10Edits($dateRange[0],$dateRange[1]);
		$editsHTML = View::factory('stats/top10');
		$editsHTML->max = $top10Edits['max'];
		$editsHTML->data = $top10Edits['top10'];
		$editsHTML->title = "Signatures updated";
		
		
		$top10WHs = $this->getTop10WHs($dateRange[0],$dateRange[1]);
		$whsHTML = View::factory('stats/top10');
		$whsHTML->max = $top10WHs['max'];
		$whsHTML->data = $top10WHs['top10'];
		$whsHTML->title = "Wormholes mapped";
		
		
		$view->addsHTML = $addsHTML;
		$view->editsHTML = $editsHTML;
		$view->whsHTML = $whsHTML;
		$view->week = $week;
		$view->month = $month;
		$view->monthName = date("F", mktime(0, 0, 0, $month, 10));
		$view->year = $year;
		$view->statsMode = $statsMode;
		$this->template->content = $view;	
	}
	
	private function getTop10WHs($start, $end)
	{
		//adds
		$groupTop10WHs = DB::query(Database::SELECT, "SELECT charID, charName, sum(wormholes) as value FROM stats WHERE groupID=:group AND dayStamp >= :start AND dayStamp < :end AND wormholes != 0 GROUP BY charID  ORDER BY value DESC LIMIT 0,10")
										->param(':group', $this->groupData['groupID'])->param(':start', $start)->param(':end', $end)->execute()->as_array();	
		

		$max = 0;
		if( count($groupTop10WHs ) > 0 )
		{
			foreach( $groupTop10WHs as &$p )
			{
				if( $max < $p['value'] )
				{
					$max = $p['value'];
				}
			}
		}		
		
		return array( 'max' => $max, 'top10' => $groupTop10WHs );
	}
	
	private function getTop10Edits($start, $end)
	{
		//adds
		$groupTop10Edits = DB::query(Database::SELECT, "SELECT charID, charName, sum(updates) as value FROM stats WHERE groupID=:group AND dayStamp >= :start AND dayStamp < :end AND updates != 0 GROUP BY charID  ORDER BY value DESC LIMIT 0,10")
										->param(':group', $this->groupData['groupID'])->param(':start', $start)->param(':end', $end)->execute()->as_array();	 
		
		$max = 0;
		if( count($groupTop10Edits ) > 0 )
		{
			foreach( $groupTop10Edits as &$p )
			{
				if( $max < $p['value'] )
				{
					$max = $p['value'];
				}
			}
		}		
		
		return array( 'max' => $max, 'top10' => $groupTop10Edits );
	}
	
	private function getTop10Adds($start, $end)
	{
		//adds
		$groupTop10 = DB::query(Database::SELECT, "SELECT charID, charName, sum(adds) as value FROM stats WHERE groupID=:group AND dayStamp >= :start AND dayStamp < :end AND adds != 0 GROUP BY charID  ORDER BY value DESC LIMIT 0,10")
										->param(':group', $this->groupData['groupID'])->param(':start', $start)->param(':end', $end)->execute()->as_array();	 
		

		$max = 0;
		if( count($groupTop10 ) > 0 )
		{
			foreach( $groupTop10 as &$p )
			{
				if( $max < $p['value'] )
				{
					$max = $p['value'];
				}
			}	
		}
		
		return array( 'max' => $max, 'top10' => $groupTop10 );
	}
	function monthTimestamps($month, $year)
	{
		$start = strtotime('first day of 1.'.$month.'.'.$year.' +1 day');
		return array( $start, strtotime('last day of 1.'.$month.'.'.$year));
	}	
	
	function weekTimestamps($week, $year)
	{
		$start = strtotime("+".($week-1)." weeks -1 day", strtotime("1.1.".$year));
		return array( $start, strtotime('next monday', $start)-1 );
	}	
	
	function getIsoWeeksInYear($year) {
		$date = new DateTime;
		$date->setISODate($year, 53);
		return ($date->format("W") === "53" ? 53 : 52);
	}
}

?>