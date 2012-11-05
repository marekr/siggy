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
		$this->user = $this->auth->get_user();
		
		parent::__construct($request, $response);
	}
		
	public function action_index()
	{
		$this->template->title = "siggy: stats";
		$this->template->selectedTab = 'home';
		$this->template->loggedIn = $this->auth->logged_in();
		$this->template->user = $this->user;
		$this->template->layoutMode = 'blank';
		
		$week = intval($this->request->param('week'));
		$year = intval($this->request->param('year'));
		
		if( empty($week) && !empty($year) )
		{
			$dateRange = array( 0 => strtotime("1.1.".$year),
								1 => strtotime("1.1.".($year+1))-1
								);
		}
		else
		{
			if( empty($week) && empty($year) )
			{
				$year = date("Y");
				$week = date("W");
			}
			$dateRange = $this->weekTimestamps($week, $year);
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
		
		
		$view = View::factory('stats/weeklyBody');
		$view->addsHTML = $addsHTML;
		$view->editsHTML = $editsHTML;
		$view->whsHTML = $whsHTML;
		$view->week = $week;
		$view->year = $year;
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
	
	function weekTimestamps($week, $year)
	{
		$start = strtotime("1.1.".$year." +".($week-1)." weeks +1 day");
		return array( $start, strtotime('next monday', $start)-1 );
	}	

}

?>