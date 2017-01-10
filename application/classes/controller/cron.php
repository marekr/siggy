<?php defined('SYSPATH') or die('No direct script access.');
//error_reporting(E_ALL);
//error_reporting(E_ALL);
ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');
class eveAPIWalletJournalTypes
{
	const playerTrading = 1;
	const marketTransaction = 2;
	const playerDonation = 10;
	const bountyPrizes = 17;
	const insurance = 19;
	const CSPA = 35;
	const corpAccountWithdrawal = 37;
	const brokerFee = 46;
	const manufacturing = 56;
	const bountyPrize = 85;

	// etc.
}

use Pheal\Pheal;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;

class Controller_Cron extends Controller
{
	private function processBillingTransactionsResult( $transactions, $previousID, &$maxID, &$fromID )
	{
		$stop = FALSE;
		$transactions = $transactions->toArray();
		if( count( $transactions ) > 0 )
		{
			foreach( $transactions as $trans )
			{
				$maxID = max($maxID, $trans['refID']);
				$fromID = min($fromID, $trans['refID']);

				if( $previousID == $trans['refID'] )	//we hit the last processed entry
				{
					$stop = TRUE;
				}

				if( $trans['refTypeID'] == eveAPIWalletJournalTypes::playerDonation || $trans['refTypeID'] == eveAPIWalletJournalTypes::corpAccountWithdrawal)
				{
					$entryCode = trim(str_replace('DESC:','',$trans['reason']));
					$entryCode = strtolower($this->superclean($entryCode));
					if( !empty($entryCode) )
					{
						preg_match('/^siggy-([a-zA-Z0-9]{14,})/', $entryCode, $matches);
						if( count($matches) > 0 && isset($matches[1]) )
						{
							$res = DB::selectOne(Database::SELECT, 'SELECT eveRefID FROM billing_payments WHERE eveRefID=?',[$trans['refID']]);

							if( $res == null )
							{
								$paymentCode = strtolower($matches[1]);	//get 14 char "account code"
								$group = Group::findByPaymentCode($paymentCode);

								if( $group != null )
								{
									$insert = array( 'groupID' => $group->id,
													 'eveRefID' => $trans['refID'],
													 'paymentTime' => strtotime($trans['date']),
													 'paymentProcessedTime' => time(),
													 'paymentAmount' => (float)$trans['amount'],
													 'payeeID' => $trans['ownerID1'],
													 'payeeName' => $trans['ownerName1'],
													 'payeeType' => ($trans['refTypeID'] == eveAPIWalletJournalTypes::corpAccountWithdrawal ? 'corp' : 'char'),
													 'argID' => $trans['argID1'],
													 'argName' => $trans['argName1']
													);
									DB::table( 'billing_payments')->insert($insert);

									$group->applyISKPayment((float)$trans['amount']);
								}
								else
								{
									continue;
									//free money!
								}
							}
							else
							{
								//processed already!
								//do nothing
								print_r($entryCode);
								print_r($res->eveRefID);
								print "Payment already processed";
								print "<br />";
								print "<br />";
							}

						}
					}
					else
					{
						//noope
						//free money!
					}
				}
			}
		}
		else
		{
			$stop = TRUE;
		}
		return $stop;
	}

	public function superclean($text)
	{
		// Strip HTML Tags
		$clear = strip_tags($text);
		// Clean up things like &amp;
		$clear = html_entity_decode($clear);
		// Strip out any url-encoded stuff
		$clear = urldecode($clear);
		// Replace non-AlNum characters with space
		$clear = preg_replace('/[^-a-z0-9]+/i', ' ', $clear);
		// Replace Multiple spaces with single space
		$clear = preg_replace('/ +/', ' ', $clear);
		// Trim the string of leading/trailing space
		$clear = trim($clear);

		return $clear;
	}

	public function action_pruneSiggySessions()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;

		//30 minute cutoff
		$cutoff = Carbon::now()->subMinutes(30)->toDateTimeString();

		DB::delete('sessions')->where('updated_at', '<=', $cutoff)->execute();
	}

	public function action_purgeNotifications()
	{
		//15 day cutoff
		$cutoff = time()-60*60*24*15;

		$query = DB::delete(Database::DELETE, "DELETE FROM notifications WHERE created_at <= ?",[$cutoff]);

		print "done";
	}

	public function action_billingTransactions()
	{
		ini_set('memory_limit', '256M');
		ini_set('max_execution_time', 0);
		set_time_limit(0);

		PhealHelper::configure();
		$pheal = new Pheal( "3523432", "iBfWRWpwZ9I5l7Ynt2Y7ZlxiesY6b7vVhmpHlhzLDMdCFQnaTus4DBgOGxIfwu4M", "corp" );

		$previousID = (float)miscUtils::getDBCacheItem( 'lastProcessedJournalRefID' );
		$transactions = $pheal->WalletJournal( array( 'rowCount' => 100) )->entries;

		$maxID = 0;
		$fromID = 0;
		$stop = $this->processBillingTransactionsResult($transactions, $previousID, $maxID, $fromID );

		if( !$stop )
		{
			while( !$stop )
			{
				print $fromID;
				$transactions = $pheal->WalletJournal( array('fromID' => $fromID, 'rowCount' => 100) )->entries;
				$stop = $this->processBillingTransactionsResult($transactions, $previousID, $maxID, $fromID );
				unset($transactions);
			}
		}

		$maxID = (string)$maxID;
		miscUtils::storeDBCacheItem( 'lastProcessedJournalRefID', $maxID );
	}

	public function action_billingCharges()
	{
		$groups = Group::where('billable',1)->get();
		foreach($groups as $group)
		{
			$numUsers = $group->getCharacterUsageCount();
			if( $numUsers == 0 )
			{
				continue;
			}

			$cost = miscUtils::computeCostPerDays($numUsers, 1);

			$message = 'Daily usage cost - ' . $numUsers . ' characters';

			$insert = array(
								'amount' => $cost,
								'date' => time(),
								'groupID' => $group->id,
								'memberCount' => $numUsers,
								'message' => $message
							);
			$result = DB::table('billing_charges')->insert($insert);

			$group->applyISKCharge( $cost );
		}
	}

	public function action_apiUpdateCorpData()
	{
		set_time_limit(0);

		PhealHelper::configure();
		$pheal = new Pheal(null,null,'corp');

		$select = date('G');
		if( $select > 9 )
		{
			$select -= 9;
			if( $select > 9 )
			{
				$select -= 9;
			}
		}

		$corpsToUpdate = array();
		$corpsToUpdate = DB::select(Database::SELECT, "SELECT * FROM groupmembers WHERE memberType='corp' AND SUBSTR(id,LENGTH(id),1) = ?",[$select]);

		foreach($corpsToUpdate as $gm)
		{
			$corp = Corporation::find((int)$gm->eveID);

			//incase this fails...
			if($corp != null)
			{
				$corp->syncWithApi();
			}
		}
	}

	public function action_clearOldSigs()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		//two days?
		$cutoff = Carbon::now()->subDays(26)->toDateTimeString();
		$whCutoff = Carbon::now()->subDays(2)->toDateTimeString();

		$groups = DB::select("SELECT id,skip_purge_home_sigs FROM groups");
		foreach( $groups as $group )
		{
			$ignoreSys = '';
			$chains = DB::select("SELECT chainmap_homesystems_ids FROM chainmaps
													WHERE group_id = ? AND
													chainmap_skip_purge_home_sigs=1",[$group->id]);

			if( $chains != null )
			{
				$ignoreSys = array();
				foreach( $chains as $c )
				{
					if( !empty($c->chainmap_homesystems_ids) )
					{
						$ignoreSys[] = $c->chainmap_homesystems_ids;
					}
				}

				$ignoreSys = implode(',', $ignoreSys);
			}

			$ignoreSysExtra = '';
			if( !empty($ignoreSys) )
			{
				$ignoreSysExtra = "systemID NOT IN(".$ignoreSys.") AND ";
			}

			
			$query = DB::delete("DELETE FROM systemsigs 
								WHERE sig != 'POS' AND
									groupID=:groupID AND
									{$ignoreSysExtra}
									( created_at <= :cutoff OR (type = 'wh' AND created_at <= :whcutoff))",[
										'cutoff' => $cutoff,
										'groupID' => $group->id,
										'whcutoff' => $whCutoff
									]);
		}
	}

	public function action_resetStuff()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		//two days?
		$cutoff = time()-60*60*24;

		//DB::update('activesystems')->set(array('displayName' => '', 'activity' => 0, 'lastActive' => 0, 'inUse' => 0))->where('lastActive', '<=', $cutoff)->where('lastActive', '!=', 0)->execute();
		DB::table('wormholes')->where('last_jump', '<=', $cutoff)->delete();

		print 'done!';
	}

	public function action_hourlyAPIStats()
	{
		$cutoff = time()-(3600*24*2);

		DB::table('apihourlymapdata')->where('hourStamp', '<=', $cutoff)->delete();
		DB::table('jumpstracker')->where('hourStamp', '<=', $cutoff)->delete();

		$systems = DB::table('solarsystems')->orderBy('id');
		foreach($systems as &$system)
		{
			$system->jumps = 0;
			$system->kills = 0;
			$system->npcKills = 0;
			$system->podKills = 0;
		}

		PhealHelper::configure();
		$pheal = new Pheal('','');
		$pheal->scope = 'map';

		$jumpsData = $pheal->Jumps();
		foreach($jumpsData->solarSystems as $ss )
		{
			if( isset( $systems[ $ss->solarSystemID ] ) )
			{
				$systems[ $ss->solarSystemID ]->jumps = $ss->shipJumps;
			}
		}

		$killsData = $pheal->Kills();
		foreach($killsData->solarSystems as $ss )
		{
			if( isset( $systems[ $ss->solarSystemID ] ) )
			{
				$systems[ $ss->solarSystemID ]->kills = $ss->shipKills;
				$systems[ $ss->solarSystemID ]->npcKills = $ss->factionKills;
				$systems[ $ss->solarSystemID ]->podKills = $ss->podKills;
			}
		}


		date_default_timezone_set('UTC');
		$requestDateInfo = getdate( time() - 3600 );
		$time = gmmktime($requestDateInfo['hours'],0,0,$requestDateInfo['mon'],$requestDateInfo['mday'],$requestDateInfo['year']);

		foreach($systems as $system)
		{
			DB::insert('INSERT INTO apihourlymapdata (`systemID`,`hourStamp`, `jumps`, `kills`, `npcKills`, `podKills`) 
				VALUES(:systemID, :hourStamp, :jumps, :kills, :npcKills, :podKills) ON DUPLICATE KEY UPDATE systemID=systemID',[
					'systemID' => $system->id,
					'hourStamp' => $time,
					'jumps' => $system->jumps,
					'kills' => $system->kills,
					'npcKills' => $system->npcKills,
					'podKills' => $system->podKills
				]);

		}

		print "done!";
	}
}
