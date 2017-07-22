<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Facades\Auth;
use App\Facades\SiggySession;
use Siggy\DatePager;
use \ZebraPagination2;

class StatsController extends BaseController {

	public function leaderboard(Request $request)
	{
		$datep = $this->__setupDatePager($request);

		$dateRange = $datep->getTimestamps();

		//short names for vars for multipliers
		$wormhole = (double)SiggySession::getGroup()->stats_wh_map_points;
		$sig_add = (double)SiggySession::getGroup()->stats_sig_add_points;
		$sig_update = (double)SiggySession::getGroup()->stats_sig_update_points;
		$pos_add= (double)SiggySession::getGroup()->stats_pos_add_points;
		$pos_update = (double)SiggySession::getGroup()->stats_pos_update_points;

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
												'group' => SiggySession::getGroup()->id,
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
												'group' => SiggySession::getGroup()->id,
												'start' => $dateRange['start'],
												'end' => $dateRange['end']
											]);

		return view('stats.leaderboard', [
											'wormhole' => (double)SiggySession::getGroup()->stats_wh_map_points,
											'sig_add' => (double)SiggySession::getGroup()->stats_sig_add_points,
											'sig_update' => (double)SiggySession::getGroup()->stats_sig_update_points,
											'pos_add' => (double)SiggySession::getGroup()->stats_pos_add_points,
											'pos_update' => (double)SiggySession::getGroup()->stats_pos_update_points,
											'results' => $results,
											'rank_offset' => $offset,
											'pagination' => $paginationHTML,
											'title' => 'leaderboard',
											'selectedTab' => 'stats',
											'layoutMode' => 'blank',
											'previous_date' => $datep->getPreviousDate(),
											'current_date' => $datep->getCurrentDate(),
											'next_date' => $datep->getNextDate(),
											'stats_mode' => $datep->mode,
											'sub_page' => 'leaderboard',
											'settings' => $this->loadSettings(),
											'group' => SiggySession::getGroup(),
										]);
	}


	public function sigUpdates(Request $request)
	{
		return $this->handle_keyed_stats($request, 'sig_updates');
	}

	public function sigAdds(Request $request)
	{
		return $this->handle_keyed_stats($request, 'sig_adds');
	}

	public function wormholes(Request $request)
	{
		return $this->handle_keyed_stats($request, 'wormholes');
	}

	public function posAdds(Request $request)
	{
		return $this->handle_keyed_stats($request, 'pos_adds');
	}

	public function posUpdates(Request $request)
	{
		return $this->handle_keyed_stats($request, 'pos_updates');
	}

	private function handle_keyed_stats(Request $request, $key)
	{
		$keyLookup = array('sig_adds' => 'adds',
							'sig_updates' => 'updates',
							'pos_adds' => 'pos_adds',
							'wormholes' => 'wormholes',
							'pos_updates' => 'pos_updates'
							);
		$convertedKey = $keyLookup[$key];

		$datep = $this->__setupDatePager($request);

		$dateRange = $datep->getTimestamps();


		$resultCount = DB::selectOne("SELECT COUNT(*) as total
											FROM
											(
													SELECT charID, charName, sum(".$convertedKey.") as value FROM stats
													WHERE groupID=:group AND dayStamp >= :start AND dayStamp < :end AND ".$convertedKey." != 0
													GROUP BY charID
													ORDER BY value DESC
											) u",[
												'group' => SiggySession::getGroup()->id,
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
												'group' => SiggySession::getGroup()->id,
												'start' => $dateRange['start'],
												'end' => $dateRange['end']
											]);

		return view('stats.specific_stat', [
											'wormhole' => (double)SiggySession::getGroup()->stats_wh_map_points,
											'sig_add' => (double)SiggySession::getGroup()->stats_sig_add_points,
											'sig_update' => (double)SiggySession::getGroup()->stats_sig_update_points,
											'pos_add' => (double)SiggySession::getGroup()->stats_pos_add_points,
											'pos_update' => (double)SiggySession::getGroup()->stats_pos_update_points,
											'results' => $results,
											'rank_offset' => $offset,
											'pagination' => $paginationHTML,

											'title' => 'leaderboard',
												'selectedTab' => 'stats',
												'layoutMode' => 'blank',
												'previous_date' => $datep->getPreviousDate(),
												'current_date' => $datep->getCurrentDate(),
												'next_date' => $datep->getNextDate(),
												'stats_mode' => $datep->mode,
												'sub_page' => $key,
												'settings' => $this->loadSettings(),
												'group' => SiggySession::getGroup(),
										]);
	}

	private function __setupDatePager(Request $request)
	{
		$year = $request->input('year', null);
		$month = $request->input('month', null);
		$day = $request->input('day', null);
		$week = $request->input('week', null);

		$mode = DatePager::MODEWEEKLY;
		if( $month != null )
		{
			$mode = DatePager::MODEMONTHLY;
		}
		else if( $day != null )
		{
			$mode = DatePager::MODEWEEKLY;
		}
		else if( $year != null && $week == null )
		{
			$mode = DatePager::MODEYEARLY;
		}

		return new DatePager($mode, $day, $month, $year, $week);
	}

	public function overview(Request $request)
	{
		$datep = $this->__setupDatePager($request);
		$dateRange = $datep->getTimestamps();

		$top10Adds = $this->getTop10('adds',$dateRange['start'],$dateRange['end']);
		$top10Edits = $this->getTop10('updates',$dateRange['start'],$dateRange['end']);
		$top10WHs = $this->getTop10('wormholes',$dateRange['start'],$dateRange['end']);

		return view('stats.stats', [
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
												'group' => SiggySession::getGroup(),
											]);
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
												'group' => SiggySession::getGroup()->id,
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
