<?php defined('SYSPATH') or die('No direct script access.');

require_once APPPATH.'classes/groupUtils.php';
require_once APPPATH.'classes/mapUtils.php';
require_once APPPATH.'classes/miscUtils.php';
require_once APPPATH.'classes/Zebra_Pagination2.php';

class Controller_Stats extends FrontController {
	private $auth;
	private $user;
	public $template = 'template/public_bootstrap32';

	function __construct(Kohana_Request $request, Kohana_Response $response)
	{
		parent::__construct($request, $response);
	}

	public function action_leaderboard()
	{
		$this->template->title = "siggy: leaderboard";

		$wrapper = View::factory('stats/stats_wrapper');

		$datep = $this->__setupDatePager();
		$wrapper->previous_date = $datep->getPreviousDate();
		$wrapper->current_date = $datep->getCurrentDate();
		$wrapper->next_date = $datep->getNextDate();
		$wrapper->stats_mode = $datep->mode;
		$wrapper->sub_page = 'leaderboard';

		$view = View::factory('stats/leaderboard');

		$dateRange = $datep->getTimestamps();

		//short names for vars for multipliers
		$view->wormhole = $wormhole = (double)Auth::$session->accessData['stats_wh_map_points'];
		$view->sig_add = $sig_add = (double)Auth::$session->accessData['stats_sig_add_points'];
		$view->sig_update = $sig_update = (double)Auth::$session->accessData['stats_sig_update_points'];
		$view->pos_add = $pos_add= (double)Auth::$session->accessData['stats_pos_add_points'];
		$view->pos_update = $pos_update = (double)Auth::$session->accessData['stats_pos_update_points'];

		$number_per_page = 25;

		$resultCount = DB::query(Database::SELECT, "SELECT COUNT(*) as total
											FROM
											(
												SELECT charID, (sum(wormholes) + sum(adds) + sum(updates) + sum(pos_adds)+sum(pos_updates)) as score
												FROM stats
												WHERE groupID=:group AND dayStamp >= :start AND dayStamp < :end
												GROUP BY charID
												HAVING score > 0
												ORDER BY score DESC
											) u")
								->param(':group', Auth::$session->groupID)
								->param(':start', $dateRange['start'])
								->param(':end', $dateRange['end'])
								->execute()
								->current();

		$pagination = new Zebra_Pagination2();
		$pagination->records($resultCount['total']);
		$pagination->records_per_page($number_per_page);

		$paginationHTML = $pagination->render(true);
		$offset = $pagination->next_page_offset();

		$results = DB::query(Database::SELECT, "SELECT charID, charName, ({$wormhole}*sum(wormholes) + {$sig_add}*sum(adds) + {$sig_update}*sum(updates) + {$pos_add}*sum(pos_adds)+{$pos_update}*sum(pos_updates)) as score,
											sum(wormholes) as wormholes,
											sum(adds) as adds,
											sum(updates) as updates,
											sum(pos_adds) as pos_adds,
											sum(pos_updates) as pos_updates
											FROM stats
											WHERE groupID=:group AND dayStamp >= :start AND dayStamp < :end
											GROUP BY charID
											HAVING score > 0
											ORDER BY score DESC
											LIMIT ".$offset.",".$number_per_page."
											")
								->param(':group', Auth::$session->groupID)
								->param(':start', $dateRange['start'])
								->param(':end', $dateRange['end'])
								->execute()
								->as_array();

		$view->results = $results;
		$view->rank_offset = $offset;
		$view->pagination = $paginationHTML;


		$wrapper->content = $view;
		$this->template->content = $wrapper;
	}

	public function before()
	{
		parent::before();

		$this->template->title = "siggy: stats";
		$this->template->selectedTab = 'stats';
		$this->template->loggedIn = Auth::loggedIn();
		$this->template->user = Auth::$user->data;
		$this->template->layoutMode = 'blank';
	}

	public function action_sig_updates()
	{
		$this->handle_keyed_stats('sig_updates');
	}

	public function action_sig_adds()
	{
		$this->handle_keyed_stats('sig_adds');
	}

	public function action_wormholes()
	{
		$this->handle_keyed_stats('wormholes');
	}

	public function action_pos_adds()
	{
		$this->handle_keyed_stats('pos_adds');
	}

	public function action_pos_updates()
	{
		$this->handle_keyed_stats('pos_updates');
	}

	private function handle_keyed_stats($key)
	{
		$wrapper = View::factory('stats/stats_wrapper');

		$keyLookup = array('sig_adds' => 'adds',
							'sig_updates' => 'updates',
							'pos_adds' => 'pos_adds',
							'wormholes' => 'wormholes',
							'pos_updates' => 'pos_updates'
							);
		$convertedKey = $keyLookup[$key];

		$datep = $this->__setupDatePager();
		$wrapper->previous_date = $datep->getPreviousDate();
		$wrapper->current_date = $datep->getCurrentDate();
		$wrapper->next_date = $datep->getNextDate();
		$wrapper->stats_mode = $datep->mode;
		$wrapper->sub_page = $key;

		$view = View::factory('stats/specific_stat');

		$dateRange = $datep->getTimestamps();


		$resultCount = DB::query(Database::SELECT, "SELECT COUNT(*) as total
											FROM
											(
													SELECT charID, charName, sum(".$convertedKey.") as value FROM stats
													WHERE groupID=:group AND dayStamp >= :start AND dayStamp < :end AND ".$convertedKey." != 0
													GROUP BY charID
													ORDER BY value DESC
											) u")
								->param(':group', Auth::$session->groupID)
								->param(':start', $dateRange['start'])
								->param(':end', $dateRange['end'])
								->execute()
								->current();

		$pagination = new Zebra_Pagination2();
		$pagination->records($resultCount['total']);
		$pagination->records_per_page(50);

		$paginationHTML = $pagination->render(true);
		$offset = $pagination->next_page_offset();

		$results = DB::query(Database::SELECT, "SELECT charID, charName, sum(".$convertedKey.") as value FROM stats
													WHERE groupID=:group AND dayStamp >= :start AND dayStamp < :end AND ".$convertedKey." != 0
													GROUP BY charID
													ORDER BY value DESC
													LIMIT ".$offset.",50")
										->param(':group', Auth::$session->groupID)
										->param(':start', $dateRange['start'])
										->param(':end', $dateRange['end'])
										->execute()
										->as_array();

		$view->results = $results;
		$view->rank_offset = $offset;
		$view->pagination = $paginationHTML;
		$wrapper->content = $view;
		$this->template->content = $wrapper;
	}

	private function __setupDatePager()
	{
		$year = $this->request->param('year', NULL);
		$month = $this->request->param('month', NULL);
		$day = $this->request->param('day', NULL);
		$week = $this->request->param('week', NULL);

		$mode = DatePager::MODEWEEKLY;
		if( $month != NULL )
		{
			$mode = DatePager::MODEMONTHLY;
		}
		else if( $day != NULL )
		{
			$mode = DatePager::MODEWEEKLY;
		}
		else if( $year != NULL && $week == NULL )
		{
			$mode = DatePager::MODEYEARLY;
		}

		return new DatePager($mode, $day, $month, $year, $week);
	}

	public function action_overview()
	{
		$this->template->title = "siggy: stats";

		$wrapper = View::factory('stats/stats_wrapper');
		$view = View::factory('stats/stats');

		$datep = $this->__setupDatePager();

		$wrapper->previous_date = $datep->getPreviousDate();
		$wrapper->current_date = $datep->getCurrentDate();
		$wrapper->next_date = $datep->getNextDate();
		$wrapper->stats_mode = $datep->mode;
		$wrapper->sub_page = 'overview';

		$dateRange = $datep->getTimestamps();

		$top10Adds = $this->getTop10('adds',$dateRange['start'],$dateRange['end']);
		$addsHTML = View::factory('stats/top10');
		$addsHTML->max = $top10Adds['max'];
		$addsHTML->data = $top10Adds['top10'];
		$addsHTML->title = "Signatures added";

		$top10Edits = $this->getTop10('updates',$dateRange['start'],$dateRange['end']);
		$editsHTML = View::factory('stats/top10');
		$editsHTML->max = $top10Edits['max'];
		$editsHTML->data = $top10Edits['top10'];
		$editsHTML->title = "Signatures updated";


		$top10WHs = $this->getTop10('wormholes',$dateRange['start'],$dateRange['end']);
		$whsHTML = View::factory('stats/top10');
		$whsHTML->max = $top10WHs['max'];
		$whsHTML->data = $top10WHs['top10'];
		$whsHTML->title = "Wormholes mapped";


		$view->addsHTML = $addsHTML;
		$view->editsHTML = $editsHTML;
		$view->whsHTML = $whsHTML;

		$wrapper->content = $view;

		$this->template->content = $wrapper;
	}

	private function getTop10($key, $start, $end)
	{
		if( !in_array($key,array('wormholes','updates','adds') ) )
		{
			throw new Exception("Invalid stat key");
		}

		$groupTop10 = DB::query(Database::SELECT, "SELECT charID, charName, sum(".$key.") as value FROM stats
													WHERE groupID=:group AND dayStamp >= :start AND dayStamp < :end AND ".$key." != 0
													GROUP BY charID
													ORDER BY value DESC LIMIT 0,10")
										->param(':group', Auth::$session->groupID)
										->param(':start', $start)
										->param(':end', $end)
										->execute()
										->as_array();

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
}
