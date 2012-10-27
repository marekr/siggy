<?php defined('SYSPATH') or die('No direct script access.');

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
	
class Controller_Cron extends Controller 
{

	public function action_billingTransactions()
	{
		
		require_once( Kohana::find_file('vendor', 'pheal/Pheal') );
		spl_autoload_register( "Pheal::classload" );
		PhealConfig::getInstance()->cache = new PhealFileCache(APPPATH.'cache/api/');
		PhealConfig::getInstance()->http_ssl_verifypeer = false;
		$pheal = new Pheal( "1368854", "EzmkgBrhVjksl2KbzDY8IIa0thRRoPVE26HD7r0cqRcJLQqSYN2wtqYkwIpO28fT", "corp" );
		
		$fromID = (float)0;
		$transactions = $pheal->WalletJournal( array('fromID' => $fromID, 'rowCount' => 100) )->entries;
		
		
		$lastID = (float)0;	//store the 64 bit integer as a string lololol
		foreach($transactions as $trans )
		{
			print "Processing: ";
			print_r($trans);
			print "<br />";
			if( $lastID < (float)$trans->refID )	//first transaction should be the latest
			{
				$lastID = (float)$trans->refID;
			}
		
			if( $trans->refTypeID == eveAPIWalletJournalTypes::playerDonation )
			{
				$entryCode = trim(str_replace('DESC:','',$trans->reason));
				if( !empty($entryCode) )
				{
					preg_match('/^siggy-([a-zA-Z0-9]{14,})/', $entryCode, $matches);
					if( count($matches) > 0 && isset($matches[1]) )
					{
						$res = DB::query(Database::SELECT, 'SELECT eveRefID FROM billing_payments WHERE eveRefID=:refID')->param(':refID', $trans->refID)->execute()->current();
						
						//if( !isset($res['eveRefID']) )
						if( true )
						{
							$paymentCode = strtolower($matches[1]);	//get 14 char "account code"
							$group = DB::query(Database::SELECT, 'SELECT * FROM	groups WHERE paymentCode=:paymentCode')->param(':paymentCode', $paymentCode)->execute()->current();
							
							if( isset( $group['groupID'] ) )
							{
								$insert = array( 'groupID' => $group['groupID'],
												 'eveRefID' => $trans->refID,
												 'paymentTime' => 0,
												 'paymentProcessedTime' => time(),
												 'paymentAmount' => (float)$trans->amount,
												 'payeeID' => $trans->ownerID1,
												 'payeeName' => $trans->ownerName1
												);
								//DB::insert( 'billing_payments', array_keys($insert) )->values( array_values($insert) )->execute();
					
								groupUtils::applyISKPayment($group['groupID'], (float)$trans->amount);
								
								//
								//(float)$trans->amount
							}
							else
							{
								//free money!
							}
						}
						else
						{
							//processed already!
							//do nothing
							print "Payment already processed";
						}
						
					}
				}
				else
				{
					//noope
				}
			}
		}
	}

	public function action_clearOldSigs()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		//two days?
		$cutoff = time()-60*60*24*2;
  //DB::delete('systemsigs')->where('created', '<=', $cutoff)->where('sig', '!=', 'POS')->execute();
  
		$groups = DB::query(Database::SELECT, "SELECT groupID,skipPurgeHomeSigs,homeSystemIDs FROM groups")->execute()->as_array();	 
		foreach( $groups as $group )
		{
			$ignoreSys = '';
			
			if( $group['skipPurgeHomeSigs'] && !empty($group['homeSystemIDs']) )
			{
				$ignoreSys = $group['homeSystemIDs'];
			}
		
			$subGroupsQuery = DB::query(Database::SELECT, "SELECT subGroupID, sgSkipPurgeHomeSigs,sgHomeSystemIDs FROM subgroups WHERE groupID = :groupID")->param(':groupID', $group['groupID'])->execute();	 
			$subGroups = $subGroupsQuery->as_array();
			if(	$subGroupsQuery->count() > 0 )
			{
				foreach( $subGroups as $subGroup )
				{
					if( $subGroup['sgSkipPurgeHomeSigs'] && !empty($subGroup['sgHomeSystemIDs']) )
					{
						$ignoreSys .= ( empty($ignoreSys) ? $subGroup['sgHomeSystemIDs'] : ','.$subGroup['sgHomeSystemIDs'] );
					}
				}
			}
			
			if( !empty($ignoreSys) )
			{
				DB::query(Database::DELETE, "DELETE FROM systemsigs WHERE sig != 'POS' AND groupID=:groupID AND systemID NOT IN(".$ignoreSys.") AND created <= :cutoff")->param(':cutoff',$cutoff)->param(':groupID', $group['groupID'])->execute();
			}
			else
			{
				DB::query(Database::DELETE, "DELETE FROM systemsigs WHERE sig != 'POS' AND groupID=:groupID AND created <= :cutoff")->param(':cutoff',$cutoff)->param(':groupID', $group['groupID'])->execute();
			}
		}
	
	}

	public function action_resetStuff()
	{
			$this->profiler = NULL;
			$this->auto_render = FALSE;
			//two days?
			$cutoff = time()-60*60*24;
      
      DB::update('activesystems')->set(array('displayName' => '', 'activity' => 0, 'lastActive' => 0, 'inUse' => 0))->where('lastActive', '<=', $cutoff)->where('lastActive', '!=', 0)->execute();
      DB::delete('wormholes')->where('lastJump', '<=', $cutoff)->execute();
      
      print 'done!';
	}
	
	public function action_hourlyAPIStats()
	{
		$cutoff = time()-(3600*24*2);
		
		DB::delete('apiHourlyMapData')->where('hourStamp', '<=', $cutoff)->execute();
		DB::delete('jumpsTracker')->where('hourStamp', '<=', $cutoff)->execute();
      
		$systems = DB::select('id')->from('solarsystems')->order_by('id', 'ASC')->execute()->as_array('id');
		foreach($systems as &$system)
		{		
			$system['jumps'] = 0;
			$system['kills'] = 0;
			$system['npcKills'] = 0;
			$system['podKills'] = 0;
		}
	
	
		require_once(Kohana::find_file('vendor', 'pheal/Pheal'));
		spl_autoload_register("Pheal::classload");
		PhealConfig::getInstance()->http_ssl_verifypeer = false;
		$pheal = new Pheal('','');
		$pheal->scope = 'map';
		
		$jumpsData = $pheal->Jumps();
		foreach($jumpsData->solarSystems as $ss )
		{
			if( isset( $systems[ $ss->solarSystemID ] ) )
			{
				$systems[ $ss->solarSystemID ]['jumps'] = $ss->shipJumps;
			}
		}
		
		$killsData = $pheal->Kills();
		foreach($killsData->solarSystems as $ss )
		{
			if( isset( $systems[ $ss->solarSystemID ] ) )
			{
				$systems[ $ss->solarSystemID ]['kills'] = $ss->shipKills;
				$systems[ $ss->solarSystemID ]['npcKills'] = $ss->factionKills;
				$systems[ $ss->solarSystemID ]['podKills'] = $ss->podKills;
			}
		}
		

		date_default_timezone_set('UTC');
		$requestDateInfo = getdate( time() - 3600 );
		$time = gmmktime($requestDateInfo['hours'],0,0,$requestDateInfo['mon'],$requestDateInfo['mday'],$requestDateInfo['year']);		
	
		foreach($systems as $system)
		{
			DB::query(Database::INSERT, 'INSERT INTO apiHourlyMapData (`systemID`,`hourStamp`, `jumps`, `kills`, `npcKills`, `podKills`) VALUES(:systemID, :hourStamp, :jumps, :kills, :npcKills, :podKills) ON DUPLICATE KEY UPDATE systemID=systemID')
														->param(':systemID', $system['id'] )->param(':hourStamp', $time )->param(':jumps', $system['jumps'] )->param(':kills', $system['kills'] )->param(':npcKills', $system['npcKills'] )->param(':podKills', $system['podKills'] )->execute();
	
		}
		
		print "done!";
	}
}