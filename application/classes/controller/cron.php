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
					$entryCode = $this->superclean($entryCode);
					if( !empty($entryCode) )
					{
						preg_match('/^siggy-([a-zA-Z0-9]{14,})/', $entryCode, $matches);
						if( count($matches) > 0 && isset($matches[1]) )
						{
							$res = DB::query(Database::SELECT, 'SELECT eveRefID FROM billing_payments WHERE eveRefID=:refID')->param(':refID', $trans['refID'])->execute()->current();
							
							if( !isset($res['eveRefID']) )
							{
								$paymentCode = strtolower($matches[1]);	//get 14 char "account code"
								$group = DB::query(Database::SELECT, 'SELECT * FROM	groups WHERE paymentCode=:paymentCode')->param(':paymentCode', $paymentCode)->execute()->current();
								
								if( isset( $group['groupID'] ) )
								{
								
									$insert = array( 'groupID' => $group['groupID'],
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
									DB::insert( 'billing_payments', array_keys($insert) )->values( array_values($insert) )->execute();
						
									groupUtils::applyISKPayment($group['groupID'], (float)$trans['amount']);
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
									print_r($entryCode);
									print_r($res);
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
		$cutoff = time()-60*30; 
      
		DB::delete('siggysessions')->where('lastBeep', '<=', $cutoff)->execute();
	}

	public function action_billingTransactions()
	{
		ini_set('memory_limit', '128M');
		ini_set('max_execution_time', 0);
		set_time_limit(600);
		
		require_once( Kohana::find_file('vendor', 'pheal/Pheal') );
		spl_autoload_register( "Pheal::classload" );
		PhealConfig::getInstance()->cache = new PhealFileCache(APPPATH.'cache/api/');
		PhealConfig::getInstance()->http_ssl_verifypeer = false;
		PhealConfig::getInstance()->http_user_agent = 'siggy '.SIGGY_VERSION.' borkedlabs@gmail.com';
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
		$groups = DB::select()->from('groups')->where('billable','=',1)->execute()->as_array();
		foreach($groups as $group)
		{
			$numUsers = groupUtils::getCharacterUsageCount($group['groupID']);
			if( $numUsers == 0 )
			{
				continue;
			}
			
			$cost = miscUtils::computeCostPerDays($numUsers, 1);
			
			$message = 'Daily usage cost - ' . $numUsers . ' characters';
			
			$insert = array(
								'amount' => $cost,
								'date' => time(),
								'groupID' => $group['groupID'],
								'memberCount' => $numUsers,
								'message' => $message
							);
			$result = DB::insert('billing_charges', array_keys($insert) )->values( array_values($insert) )->execute();
			
			groupUtils::applyISKCharge( $group['groupID'], $cost );
		
		}	
	}
	
	public function action_apiUpdateCorpData()
	{
		set_time_limit(600);
		
		require_once( Kohana::find_file('vendor', 'pheal/Pheal') );
		spl_autoload_register( "Pheal::classload" );
		PhealConfig::getInstance()->cache = new PhealFileCache(APPPATH.'cache/api/');
		PhealConfig::getInstance()->http_ssl_verifypeer = false;
		PhealConfig::getInstance()->http_user_agent = 'siggy '.SIGGY_VERSION.' borkedlabs@gmail.com';
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
		$corpsToUpdate = DB::query(Database::SELECT, "SELECT * FROM groupmembers WHERE memberType='corp' AND SUBSTR(id,LENGTH(id),1) = :select")
							->param(':select', $select)
							->execute()->as_array();	 
		
		foreach($corpsToUpdate as $gm)
		{
			try
			{
					$result = $pheal->CorporationSheet( array( 'corporationID' => (int)$gm['eveID'] ) );
									
					DB::query(Database::INSERT, 'INSERT INTO corporations (`corporationID`, `corporationName`, `memberCount`, `ticker`, `description`, `lastUpdate`) VALUES(:corporationID, :corporationName, :memberCount, :ticker, :description, :lastUpdate)'
											   .' ON DUPLICATE KEY UPDATE description = :description, memberCount = :memberCount, lastUpdate = :lastUpdate')
											->param(':memberCount', $result->memberCount )
											->param(':corporationID', $gm['eveID'] )
											->param(':corporationName', $result->corporationName )
											->param(':description', $result->description )
											->param(':ticker', $result->ticker )
											->param(':lastUpdate', time() )
											->execute();	
			}
			catch( Exception $e )
			{
				echo $e->getMessage();
			}
		}
	}

	public function action_clearOldSigs()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		//two days?
		$cutoff = time()-60*60*24*7;
  
		$groups = DB::query(Database::SELECT, "SELECT groupID,skipPurgeHomeSigs,homeSystemIDs FROM groups")->execute()->as_array();	 
		foreach( $groups as $group )
		{
			$ignoreSys = '';
			
			if( $group['skipPurgeHomeSigs'] && !empty($group['homeSystemIDs']) )
			{
				$ignoreSys = $group['homeSystemIDs'];
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
      
		//DB::update('activesystems')->set(array('displayName' => '', 'activity' => 0, 'lastActive' => 0, 'inUse' => 0))->where('lastActive', '<=', $cutoff)->where('lastActive', '!=', 0)->execute();
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
		PhealConfig::getInstance()->http_user_agent = 'siggy '.SIGGY_VERSION.' borkedlabs@gmail.com';
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