<?php defined('SYSPATH') or die('No direct script access.');

use Illuminate\Database\Capsule\Manager as DB;

class Controller_Stats extends FrontController {

	function __construct(Kohana_Request $request, Kohana_Response $response)
	{
		parent::__construct($request, $response);
	}

	public function action_leaderboard()
	{
		$datep = $this->__setupDatePager();


		$dateRange = $datep->getTimestamps();

		//short names for vars for multipliers
		$wormhole = (double)Auth::$session->group->stats_wh_map_points;
		$sig_add = (double)Auth::$session->group->stats_sig_add_points;
		$sig_update = (double)Auth::$session->group->stats_sig_update_points;
		$pos_add= (double)Auth::$session->group->stats_pos_add_points;
		$pos_update = (double)Auth::$session->group->stats_pos_update_points;

		$number_per_page = 25;

		$resultCount = DB::selectOne("SELECT COUNT(*) as total
											FROM
											(
												SELECT charID, (sum(wormholes) + sum(adds) + sum(updates) + sum(pos_adds)+sum(pos_updates)) as score
												FROM stats
												WHERE groupID=:group AND dayStamp >= :start AND dayStamp < :end
												GROUP BY charID
												HAVING score > 0
												ORDER BY score DESC
											) u",[
												'group' => Auth::$session->group->id,
												'start' => $dateRange['start'],
												'end' => $dateRange['end']
											]);

		$pagination = new ZebraPagination2();
		$pagination->records($resultCount->total);
		$pagination->records_per_page($number_per_page);

		$paginationHTML = $pagination->render(true);
		$offset = $pagination->next_page_offset();

		$results = DB::select("SELECT charID, charName, ({$wormhole}*sum(wormholes) + {$sig_add}*sum(adds) + 
											{$sig_update}*sum(updates) + {$pos_add}*sum(pos_adds)+{$pos_update}*sum(pos_updates)) as score,
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
											",[
												'group' => Auth::$session->group->id,
												'start' => $dateRange['start'],
												'end' => $dateRange['end']
											]);

		$resp =  view('stats.leaderboard', [
											'wormhole' => (double)Auth::$session->group->stats_wh_map_points,
											'sig_add' => (double)Auth::$session->group->stats_sig_add_points,
											'sig_update' => (double)Auth::$session->group->stats_sig_update_points,
											'pos_add' => (double)Auth::$session->group->stats_pos_add_points,
											'pos_update' => (double)Auth::$session->group->stats_pos_update_points,
											'results' => $results,
											'rank_offset' => $offset,
											'pagination' => $paginationHTML,
											'title' => 'siggy: leaderboard',
											'selectedTab' => 'stats',
											'layoutMode' => 'blank',
											'previous_date' => $datep->getPreviousDate(),
											'current_date' => $datep->getCurrentDate(),
											'next_date' => $datep->getNextDate(),
											'stats_mode' => $datep->mode,
											'sub_page' => 'leaderboard',
											'settings' => $this->loadSettings(),
											'group' => Auth::$session->group,
										]);
		$this->response->body($resp);
	}

	public function before()
	{
		parent::before();
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
		$keyLookup = array('sig_adds' => 'adds',
							'sig_updates' => 'updates',
							'pos_adds' => 'pos_adds',
							'wormholes' => 'wormholes',
							'pos_updates' => 'pos_updates'
							);
		$convertedKey = $keyLookup[$key];

		$datep = $this->__setupDatePager();

		$dateRange = $datep->getTimestamps();


		$resultCount = DB::selectOne("SELECT COUNT(*) as total
											FROM
											(
													SELECT charID, charName, sum(".$convertedKey.") as value FROM stats
													WHERE groupID=:group AND dayStamp >= :start AND dayStamp < :end AND ".$convertedKey." != 0
													GROUP BY charID
													ORDER BY value DESC
											) u",[
												'group' => Auth::$session->group->id,
												'start' => $dateRange['start'],
												'end' => $dateRange['end']
											]);

		$pagination = new ZebraPagination2();
		$pagination->records($resultCount->total);
		$pagination->records_per_page(50);

		$paginationHTML = $pagination->render(true);
		$offset = $pagination->next_page_offset();

		$results = DB::select("SELECT charID, charName, sum(".$convertedKey.") as value FROM stats
													WHERE groupID=:group AND dayStamp >= :start AND dayStamp < :end AND ".$convertedKey." != 0
													GROUP BY charID
													ORDER BY value DESC
													LIMIT ".$offset.",50",[
												'group' => Auth::$session->group->id,
												'start' => $dateRange['start'],
												'end' => $dateRange['end']
											]);

		$resp =  view('stats.specific_stat', [
											'wormhole' => (double)Auth::$session->group->stats_wh_map_points,
											'sig_add' => (double)Auth::$session->group->stats_sig_add_points,
											'sig_update' => (double)Auth::$session->group->stats_sig_update_points,
											'pos_add' => (double)Auth::$session->group->stats_pos_add_points,
											'pos_update' => (double)Auth::$session->group->stats_pos_update_points,
											'results' => $results,
											'rank_offset' => $offset,
											'pagination' => $paginationHTML,

											'title' => 'siggy: leaderboard',
												'selectedTab' => 'stats',
												'layoutMode' => 'blank',
												'previous_date' => $datep->getPreviousDate(),
												'current_date' => $datep->getCurrentDate(),
												'next_date' => $datep->getNextDate(),
												'stats_mode' => $datep->mode,
												'sub_page' => $key,
												'settings' => $this->loadSettings(),
												'group' => Auth::$session->group,
										]);
		$this->response->body($resp);
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
		$datep = $this->__setupDatePager();
		$dateRange = $datep->getTimestamps();

		$top10Adds = $this->getTop10('adds',$dateRange['start'],$dateRange['end']);
		$top10Edits = $this->getTop10('updates',$dateRange['start'],$dateRange['end']);
		$top10WHs = $this->getTop10('wormholes',$dateRange['start'],$dateRange['end']);

		$resp = view('stats.stats', [
												'top10Adds' => $top10Adds,
												'top10Edits' => $top10Edits,
												'top10WHs' => $top10WHs,
												'title' => 'siggy:stats',
												'selectedTab' => 'stats',
												'layoutMode' => 'blank',
												'previous_date' => $datep->getPreviousDate(),
												'current_date' => $datep->getCurrentDate(),
												'next_date' => $datep->getNextDate(),
												'stats_mode' => $datep->mode,
												'sub_page' => 'overview',
												'settings' => $this->loadSettings(),
												'group' => Auth::$session->group,
											]);
		$this->response->body($resp);
	}

	private function getTop10($key, $start, $end)
	{
		if( !in_array($key,array('wormholes','updates','adds') ) )
		{
			throw new Exception("Invalid stat key");
		}

		$groupTop10 = DB::select( "SELECT charID, charName, sum(".$key.") as value FROM stats
													WHERE groupID=:group AND dayStamp >= :start AND dayStamp < :end AND ".$key." != 0
													GROUP BY charID
													ORDER BY value DESC LIMIT 0,10",[
												'group' => Auth::$session->group->id,
												'start' => $start,
												'end' => $end
											]);

		$max = 0;
		if( count($groupTop10 ) > 0 )
		{
			foreach( $groupTop10 as &$p )
			{
				if( $max < $p->value )
				{
					$max = $p->value;
				}
			}
		}

		return array( 'max' => $max, 'top10' => $groupTop10 );
	}
}
