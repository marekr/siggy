<?php defined('SYSPATH') or die('No direct script access.');

require_once APPPATH.'classes/groupUtils.php';
require_once APPPATH.'classes/mapUtils.php';
require_once APPPATH.'classes/miscUtils.php';

class Controller_Special extends Controller {
	
	private $groupData = array();
	private $authPassword = '';
	private $trusted = false;
	
	function __construct(Kohana_Request $request, Kohana_Response $response)
	{
		
		parent::__construct($request, $response);
	}
	
	public function action_searchCorpName()
	{
		$results = $this->searchEVEEntityByName( 'Dreddit', 'corp' );
		print_r($results);
		
		
	}
	
	public function action_generateHubJumps()
	{
	
		if( !isset($_GET['key']) || $_GET['key'] != 'PIZZAMOFO' )
		{
			exit('GTFO');
		}
		
		
		$data = DB::select()->from('mapsolarsystemjumps')->order_by('fromSolarSystemID', 'ASC')->execute()->as_array();
		
		$jumps = array();
		foreach($data as $j)
		{
			$jumps[ $j['fromSolarSystemID'] ][] = $j['toSolarSystemID'];
		}
		
		
		$systems = DB::select('solarSystemID')->from('mapsolarsystems')->where('solarSystemID', '<', 31000000)->order_by('solarSystemID', 'ASC')->execute()->as_array();
		
		
		//targets
		$targets = array( '30000142',	//jita
						   '30002187',	//amarr
							'30002659',		//dodixie
							'30002510',		//rens
							'30002053'		//hek
						);
		
		
		foreach($systems as $origin)
		{
			foreach($targets as $target)
			{
				$jumpResult = $this->shortestRoute($origin['solarSystemID'], $target, $jumps);
				
				$insert = array( 'origin_system' => $jumpResult['origin'],
								 'destination_system' => $jumpResult['destination'],
								 'num_jumps' => $jumpResult['distance'],
								 'route' => $jumpResult['jumps'],
								 'type' => 'shortest'
								 );
				DB::insert('precomputedroutes', array_keys($insert) )->values(array_values($insert))->execute();
				print_r($jumpResult);
			}
		}
		
	}
	
	private function shortestRoute($origin, $target, $jumps)
	{
		$jumpResult = array(
			'origin' => $origin,
			'destination' => $target,
			'jumps' => 'N/A',
			'distance' => -1
		);
		
		if (isset($jumps[$origin]) && isset($jumps[$target])) {
		 
			// Target and origin the same, no distance
			if ($target == $origin) { 
				$jumpResult['jumps'] = $origin;
				$jumpResult['distance'] = 0;
			}
		 
			// Target is a neigbour system of origin
			elseif (in_array($target, $jumps[$origin])) {
				$jumpResult['jumps'] = $origin . ',' . $target;
				$jumpResult['distance'] = 1;
			}
		 
			// Lets start the fun
			else {
				// Will contain the system IDs
				$resultPath = array();
				// Already visited system
				$visitedSystems = array();
				// Limit the number of iterations
				$remainingJumps = 9000;
				// Systems we can reach from here
				$withinReach = array($origin);
		 
				while (count($withinReach) > 0 && $remainingJumps > 0 && count($resultPath) < 1) {
					$remainingJumps--;
		 
					// Jump to the first system within reach
					$currentSystem = array_shift($withinReach);
		 
					// Get the IDs of the systems, connected to the current
					$links = $jumps[$currentSystem];
					$linksCount = count($links);
		 
					// Test all connected systems
					for($i = 0; $i < $linksCount; $i++) {
						$neighborSystem = $links[$i];
		 
						// If neighbour system is the target,
						// Build an array of ordered system IDs we need to
						// visit to get from thhe origin system to the 
						// target system
						if ($neighborSystem == $target) {
							$resultPath[] = $neighborSystem;
							$resultPath[] = $currentSystem;
							while ($visitedSystems[$currentSystem] != $origin) {
								$currentSystem = $visitedSystems[$currentSystem];
								$resultPath[] = $currentSystem;
							}
							$resultPath[] = $origin;
							$resultPath = array_reverse($resultPath);
							break;
						}
		 
						// Otherwise, store the current - neighbour
						// Connection in the visited systems and add the
						// neighbour to the systems within reach
						else if (!isset($visitedSystems[$neighborSystem])) {
							$visitedSystems[$neighborSystem] = $currentSystem;
							array_push($withinReach, $neighborSystem);
						}
					}
				}
		 
				// If the result path is filled, we have a connection
				if (count($resultPath) > 1) {
					$jumpResult['distance'] = count($resultPath) - 1;
					$jumpResult['jumps'] = implode(',', $resultPath);
				}
			}
		}
		
		return $jumpResult;
	}
	
	private function searchEVEEntityByName( $names, $type = 'corp' )
	{
		if( $type == 'corp' )
		{
			$nameArray = explode(',', $names);
			$queryArray = array();
			foreach($nameArray as $name)
			{
				$name = trim($name);
				if(!empty($name))
				{
					$queryArray[] = "corporationName LIKE ".Database::instance()->escape($name);
				}
			}
			$querySQL = implode(" OR ", $queryArray);
			$results = DB::query(Database::SELECT, 'SELECT * FROM corporations WHERE '.$querySQL)->execute()->as_array();
			
			if( count( $results ) )
			{
				return $results;
			}
		}
	
		require_once( Kohana::find_file('vendor', 'pheal/Pheal') );
		spl_autoload_register( "Pheal::classload" );
		PhealConfig::getInstance()->cache = new PhealFileCache(APPPATH.'cache/api/');
		PhealConfig::getInstance()->http_ssl_verifypeer = false;
		$pheal = new Pheal(null,null,'eve');      
		
		$result = $pheal->CharacterID( array( 'names' => $names ) )->toArray();
		$potentialCorps = $result['result']['characters'];
		
		$pheal->scope = 'corp';
		
		$resultArray = array();
		foreach( $potentialCorps as $corp )
		{
			print "trying:<br />";
			print_r($corp) . "<br />";
			try
			{
				$result = $pheal->CorporationSheet( array( 'corporationID' => (int)$corp['characterID'] ) )->toArray();
				print 'found corp, storing locally!';
				$result = $result['result'];
				DB::query(Database::INSERT, 'INSERT INTO corporations (`corporationID`, `corporationName`, `memberCount`, `ticker`, `description`, `lastUpdate`) VALUES(:corporationID, :corporationName, :memberCount, :ticker, :description, :lastUpdate)'
										   .' ON DUPLICATE KEY UPDATE description = :description, memberCount = :memberCount, lastUpdate = :lastUpdate')
										->param(':memberCount', $result['memberCount'] )
										->param(':corporationID', $result['corporationID'] )
										->param(':corporationName', $result['corporationName'] )
										->param(':description', $result['description'] )
										->param(':ticker', $result['ticker'] )
										->param(':lastUpdate', time() )
										->execute();	
				$resultArray[] = $result;					
				
				
			}
			catch( PhealAPIException $e )
			{
				if( $e->code == 523 )
				{
					print 'not a corp!';
				}
			}
		}
		
		return $resultArray;
	}
	
	public function action_makePaymentCodes()
	{
		
		
		$groups = DB::select()->from('groups')->where('paymentCode','=','')->execute()->as_array();
		foreach($groups as $group)
		{
			$code = miscUtils::generateString();
			DB::update('groups')->set( array('paymentCode' => $code) )->where('groupID', '=',  $group['groupID'])->execute();
			
		
		}
	}
	
	public function action_rebuildShipsTable()
	{
		if( !isset($_GET['key']) || $_GET['key'] != 'PIZZAMOFO' )
		{
			exit('GTFO');
		}
		
		$ships = DB::query(Database::SELECT,'SELECT t.*, 
												   g.groupName,
												   r.raceName,
												   m.marketGroupName marketGroupName,
												   mp.marketGroupName marketParentGroupName
												FROM
												  chrRaces AS r 
												  INNER JOIN invTypes AS t ON r.raceID = t.raceID
												  INNER JOIN invGroups AS g ON t.groupID = g.groupID
												  LEFT OUTER JOIN invMarketGroups AS m ON t.marketGroupID = m.marketGroupID 
												  LEFT JOIN invMarketGroups AS mp ON m.parentGroupID = mp.marketGroupID 
												WHERE
												  (g.categoryID = 6 AND t.published = 1)
                                                  OR t.groupID=29
												ORDER BY
												  t.typeName ASC')
						->execute()
						->as_array();		
		foreach($ships as $ship)
		{
			if( $ship['typeID'] == 29988 ) //proteus
			{
				$ship['mass'] = 15000000;
			}
			else if( $ship['typeID'] == 29986 ) // legion
			{
				$ship['mass'] = 16000000;
			}
			else if( $ship['typeID'] == 29984 ) // tengu
			{
				$ship['mass'] = 14000000;
			}
			else if( $ship['typeID'] == 29990 ) // loki
			{
				$ship['mass'] = 15000000;
			}
		
			$insert = array( 'shipID' => $ship['typeID'],
							 'shipName' => $ship['typeName'],
							 'mass' => (double)$ship['mass'],
							// 'graphicID' => (int)$ship['graphicID'],
							// 'iconID' => (int)$ship['iconID'],
							 'shipClass' => $ship['groupName']
							 );
			DB::insert('ships', array_keys($insert) )->values(array_values($insert))->execute();
		}
	}
	
	//protect agaisnt stupid later
	public function action_preProcess()
	{
		$start = $this->request->param('start',0);
	
		if( !isset($_GET['key']) || $_GET['key'] != 'PIZZAMOFO' )
		{
			exit('GTFO');
		}
		
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		$increment = 200;
			
		print $start."<br />";
		
		$classMap = DB::query(Database::SELECT,'SELECT locationID, wormholeClassID AS class FROM maplocationwormholeclasses')->execute()->as_array('locationID');

		
		$total = DB::query(Database::SELECT, 'SELECT count(*) as total FROM mapsolarsystems')->execute()->get('total');

		$systems = DB::select()->from('mapsolarsystems')->order_by('solarSystemID', 'ASC')->limit($increment)->offset($start)->execute()->as_array();
		
		foreach($systems as $system)
		{
			$insert['id'] = $system['solarSystemID'];
			$insert['name'] = $system['solarSystemName'];
			$insert['region'] = $system['regionID'];
			
			$insert['truesec'] = $system['security'];
			$insert['sec'] = round($system['security'],1);
			
			if( isset( $classMap[ $system['regionID'] ] ) )
			{
					$insert['sysClass'] = $classMap[ $system['regionID'] ]['class'];
			}
			
			//system class maps override region
			if( isset( $classMap[ $system['solarSystemID'] ] ) )
			{
					$insert['sysClass'] = $classMap[ $system['solarSystemID'] ]['class'];
			}

			$insert['constellation'] = $system['constellationID'];
			$insert['radius'] = (($system['radius']/1000)/149598000);
			

			
			$insert['planets'] = DB::query(Database::SELECT, 'SELECT COUNT(*) as total FROM mapDenormalize WHERE solarSystemID = :system AND groupID = 7')
													->param(':system', $insert['id'])->execute()->get('total');
			$insert['moons'] = DB::query(Database::SELECT, 'SELECT COUNT(*) as total FROM mapDenormalize WHERE solarSystemID = :system AND groupID = 8')
													->param(':system', $insert['id'])->execute()->get('total');
			$insert['belts'] = DB::query(Database::SELECT, 'SELECT COUNT(*) as total FROM mapDenormalize WHERE solarSystemID = :system AND groupID = 9')
													->param(':system', $insert['id'])->execute()->get('total');
			
			$insert['effect'] = DB::query(Database::SELECT, 'SELECT typeID FROM mapDenormalize WHERE solarSystemID = :system AND groupID = 995')
													->param(':system', $insert['id'])->execute()->get('typeID', 0);
			
			DB::insert('solarsystems', array_keys($insert) )->values(array_values($insert))->execute();
		}
		$start += $increment;
		print "Finished ".$start." of ".$total." systems";
		if( $start >= $total )
		{
			print "Done!";	

		}
		else
		{
			print '<meta http-equiv="refresh" content="2; url=http://localhost/evetel/preProcess/'.$start.'?key=PIZZAMOFO" />';
		}
		die();
	}	 
	

	public function action_processStatics($region)
	{
		if( !isset($_GET['key']) || $_GET['key'] != 'PIZZAMOFO' )
		{
			exit('GTFO');
		}
		
		$staticIDmap = DB::query(Database::SELECT,'SELECT * FROM statics')->execute()->as_array('staticName');
	
		$insert = array();
		$region = intval($region);
		$row = 1;
		if (($handle = fopen("./staticdata/r".$region.".csv", "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
						$num = count($data);
						$row++;
						echo 'System: '.$data[0] ." ";
						
						$statics = explode(' ', $data[1]);
						
						$systemID = DB::query(Database::SELECT, 'SELECT id,name FROM solarsystems WHERE name = :system')
													->param(':system', trim($data[0]) )->execute()->get('id');
						
						$inserted= 0;
						$insert['systemID'] = $systemID;
						if( is_array($statics) && count ($statics) > 0 )
						{
							foreach($statics as $k => $static)
							{
								if( !empty($static) )
								{
									$static = preg_replace("/[^a-zA-Z0-9]/", "", $static);
									
									//insert routine
									if( isset( $staticIDmap[ $static ] ) )
									{
										$insert['staticID'] = $staticIDmap[ $static ]['staticID'];
									}
									try
									{
										DB::insert('staticmap', array_keys($insert) )->values(array_values($insert))->execute();
										$inserted++;
									}
									catch(Database_Exception $e)
									{
									}
								}
							}
						}
						echo 'Statics Inserted: '.$inserted.'<br />';
				}
				fclose($handle);
		}	 
	}
	
	public function action_buildStatics()
	{
		if( !isset($_GET['key']) || $_GET['key'] != 'PIZZAMOFO' )
		{
			exit('GTFO');
		}
		
		$view = View::factory('special/buildStatics');
		
		$staticData = DB::select()->from('statics')->order_by('staticName', 'ASC')->execute()->as_array();
		
		$statics = '';
		foreach($staticData as $s )
		{
			if( isset($_POST['static'] ) && $s['staticID'] == $_POST['static'] )
			{
				$statics .= '<option value="'.$s['staticID'].'" selected="selected">'.$s['staticName'].'</option>';
			}
			else
			{
				$statics .= '<option value="'.$s['staticID'].'">'.$s['staticName'].'</option>';
			}
		}
		$view->statics = $statics;
		
		$regionData = DB::select()->from('regions')->where('regionID','>=',11000001)->order_by('regionID', 'ASC')->execute()->as_array();
		
		$regions = '';
		foreach($regionData as $r )
		{
			if( isset($_POST['region'] ) && $r['regionID'] == $_POST['region'] )
			{
				$regions .= '<option value="'.$r['regionID'].'" selected="selected">'.$r['regionName'].'</option>';
			}
			else
			{
				$regions .= '<option value="'.$r['regionID'].'">'.$r['regionName'].'</option>';
			}
		}
		$view->regions = $regions;
		
		$constellationData = DB::select()->from('constellations')->where('constellationID','>=',21000001)->order_by('constellationID', 'ASC')->execute()->as_array();
		
		$constellations = '';
		foreach($constellationData as $c )
		{
			$constellations .= '<option value="'.$c['constellationID'].'">'.($c['constellationID']-21000000).'</option>';
		}
		$view->constellations = $constellations;
		
		if( isset($_POST['static'] ) )
		{
			$const = intval($_POST['constellation']);
			$reg = intval($_POST['region']);
			
			if( $const != 0 )
			{
				$systems = DB::select('id')->from('solarsystems')->where('region','=', $reg)->where('constellation','=', $const)->execute()->as_array();
			}
			else
			{
				$systems = DB::select('id')->from('solarsystems')->where('region','=', $reg)->execute()->as_array();
			}
			
			$count = count($systems);
			
			if( $count > 0 )
			{
				foreach($systems as $s)
				{
					$insert['systemID'] = $s['id'];
					$insert['staticID'] = intval($_POST['static']);
					
					
					DB::insert('staticmap', array_keys($insert) )->values(array_values($insert))->execute();
				}
			}
			
			print $count.' systems had the static added';
			
		}
		
		
		$this->response->body($view);
	}
	
	public function action_fixGroup()
	{
		if( !isset($_GET['key']) || $_GET['key'] != 'PIZZAMOFO' )
		{
			exit('GTFO');
		}
		
		$subgroup = 0;
		$group = intval($_GET['group']);
		if( $group <= 0 )
		{
			return;
		}
		if( isset($_GET['subgroup']) )
		{
			$subgroup = intval($_GET['subgroup']);
		}
		
		//$subgroups = array( 0 );
		//$subgroupsd = DB::select()->from('subgroups')->where('groupID','=',$group)->execute()->as_array();
	 // foreach($subgroupsd as $subgroup)
	 // {
		//	$subgroups[] = $subgroup['subGroupID'];
	 // }
		
		$systems = DB::select('id')->from('solarsystems')->order_by('id', 'ASC')->execute()->as_array();
		
	//	foreach( $subgroups as $sg )
	 // {
			$i = 0;
			foreach($systems as $system)
			{		
				DB::query(Database::INSERT, 'INSERT INTO activesystems (`systemID`,`groupID`,`subGroupID`) VALUES(:systemID, :groupID, :subGroupID) ON DUPLICATE KEY UPDATE systemID=systemID')
														->param(':systemID', $system['id'] )->param(':groupID', $group )->param(':subGroupID', $subgroup )->execute();
		 
				$i++;
			}
			print 'done for '.$i.'systems for subgroup '.$subgroup.'<br />';
		//}
	 
	}	
	

	
	public function action_whHashFix()
	{
		if( !isset($_GET['key']) || $_GET['key'] != 'PIZZAMOFO' )
		{
			exit('GTFO');
		}
		
		$subgroups = array( 0 );
		$wormholes = DB::select()->from('wormholes')->execute()->as_array();
		
		foreach( $wormholes as $wh )
		{
			$oldhash = $wh['hash'];
			$newhash = $this->whHashByID($wh['to'], $wh['from']);
			
			DB::update('wormholes')->set( array('hash' => $newhash) )->where('hash', '=',  $oldhash)->execute();
		}
		
		$subgroupsd = DB::select()->from('subgroups')->execute()->as_array();
		foreach($subgroupsd as $subgroup)
		{
			$cache = Cache::instance();
			$cache->delete('mapCache-'.$subgroup['groupID'].'-'.$subgroup['subGroupID']);
		}		 
			
		print "done!";
	}
	
	private function rebuildMapData($groupID, $subGroupID=0, $additionalSystems = null)
	{
			$data = array();
			
			$wormholes = DB::query(Database::SELECT, "SELECT `hash`, `to`, `from`, eol, mass, eolToggled FROM wormholes WHERE groupID=:group AND subGroupID=:subGroupID")
							 ->param(':group', $groupID)->param(':subGroupID', $subGroupID)->execute()->as_array('hash');	 
			
			$systemsToPoll = array();
			$wormholeHashes = array();
			foreach( $wormholes as $wormhole )
			{
				$systemsToPoll[] = $wormhole['to'];
				$systemsToPoll[] = $wormhole['from'];
				$wormholeHashes[] = $wormhole['hash'];
			}
			
			
			$data['systems'] = array();
			$data['systemIDs'] = array();
			
			$systemsToPoll = array_unique($systemsToPoll);
			

			if( $additionalSystems != null && is_array($additionalSystems) && count($additionalSystems) > 0 )
			{
				$systemsToPoll = array_merge($systemsToPoll, $additionalSystems);
			}

			if( count($systemsToPoll) > 0 )
			{
					$systemsToPoll = implode(',', $systemsToPoll);
					
					$chainMapSystems = DB::query(Database::SELECT, "SELECT sa.systemID,ss.name,sa.displayName,sa.inUse,sa.x,sa.y,sa.activity,ss.sysClass,ss.effect FROM activesystems sa 
					 INNER JOIN solarsystems ss ON ss.id = sa.systemID
					WHERE sa.systemID IN(".$systemsToPoll.") AND sa.groupID=:group AND sa.subGroupID=:subgroup ORDER BY sa.systemID ASC")
												->param(':group', $groupID)->param(':subgroup', $subGroupID)->execute()->as_array('systemID');	
					
					foreach( $chainMapSystems as &$sys ) 
					{
							if( in_array( $sys['systemID'], $additionalSystems ) )
							{
									$sys['special'] = 1;
							}
							else
							{
									$sys['special'] = 0;
							}
					}
					$data['systems'] = $chainMapSystems;
					$data['systemIDs'] = explode(',', $systemsToPoll);
			}
			
			$data['wormholeHashes'] = $wormholeHashes;
			$data['wormholes'] = $wormholes;
			$data['updateTime'] = time();
			
			
			return $data;
	}	
	
	
	private function whHashByID($to, $from)
	{
		if( $to < $from )
		{
			return md5( intval($to) . intval($from) );
		}
		else
		{
			return md5( intval($from) . intval($to) );
		}
	}		
}