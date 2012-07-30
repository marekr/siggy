<?php defined('SYSPATH') or die('No direct script access.');

require_once APPPATH.'classes/access.php';
require_once APPPATH.'classes/groupUtils.php';
require_once APPPATH.'classes/mapUtils.php';
require_once APPPATH.'classes/miscUtils.php';

class Controller_Siggy extends Controller 
{
		private $access=null;
		
		private $groupData = array();
	//	private $authPassword = '';
		private $trusted = false;
		private $igb = false;
		
		//new 
		private $authStatus = false;
		
		private $charID = 0;
		private $corpID = 0;
		private $charName = '';
		
		private $apiCharInfo = array();
		
		function __construct(Kohana_Request $request, Kohana_Response $response)
		{
			$this->igb = miscUtils::isIGB();
			$this->trusted = miscUtils::getTrust();	
			
			$this->access = new access();
			
			
			Cookie::$salt = 'y[$e.swbDs@|Gd(ndtUSy^';
			
			$this->authStatus = $this->access->authenticate();
			$this->groupData =& $this->access->accessData;			
			
			parent::__construct($request, $response);
		}
		
		public function action_index( $ssname = '' )
		{
				header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
				$mapOpen = ( isset($_COOKIE['mapOpen'] ) ? intval($_COOKIE['mapOpen']) : 0 );
				if( $this->igb )
				{
						if( $this->authStatus == AuthStatus::GPASSWRONG )
						{
								$view = View::factory('siggy/groupPassword');
								$view->groupData = $this->groupData;
								$view->trusted = $this->trusted;
								$view->wrongPass = false;
								$this->response->body($view);
						}
						elseif( $this->authStatus == AuthStatus::APILOGINNOACCESS )
						{
								$this->request->redirect('/account/noAPIAccess');
						}
						elseif( $this->authStatus == AuthStatus::APILOGINREQUIRED )
						{
								$this->request->redirect('/account/login');
						}
						else
						{
								if( $this->authStatus == AuthStatus::ACCEPTED )
								{
										if( !empty($ssname) )
										{
												$ssname = preg_replace("/[^a-zA-Z0-9]/", "", $ssname);
												$requested = true;
										}
										else
										{
												$ssname = $_SERVER['HTTP_EVE_SOLARSYSTEMNAME'];
												$requested = false;
										}

										$view = View::factory('siggy/siggyMain');
										//$this->getSystemList();
										$view->initialSystem = false;
										if( $ssname )
										{
												$sysData = $this->getSystemData($ssname);
												if( $sysData )
												{
														$view->systemData = $sysData;
														$view->initialSystem = true;
												}
												else
												{
														$requested = false;
														$view->systemData = array('id' => 30000142, 'name' => 'Jita');
														$view->initialSystem = true;
												}
										}
									
										$view->trusted = $this->trusted;
										$view->group = $this->groupData;
										$view->mapOpen = $mapOpen;
										$view->requested = $requested;
										$view->charID = $this->groupData['charID'];
										$view->corpID = $this->groupData['corpID'];
										$view->charName = $this->groupData['charName'];
										$view->igb = true;
										$view->apilogin = ( $this->groupData['authMode'] == 2 ? true : false);
										
										$this->response->body($view);
								}
								else
								{
										$view = View::factory('siggy/accessMessage');
										$view->groupData = $this->groupData;
										$view->trusted = $this->trusted;
										$view->set('offlineMode', false);
										$this->response->body($view);
								}
						}
				}
				else
				{
						if( $this->authStatus == AuthStatus::ACCEPTED )
						{
								if( !empty($ssname) )
								{
										$ssname = preg_replace("/[^a-zA-Z0-9]/", "", $ssname);
								}
								else
								{
										$ssname ='Jita';
								}
								$requested = true;

								$view = View::factory('siggy/siggyMain');
								//$this->getSystemList();
								$view->initialSystem = false;
								if( $ssname )
								{
										$sysData = $this->getSystemData($ssname);
										if( $sysData )
										{
												$view->systemData = $sysData;
												$view->initialSystem = true;
										}
										else
										{
												$view->systemData = array('id' => 30000142, 'name' => 'Jita');
												$view->initialSystem = true;
										}
								}
							
								$view->trusted = $this->trusted;
								$view->group = $this->groupData;
								$view->mapOpen = $mapOpen;
								$view->requested = $requested;
								$view->charID = $this->groupData['charID'];
								$view->corpID = $this->groupData['corpID'];
								$view->charName = $this->groupData['charName'];
								$view->igb = false;
								$view->apilogin = true;
								
								$this->response->body($view);
						}
						else
						{
								$auth = simpleauth::instance();
								$user = $auth->get_user();
								if( $this->authStatus == AuthStatus::APILOGINREQUIRED )
								{
									//	$view = View::factory('siggy/userLogin');
									//	$view->invalidLogin = false;
									//	$this->response->body($view);
										$this->request->redirect('/account/login');
								}
								elseif ( $this->authStatus == AuthStatus::APILOGININVALID )
								{
										$this->request->redirect('/account/noAPIAccess');
										
								}
								else
								{
										$this->request->redirect('/account/noAPIAccess');
								}
						}
				}
		}
	
	public function action_stats()
	{
		if(	 !$this->isAuthed() )
		{
			$view = View::factory('siggy/groupPassword');
			$view->groupData = $this->groupData;
			$view->trusted = $this->trusted;
			$view->wrongPass = false;
			$this->response->body($view);
			return;
		}			 

		date_default_timezone_set('UTC');
		
		$start = strtotime( date('Y').'W'.date('W') );
		$end = strtotime( date('Y').'W'.date('W')+1 );
		
		//adds
		$groupTop10 = DB::query(Database::SELECT, "SELECT charID, charName, sum(adds) as adds FROM stats WHERE groupID=:group AND dayStamp >= :start AND dayStamp < :end AND adds != 0 GROUP BY charID  ORDER BY adds DESC LIMIT 0,10")
										->param(':group', $this->groupData['groupID'])->param(':start', $start)->param(':end', $end)->execute()->as_array();	 
		
		$groupTop10Max = 0;
		foreach( $groupTop10 as &$p )
		{
			if( $groupTop10Max < $p['adds'] )
			{
				$groupTop10Max = $p['adds'];
			}
		}
		
		//edits
		$groupTop10Edits = DB::query(Database::SELECT, "SELECT charID, charName, sum(updates) as edits FROM stats WHERE groupID=:group AND dayStamp >= :start AND dayStamp < :end AND updates != 0 GROUP BY charID  ORDER BY edits DESC LIMIT 0,10")
										->param(':group', $this->groupData['groupID'])->param(':start', $start)->param(':end', $end)->execute()->as_array();	 
		
		$groupTop10EditsTotal = 0;
		foreach( $groupTop10Edits as &$p )
		{
			$groupTop10EditsTotal += $p['edits'];
		}		
		
		
		//wormholes
		$groupTop10WHs = DB::query(Database::SELECT, "SELECT charID, charName, sum(wormholes) as wormholes FROM stats WHERE groupID=:group AND dayStamp >= :start AND dayStamp < :end AND wormholes != 0 GROUP BY charID  ORDER BY wormholes DESC LIMIT 0,10")
										->param(':group', $this->groupData['groupID'])->param(':start', $start)->param(':end', $end)->execute()->as_array();	 
		
		$groupTop10WHsTotal = 0;
		foreach( $groupTop10WHs as &$p )
		{
			$groupTop10WHsTotal += $p['wormholes'];
		}		
		
		
		//last week
		$lastwstart = strtotime(date('Y').'W'.(date('W')-1) );
		$lastwend = strtotime(date('Y').'W'.date('W') );
		
		//adds
		$groupTop10LastWeek = DB::query(Database::SELECT, "SELECT charID, charName, sum(adds) as adds FROM stats WHERE groupID=:group AND dayStamp >= :start AND dayStamp < :end AND adds != 0 GROUP BY charID  ORDER BY adds DESC LIMIT 0,10")
										->param(':group', $this->groupData['groupID'])->param(':start', $lastwstart)->param(':end', $lastwend)->execute()->as_array();	 
		
		$groupTop10LastWeekMax  = 0;
		foreach( $groupTop10LastWeek as &$p )
		{
			if( $groupTop10LastWeekMax < $p['adds'] )
			{
				$groupTop10LastWeekMax = $p['adds'];
			}
		}
		
		//edits
		$groupTop10EditsLastWeek = DB::query(Database::SELECT, "SELECT charID, charName, sum(updates) as edits FROM stats WHERE groupID=:group AND dayStamp >= :start AND dayStamp < :end AND updates != 0 GROUP BY charID  ORDER BY edits DESC LIMIT 0,10")
										->param(':group', $this->groupData['groupID'])->param(':start', $lastwstart)->param(':end', $lastwend)->execute()->as_array();	 
		
		$groupTop10EditsLastWeekTotal = 0;
		foreach( $groupTop10EditsLastWeek as &$p )
		{
			$groupTop10EditsLastWeekTotal += $p['edits'];
		}				
		
		//wormholes
		$groupTop10WHsLastWeek = DB::query(Database::SELECT, "SELECT charID, charName, sum(wormholes) as wormholes FROM stats WHERE groupID=:group AND dayStamp >= :start AND dayStamp < :end AND wormholes != 0 GROUP BY charID  ORDER BY wormholes DESC LIMIT 0,10")
										->param(':group', $this->groupData['groupID'])->param(':start', $lastwstart)->param(':end', $lastwend)->execute()->as_array();	 
		
		$groupTop10WHsLastWeekTotal = 0;
		foreach( $groupTop10WHsLastWeek as &$p )
		{
			$groupTop10WHsLastWeekTotal += $p['wormholes'];
		}				
		
		$view = View::factory('siggy/stats');
		$view->group = $this->groupData;
		$view->trusted = $this->trusted;
		
		$view->groupTop10 = $groupTop10;
		$view->groupTop10Max = $groupTop10Max;

		$view->groupTop10Edits = $groupTop10Edits;
		$view->groupTop10EditsTotal = $groupTop10EditsTotal;		
		
		$view->groupTop10WHs = $groupTop10WHs;
		$view->groupTop10WHsTotal = $groupTop10WHsTotal;
		
		$view->groupTop10LastWeek = $groupTop10LastWeek;
		$view->groupTop10LastWeekMax = $groupTop10LastWeekMax;
		
		$view->groupTop10EditsLastWeek = $groupTop10EditsLastWeek;
		$view->groupTop10EditsLastWeekTotal = $groupTop10EditsLastWeekTotal;
		
		$view->groupTop10WHsLastWeek = $groupTop10WHsLastWeek;
		$view->groupTop10WHsLastWeekTotal = $groupTop10WHsLastWeekTotal;
		
		
		
		
		
	//	$view->groupAllTimeTop10 = $groupAllTimeTop10;
	//	$view->groupAllTimeTop10Total = $groupAllTimeTop10Total;
		$this->response->body($view);
		
	}
	
	public function action_groupAuth()
	{
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		if( !$this->trusted )
		{
			return;
		}
		
		if( isset($_POST['authPassword']) )
		{
			$pass = sha1($_POST['authPassword'].$this->groupData['authSalt']);
			if( !empty( $this->groupData['sgAuthPassword'] ) )
			{
				if( $pass == $this->groupData['sgAuthPassword'] )
				{
					Cookie::set('authPassword', $pass, 365*60*60*24);
					$this->request->redirect('/');
				}
				else
				{
					$view = View::factory('siggy/groupPassword');
					$view->groupData = $this->groupData;
					$view->trusted = $this->trusted;
					$view->wrongPass = true;
					$this->response->body($view);
				}
			}
			elseif( !empty($this->groupData['authPassword']) )
			{
				if( $pass == $this->groupData['authPassword'] )
				{
					Cookie::set('authPassword', $pass, 365*60*60*24);
					$this->request->redirect('/');
				}
				else
				{
					$view = View::factory('siggy/groupPassword');
					$view->groupData = $this->groupData;
					$view->trusted = $this->trusted;
					$view->wrongPass = true;
					$this->response->body($view);
				}
			}
			else
			{
				echo 'No group password set, why are you here?';
			}
		}
	}
	
	public function action_systemData($name='')
	{
			if ($this->request->is_ajax()) {
					$this->profiler = NULL;
					$this->auto_render = FALSE;
					header('content-type: application/json');
			}
			
			if( !empty($name ) )
			{
				$systemData = $this->getSystemData($name);
				echo json_encode($systemData);
				die();

			}
		
	}
	
	private function getSystemList()
	{
			//removed	 ORDER BY sa.inUse DESC, sa.lastActive DESC because the client sorts it anyway
			$time = time()-60*60*24;
			
			$extra = '';
			if( !$this->shouldSysListShowReds() )
			{
				$extra = 'AND sa.inUse = 1 ';
			}
			
			$systems = DB::query(Database::SELECT, "SELECT sa.systemID,ss.name,ss.sysClass,sa.displayName,sa.inUse,sa.lastActive,sa.activity FROM activesystems sa 
			 INNER JOIN solarsystems ss ON ss.id = sa.systemID
			WHERE sa.groupID=:group AND sa.subGroupID=:subgroup AND sa.lastActive >=:time ".$extra."AND sa.lastActive != 0")
										->param(':group', $this->groupData['groupID'])->param(':subgroup', $this->groupData['subGroupID'])->param(':time', $time)->execute()->as_array('systemID');	 
										
			return $systems;
	}

	private function getSystemData( $name )
	{
			$systemQuery = DB::query(Database::SELECT, "SELECT ss.*,se.effectTitle, r.regionName, c.constellationName,sa.displayName, sa.inUse,sa.activity FROM solarsystems ss 
			INNER JOIN systemeffects se ON ss.effect = se.id
			INNER JOIN regions r ON ss.region = r.regionID
			INNER JOIN constellations c ON ss.constellation = c.constellationID
			INNER JOIN activesystems sa ON ss.id = sa.systemID
			WHERE ss.name=:name AND sa.groupID = :group AND sa.subGroupID=:subgroup")
										->param(':name', $name)->param(':group', $this->groupData['groupID'])->param(':subgroup', $this->groupData['subGroupID'])->execute();
			
			//system exists
			if(	 $systemQuery->count() < 1 )
			{
				//system potentially lacking activesystems entry
				//do this the long way :(
				$system = DB::query(Database::SELECT, 'SELECT * FROM	solarsystems WHERE name=:name')->param(':name', $name)->execute()->current();
				if( isset( $system['id'] ) )
				{
					$activeSystemQuery = DB::query(Database::SELECT, 'SELECT * FROM activesystems WHERE systemID=:id AND groupID=:group AND subGroupID=:subgroup')->param(':id', $system['id'])->param(':group',$this->groupData['groupID'])->param(':subgroup', $this->groupData['subGroupID'])->execute();
					if( $activeSystemQuery->count() < 1 )
					{
						$insert['systemID'] = $system['id'];
						$insert['groupID'] = $this->groupData['groupID'];
						$insert['subGroupID'] = $this->groupData['subGroupID'];
						DB::insert('activesystems', array_keys($insert) )->values(array_values($insert))->execute();
				
						//try again
						//dont like this but ah well for now
						$systemQuery = DB::query(Database::SELECT, "SELECT ss.*,se.effectTitle, r.regionName, c.constellationName,sa.displayName, sa.inUse FROM solarsystems ss 
						INNER JOIN systemeffects se ON ss.effect = se.id
						INNER JOIN regions r ON ss.region = r.regionID
						INNER JOIN constellations c ON ss.constellation = c.constellationID
						INNER JOIN activesystems sa ON ss.id = sa.systemID
						WHERE ss.name=:name AND sa.groupID = :group AND sa.subGroupID=:subgroup")
													->param(':name', $name)->param(':group', $this->groupData['groupID'])->param(':subgroup', $this->groupData['subGroupID'])->execute();

					}
					else
					{
						//LOL WOOPS ERROR
					}				 
					//$systemData = $systemQuery->current();
				}
				else
				{
					//system does not exist
				}
			}			 
			
			$systemData = $systemQuery->current();
			if( !$systemData['id'] )
			{
				return FALSE;
			}
			
			$systemData['staticData'] = array();
										
			$staticData = DB::query(Database::SELECT, "SELECT st.* FROM staticmap sm 
			INNER JOIN statics st ON sm.staticID = st.staticID
			WHERE sm.systemID=:id")
										->param(':id', $systemData['id'])->execute()->as_array();	 
			
			if( count( $staticData ) > 0 )
			{
				$systemData['staticData'] = $staticData;
			}
			
			$end = miscUtils::getHourStamp();
			$start = miscUtils::getHourStamp(-24);
			$apiData = DB::query(Database::SELECT, "SELECT hourStamp, jumps, kills, npcKills FROM apiHourlyMapData WHERE systemID=:system AND hourStamp >= :start AND hourStamp <= :end ORDER BY hourStamp asc LIMIT 0,24")
											->param(':system', $systemData['id'])->param(':start', $start)->param(':end', $end)->execute()->as_array('hourStamp');	 
			
			$trackedJumps = DB::query(Database::SELECT, "SELECT hourStamp, jumps FROM jumpsTracker WHERE systemID=:system AND groupID=:group AND hourStamp >= :start AND hourStamp <= :end ORDER BY hourStamp asc LIMIT 0,24")
											->param(':system', $systemData['id'])->param(':group', $this->groupData['groupID'])->param(':start', $start)->param(':end', $end)->execute()->as_array('hourStamp');	 
			
			$systemData['stats'] = array();
			for($i = 23; $i >= 0; $i--)
			{
				$hourStamp = miscUtils::getHourStamp($i*-1);
				$apiJumps = ( isset($apiData[ $hourStamp ]) ? $apiData[ $hourStamp ]['jumps'] : 0);
				$apiKills = ( isset($apiData[ $hourStamp ]) ? $apiData[ $hourStamp ]['kills'] : 0);
				$apiNPC = ( isset($apiData[ $hourStamp ]) ? $apiData[ $hourStamp ]['npcKills'] : 0);
				$siggyJumps = ( isset($trackedJumps[ $hourStamp ]) ? $trackedJumps[ $hourStamp ]['jumps'] : 0);
				$systemData['stats'][] = array( $hourStamp*1000, $apiJumps, $apiKills, $apiNPC, $siggyJumps);
			}
			
			return $systemData;
	}
	
		public function action_switchMembership()
		{
				$k = $_GET['k'];
				
				if( $this->isAuthed() )
				{
						if( count( $this->groupData['groups'] ) > 1 || count( current($this->groupData['groups']) > 1) )
						{
								foreach( $this->groupData['groups'] as $g => $sgs )
								{
										foreach( $sgs as $sg )
										{
												if( md5($g.'-'.$sg) == $k )
												{
														Cookie::set('membershipChoice', $g.'-'.$sg, 365*60*60*24);
														break;
												}
										}
								}
						}
				}
				
				$this->request->redirect('/');
		}
	
	private function getMapCache()
	{
			$cache = Cache::instance( CACHE_METHOD );
			
			$cacheName = 'mapCache-'.$this->groupData['groupID'].'-'.$this->groupData['subGroupID'];
			
			if( $mapData = $cache->get( $cacheName, FALSE ) )
			{
				return $mapData;
			}
			else
			{
				$mapData = $this->rebuildMapCache();
										
				return $mapData;			
			}
	}
	
	private function rebuildMapData($groupID, $subGroupID = 0, $additionalSystems = null)
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
	
	private function rebuildMapCache()
	{
			if( !isset($this->groupData['subGroupID']) )
			{
				Kohana::$log->add(Kohana::ERROR, 'WHAT THE FUCK? missing subGroupID dump:'. print_r($this->groupData) );
			}
			$cache = Cache::instance( CACHE_METHOD );
			$cacheName = 'mapCache-'.$this->groupData['groupID'].'-'.$this->groupData['subGroupID'];
			
			$homeSystems = $this->getHomeSystems();
			$mapData = $this->rebuildMapData($this->groupData['groupID'], $this->groupData['subGroupID'], $homeSystems);
			
			$cache->set($cacheName, $mapData);		 
			
			return $mapData;
	}
	
	private function getHomeSystems()
	{			
			$homeSystems = array();
			if( $this->groupData['subGroupID'] != 0 )
			{
				if( $this->groupData['sgHomeSystemIDs'] != '' )
				{
					$homeSystems = explode(',', $this->groupData['sgHomeSystemIDs']);
				}
			}
			else
			{
				if( $this->groupData['homeSystemIDs'] != '' )
				{
					$homeSystems = explode(',', $this->groupData['homeSystemIDs']);
				}
			}
			
			return $homeSystems;
	}
	
	public function action_loadScanProfiles()
	{
		if(	 !$this->isAuthed() )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid auth'));
			exit();
		}			 
			
		$profiles = array();
		if( isset( $this->groupData['charID'] ) )
		{
			$profiles = DB::query(Database::SELECT, "SELECT * FROM scanprofiles WHERE charID=:charID")
											->param(':charID',  $this->groupData['charID'])->execute()->as_array('profileID');	
											
		}
		else
		{
			$profiles['error'] = 'No char ID';
		}
		
		echo json_encode($profiles);
		die();
	}
	
	public function action_tweakScanProfile()
	{
		if(	 !$this->isAuthed() )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid auth'));
			exit();
		}			 
			
		$mode = $_POST['mode'];
		if( !empty( $this->groupData['charID'] ) )
		{
			$update['profileName'] = strip_tags($_POST['profileName']);
			$update['covertOps'] = intval($_POST['covertOps']);
			$update['rangeFinding'] = intval($_POST['rangeFinding']);
			$update['rigs'] = intval($_POST['rigs']);
			$update['prospector'] = intval($_POST['prospector']);
			$update['sistersLauncher'] = intval($_POST['sistersLauncher']);
			$update['sistersProbes'] = intval($_POST['sistersProbes']);
			$update['preferred'] = intval($_POST['preferred']);
			$update['charID'] = $this->groupData['charID'];
			
			if( $update['preferred'] )
			{
				DB::update('scanprofiles')->set( array('preferred' => 0) )->where('charID', '=',  $this->groupData['charID'])->execute();
			}
			
			if( $mode == 'edit' )
			{
				$id = intval($_POST['profileID']);
				
				DB::update('scanprofiles')->set( $update )->where('profileID', '=', $id)->execute();
				$update['profileID'] = $id;
			}
			else
			{
				$ins = DB::insert('scanprofiles', array_keys($update) )->values(array_values($update))->execute();
				$update['profileID'] = $ins[0];
			}
			unset($update['charID']);
			echo json_encode( $update );
			die();
		}
	}
	
	public function action_deleteScanProfile()
	{
		if( !empty( $this->groupData['charID']) )
		{
			$id = intval($_POST['profileID']);
			DB::delete('scanprofiles')->where('profileID', '=', $id)->where('charID','=',  $this->groupData['charID'] )->execute();
		}
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
	
	private function isWormholeSystemByName($name)
	{
		if( preg_match('/\bJ\d{6}\b/', $name) )
		{
			return TRUE;
		}
		return FALSE;
	}
	
	public function isAuthed()
	{
			if(	 $this->authStatus != AuthStatus::ACCEPTED )
			{
				return FALSE;
			}			 
			return TRUE;
	}
	
	public function action_updateSilent()
	{
			$this->profiler = NULL;
			$this->auto_render = FALSE;
			header('content-type: application/json');
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			
			if( !isset( $_SERVER['HTTP_EVE_SOLARSYSTEMNAME']) && !isset( $_SERVER['HTTP_EVE_SOLARSYSTEMID']) )
			{
				return;
			}
			
			if(	!$this->isAuthed() )
			{
				echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid auth'));
				exit();
			}			 
			
			if( $this->igb )
			{
					$update = array('acsid' => 0, 'acsname' => '');
					
					$update['acsid'] = $lastSystemID = $actualCurrentSystemID = intval($_GET['acsid']);
					$update['acsname'] = $actualCurrentSystemName = $_GET['acsname'];
					
					if( $actualCurrentSystemID != $_SERVER['HTTP_EVE_SOLARSYSTEMID']	)
					{
								$update['acsid'] = $actualCurrentSystemID = $_SERVER['HTTP_EVE_SOLARSYSTEMID'];
								$update['acsname'] = $actualCurrentSystemName = $_SERVER['HTTP_EVE_SOLARSYSTEMNAME'];
								

								if( $this->groupData['recordJumps'] && $actualCurrentSystemID != 0 && $lastSystemID != 0 )
								{
									$hourStamp = miscUtils::getHourStamp();
									DB::query(Database::INSERT, 'INSERT INTO jumpsTracker (`systemID`, `groupID`, `hourStamp`, `jumps`) VALUES(:systemID, :groupID, :hourStamp, 1) ON DUPLICATE KEY UPDATE jumps=jumps+1')
														->param(':hourStamp', $hourStamp )->param(':systemID', $lastSystemID )->param(':groupID', $this->groupData['groupID'] )->execute();						

									DB::query(Database::INSERT, 'INSERT INTO jumpsTracker (`systemID`, `groupID`, `hourStamp`, `jumps`) VALUES(:systemID, :groupID, :hourStamp, 1) ON DUPLICATE KEY UPDATE jumps=jumps+1')
														->param(':hourStamp', $hourStamp )->param(':systemID', $actualCurrentSystemID )->param(':groupID', $this->groupData['groupID'] )->execute();									
								}							
					}
					
					//location tracking!
					if( isset($_SERVER['HTTP_EVE_CHARID']) && isset($_SERVER['HTTP_EVE_CHARNAME']) && $actualCurrentSystemID != 0 )
					{
						$broadcast = (isset($_COOKIE['broadcast']) ? intval($_COOKIE['broadcast']) : 1);
						
						DB::query(Database::INSERT, 'INSERT INTO charTracker (`charID`, `charName`, `currentSystemID`,`groupID`,`subGroupID`,`lastBeep`, `broadcast`) VALUES(:charID, :charName, :systemID, :groupID, :subGroupID, :lastBeep, :broadcast) ON DUPLICATE KEY UPDATE lastBeep = :lastBeep, currentSystemID = :systemID, broadcast = :broadcast, groupID = :groupID, subGroupID = :subGroupID')
													->param(':charID', $_SERVER['HTTP_EVE_CHARID'] )->param(':charName', $_SERVER['HTTP_EVE_CHARNAME'] )
													->param(':broadcast', $broadcast )
													->param(':systemID', $actualCurrentSystemID )->param(':groupID', $this->groupData['groupID'] )
													->param(':subGroupID', $this->groupData['subGroupID'] )->param(':lastBeep', time() )->execute();		
		 
					}
						
					echo json_encode( $update );
			}
		// echo View::factory('profiler/stats'); 
			exit();
	}
	
	private function __wormholeJump($origin, $dest)
	{
			$whHash = $this->whHashByID($origin, $dest);
			DB::query(Database::INSERT, 'INSERT INTO wormholes (`hash`, `to`, `from`, `groupID`, `subGroupID`, `lastJump`) VALUES(:hash, :to, :from, :groupID, :subGroupID, :lastJump) ON DUPLICATE KEY UPDATE lastJump=:lastJump')
								->param(':hash', $whHash )
								->param(':to', $dest )
								->param(':from', $origin)
								->param(':groupID', $this->groupData['groupID'] )
								->param(':subGroupID', $this->groupData['subGroupID'] )
								->param(':lastJump', time() )->execute();
								
			
			if( $this->groupData['recordWHJumpActivity']  && !empty( $_SERVER['HTTP_EVE_SHIPTYPEID'] ) )
			{
				$charID = ( $this->groupData['recordWHJumpNames'] ? $_SERVER['HTTP_EVE_CHARID'] : 0 );
				$charName = ( $this->groupData['recordWHJumpNames'] ? $_SERVER['HTTP_EVE_CHARNAME'] : '' );
				$jumpTime = ( $this->groupData['recordWHJumpTime'] ? time() : 0 );
				DB::query(Database::INSERT, 'INSERT INTO wormholetracker (`whHash`, `origin`, `destination`, `groupID`, `subGroupID`, `time`, `shipTypeID`,`charID`, `charName`) VALUES(:hash, :origin, :dest, :groupID, :subGroupID, :time,:shipTypeID,:charID,:charName)')
									->param(':hash', $whHash )
									->param(':dest', $dest)
									->param(':origin', $origin )
									->param(':groupID', $this->groupData['groupID'] )
									->param(':subGroupID', $this->groupData['subGroupID'] )
									->param(':time', $jumpTime )
									->param(':shipTypeID',  $_SERVER['HTTP_EVE_SHIPTYPEID'])
									->param(':charID', $charID)
									->param(':charName', $charName)
									->execute();
			}
			
			if( !in_array($whHash, $this->mapData['wormholeHashes']) )
			{
					if( is_array($this->mapData['systemIDs']) && count($this->mapData['systemIDs'])	 > 0 )
					{
							//to
							if( !in_array($dest, $this->mapData['systemIDs']) )
							{
									DB::update('activesystems')
									->set( array('inUse' => 1) )
									->where('systemID', '=', $dest)
									->where('groupID', '=', $this->groupData['groupID'])
									->where('subGroupID', '=', $this->groupData['subGroupID'])->execute();
							}
							//from
							if( !in_array($origin, $this->mapData['systemIDs']) )
							{
									DB::update('activesystems')
									->set( array('inUse' => 1) )
									->where('systemID', '=', $origin)
									->where('groupID', '=', $this->groupData['groupID'])
									->where('subGroupID', '=', $this->groupData['subGroupID'])->execute();
							}
					}
				
					$this->mapData = $this->rebuildMapCache();
					
								
					if( $this->groupData['statsEnabled'] )
					{
							DB::query(Database::INSERT, 'INSERT INTO stats (`charID`,`charName`,`groupID`,`subGroupID`,`dayStamp`,`wormholes`) VALUES(:charID, :charName, :groupID, :subGroupID, :dayStamp, 1) ON DUPLICATE KEY UPDATE wormholes=wormholes+1')
												->param(':charID', $_SERVER['HTTP_EVE_CHARID'] )->param(':charName', $_SERVER['HTTP_EVE_CHARNAME'] )
												->param(':groupID', $this->groupData['groupID'] )->param(':subGroupID', $this->groupData['subGroupID'] )->param(':dayStamp', miscUtils::getDayStamp() )->execute();
				
					}								
			}
			
			
			
	}
	
	public function action_getJumpLog()
	{
			$this->profiler = NULL;
			$this->auto_render = FALSE;
			header('content-type: application/json');
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1\
			

			if(	!$this->isAuthed() )
			{
				echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid auth'));
				exit();
			}			 
			
			if( !isset($_GET['whHash']) || empty( $_GET['whHash'] ) )
			{
				echo json_encode(array('error' => 1, 'errorMsg' => 'Missing whHash parameter.'));
				exit();
			}
			
			$hash = ($_GET['whHash']);
			
			$jumpData = array();
			$jumpData  = DB::query(Database::SELECT, "SELECT wt.shipTypeID, wt.charName, wt.charID, wt.origin, wt.destination, wt.time, s.shipName, s.mass, s.shipClass FROM wormholetracker wt 
			LEFT JOIN ships as s ON s.shipID = wt.shipTypeID 
			WHERE wt.groupID = :groupID AND wt.whHash = :hash 
			ORDER BY wt.time DESC")
			->param(':groupID', $this->groupData['groupID'])->param(':hash', $hash)->execute()->as_array();
			
			$totalMass = 0;
			foreach( $jumpData as $jump )
			{
				$totalMass += $jump['mass'];
			}
			
			$output['totalMass'] = $totalMass;
			$output['jumpItems'] = $jumpData;
	
			echo json_encode($output);
			exit();
			
	}
	
	public function action_update()
	{
			$this->profiler = NULL;
			$this->auto_render = FALSE;
			header('content-type: application/json');
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			ob_start( 'ob_gzhandler' );
			

			if(	!$this->isAuthed() )
			{
				echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid auth'));
				exit();
			}			 
			
			$chainMapOpen = ( isset($_GET['mapOpen']) ? intval($_GET['mapOpen']) : 0 );
			
			$update = array('systemUpdate' => 0, 'sigUpdate' => 0, 'systemListUpdate' => 0, 'globalNotesUpdate' => 0, 'mapUpdate' => 0, 'acsid' => 0, 'acsname' =>'');
			
			$this->mapData = groupUtils::getMapCache( $this->groupData['groupID'], $this->groupData['subGroupID'] );
			
			if( isset( $_GET['lastUpdate'] ) && isset( $_GET['systemID'] ) && $_GET['systemID'] != 0 )
			{
					$currentSystemID = intval($_GET['systemID']);
					$forceUpdate = $_GET['forceUpdate'] == 'true' ? 1 : 0;
					$_GET['lastUpdate'] = intval($_GET['lastUpdate']);
					$freeze = intval( $_GET['freezeSystem'] );
				//	$detectedSystemID = intval($_SERVER['HTTP_EVE_SOLARSYSTEMID']);
				
					$newSystemData = array();
					$update['acsid'] = $lastSystemID = $actualCurrentSystemID = intval($_GET['acsid']);
					$update['acsname'] = $lastSystemName = $actualCurrentSystemName = $_GET['acsname'];

					if( $this->igb )
					{
							if( ($actualCurrentSystemID != $_SERVER['HTTP_EVE_SOLARSYSTEMID'] ) )
							{
								//$newSystemData = $this->getSystemData( $_SERVER['HTTP_EVE_SOLARSYSTEMNAME'] );
								//fix me once CCP stops being dumb
								
									$update['acsid'] = $actualCurrentSystemID = $_SERVER['HTTP_EVE_SOLARSYSTEMID'];
									$update['acsname'] = $actualCurrentSystemName = $_SERVER['HTTP_EVE_SOLARSYSTEMNAME'];
								
								//
								
								
									if( $this->groupData['recordJumps'] && $actualCurrentSystemID != 0 && $lastSystemID != 0 )
									{
											$hourStamp = miscUtils::getHourStamp();
											DB::query(Database::INSERT, 'INSERT INTO jumpsTracker (`systemID`, `groupID`, `hourStamp`, `jumps`) VALUES(:systemID, :groupID, :hourStamp, 1) ON DUPLICATE KEY UPDATE jumps=jumps+1')
																->param(':hourStamp', $hourStamp )->param(':systemID', $lastSystemID )->param(':groupID', $this->groupData['groupID'] )->execute();						

											DB::query(Database::INSERT, 'INSERT INTO jumpsTracker (`systemID`, `groupID`, `hourStamp`, `jumps`) VALUES(:systemID, :groupID, :hourStamp, 1) ON DUPLICATE KEY UPDATE jumps=jumps+1')
																->param(':hourStamp', $hourStamp )->param(':systemID', $actualCurrentSystemID )->param(':groupID', $this->groupData['groupID'] )->execute();									
									}
								
									if( $chainMapOpen && ($lastSystemID != $actualCurrentSystemID) && ( $this->isWormholeSystemByName($lastSystemName) || $this->isWormholeSystemByName($_SERVER['HTTP_EVE_SOLARSYSTEMNAME']) ) && $actualCurrentSystemID != 0 && !empty($lastSystemID) )
									{
										$this->__wormholeJump($lastSystemID, $actualCurrentSystemID);
									}
								
							}					 
					}
					
					if( $forceUpdate || ( $this->igb && $_GET['systemName'] != $_SERVER['HTTP_EVE_SOLARSYSTEMNAME'] ) )
					{
							//$newSystemData = $this->getSystemData( $_SERVER['HTTP_EVE_SOLARSYSTEMNAME'] );
							//if specific system isn't picked then load new one
							if( !$freeze && $this->igb )
							{
									$update['systemData'] = $this->getSystemData( $_SERVER['HTTP_EVE_SOLARSYSTEMNAME'] );
									//$newSystemData = $this->getSystemData( $_SERVER['HTTP_EVE_SOLARSYSTEMNAME'] );
									//$update['systemData'] = $newSystemData;
									if( count( $update['systemData'] ) > 0 )
									{
											$update['systemUpdate'] = (int) 1;
											$currentSystemID = $update['systemData']['id'];
											//why not
											$update['systemList'] = $this->getSystemList();
											$update['systemListUpdate'] = (int) 1;
									}
							}
							//if specific system is picked, we have a forced update
							elseif( $freeze && $forceUpdate )
							{
									$update['systemData'] = $this->getSystemData( $_GET['systemName'] );
									if( count( $update['systemData'] ) > 0 )
									{
											$update['systemUpdate'] = (int) 1;
											$currentSystemID = $update['systemData']['id'];
											//why not
											$update['systemList'] = $this->getSystemList();
											$update['systemListUpdate'] = (int) 1;
									}
							}
					}
					
					//location tracking!
					if( $this->igb && isset($_SERVER['HTTP_EVE_CHARID']) && isset($_SERVER['HTTP_EVE_CHARNAME']) && $actualCurrentSystemID != 0 )
					{
						$broadcast = (isset($_COOKIE['broadcast']) ? intval($_COOKIE['broadcast']) : 1);
						
						DB::query(Database::INSERT, 'INSERT INTO charTracker (`charID`, `charName`, `currentSystemID`,`groupID`,`subGroupID`,`lastBeep`, `broadcast`) VALUES(:charID, :charName, :systemID, :groupID, :subGroupID, :lastBeep, :broadcast) ON DUPLICATE KEY UPDATE lastBeep = :lastBeep, currentSystemID = :systemID, broadcast = :broadcast, groupID = :groupID, subGroupID = :subGroupID')
													->param(':charID', $_SERVER['HTTP_EVE_CHARID'] )->param(':charName', $_SERVER['HTTP_EVE_CHARNAME'] )
													->param(':broadcast', $broadcast )
													->param(':systemID', $actualCurrentSystemID )->param(':groupID', $this->groupData['groupID'] )
													->param(':subGroupID', $this->groupData['subGroupID'] )->param(':lastBeep', time() )->execute();		
		 
					}
					
					if( $chainMapOpen == 1 )
					{
							$update['chainMap']['actives'] = array();
							if( is_array($this->mapData['systemIDs']) && count($this->mapData['systemIDs'])	 > 0 )
							{
									$activesData = array();
									$activesData = DB::query(Database::SELECT, "SELECT charName,currentSystemID FROM charTracker WHERE groupID = :groupID AND subGroupID = :subGroupID AND broadcast=1 AND currentSystemID IN(".implode(',',$this->mapData['systemIDs']).") AND lastBeep >= :lastBeep")
									->param(':lastBeep', time()-60)->param(':groupID', $this->groupData['groupID'])->param(':subGroupID', $this->groupData['subGroupID'])->execute()->as_array();
									
									if( is_array($activesData) && count($activesData) > 0 )
									{
											$actives = array();
											foreach( $activesData as $act )
											{
													if( strlen( $act['charName']) > 15 )
													{
														$act['charName'] = substr($act['charName'], 0,12).'...';
													}
												 $actives[ $act['currentSystemID'] ][] = $act['charName'];
											}
											foreach($actives as &$act )
											{
													natcasesort($act);
													$act = implode( ',', $act );
											}
											$update['chainMap']['actives'] = $actives;
									}
							}
							
							if( $_GET['mapLastUpdate'] != $this->mapData['updateTime'] )
							{
									$update['chainMap']['systems'] = $this->mapData['systems'];
									$update['chainMap']['wormholes'] = $this->mapData['wormholes'];
									$update['chainMap']['lastUpdate'] = $this->mapData['updateTime'];
									$update['mapUpdate'] = (int) 1;
							}
					}
					
					$activeSystemQuery = DB::query(Database::SELECT, 'SELECT lastUpdate FROM activesystems WHERE systemID=:id AND groupID=:group AND subGroupID=:subgroup')->param(':id', $currentSystemID)->param(':group',$this->groupData['groupID'])->param(':subgroup', $this->groupData['subGroupID'])->execute();

					$activeSystem = $activeSystemQuery->current();
					$recordedLastUpdate = $activeSystem['lastUpdate'];

					if( ($_GET['lastUpdate'] < $recordedLastUpdate) || ( $_GET['lastUpdate'] == 0 ) || $forceUpdate || $update['systemUpdate'] )
					{
							$additional = '';
							if( $this->groupData['showSigSizeCol'] )
							{
									$additional .= ',sigSize';
							}
							$update['sigData'] = DB::query(Database::SELECT, "SELECT sigID,sig, type, siteID, description, created, creator,updated,lastUpdater".$additional." FROM systemsigs WHERE systemID=:id AND groupID=:group")
											 ->param(':id', $currentSystemID)->param(':group', $this->groupData['groupID'])->execute()->as_array('sigID');	

							$update['sigUpdate'] = (int) 1;
					}
					
					if( $this->groupData['subGroupID'] != 0 )
					{
						if( ( $_GET['lastGlobalNotesUpdate'] ) < $this->groupData['sgNotesTime'] )
						{
							$update['globalNotesUpdate'] = (int) 1;
							$update['lastGlobalNotesUpdate'] = (int) $this->groupData['sgNotesTime'];
							$update['globalNotes'] = $this->groupData['sgNotes'];
						}
					}
					else
					{
						if( ( $_GET['lastGlobalNotesUpdate'] ) < $this->groupData['lastNotesUpdate'] )
						{
							$update['globalNotesUpdate'] = (int) 1;
							$update['lastGlobalNotesUpdate'] = (int) $this->groupData['lastNotesUpdate'];
							$update['globalNotes'] = $this->groupData['groupNotes'];
						}
					}
					
					
					$update['lastUpdate'] = $recordedLastUpdate;
			}
			else
			{
				$update['error'] = 'You suck';
			}
			echo json_encode( $update );
			
		// echo View::factory('profiler/stats'); 
			exit();
	}
	
	public function action_globalNotesSave()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		header('content-type: application/json');	 
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		
		if(	 !$this->isAuthed() )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid auth'));
			exit();
		}			 		
		
		$notes = strip_tags($_POST['notes']);
		if( $this->groupData['subGroupID'] != 0 )
		{
			$update['sgNotes'] = $notes;
			$update['sgNotesTime'] = time();
			DB::update('subgroups')->set( $update )->where('subGroupID', '=', $this->groupData['subGroupID'])->execute();
			groupUtils::recacheSubGroup($this->groupData['groupID']);
			
			echo json_encode($update['sgNotesTime']);
		}
		else
		{
			$update['groupNotes'] = $notes;
			$update['lastNotesUpdate'] = time();
			DB::update('groups')->set( $update )->where('groupID', '=', $this->groupData['groupID'])->execute();
			groupUtils::recacheGroup($this->groupData['groupID']);
			
			echo json_encode($update['lastNotesUpdate']);
		}
		exit();
	}
	
	public function action_sigData($systemID)
	{
			if ($this->request->is_ajax()) 
			{
					$this->profiler = NULL;
					$this->auto_render = FALSE;
					header('content-type: application/json');	 
					header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			}

			$sigData = DB::query(Database::SELECT, "SELECT sigID,sig, type, siteID, description, created FROM systemsigs WHERE systemID=:id AND groupID=:group")
										->param(':id', $systemID)->param(':group',$this->groupData['groupID'])->execute()->as_array('sigID');	 
			echo json_encode($sigData);
			exit();
	}
	
	
	
	public function action_sigAdd()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		header('content-type: application/json');
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		
		if(	 !$this->isAuthed() )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid auth'));
			exit();
		}			 
		
		if( isset($_POST['systemID']) )
		{
			$insert['systemID'] = intval($_POST['systemID']);
			$insert['sig'] = strtoupper($_POST['sig']);
			$insert['description'] = $_POST['desc'];
			$insert['created'] = time();
			$insert['siteID'] = intval($_POST['siteID']);
			$insert['type'] = $_POST['type'];
			$insert['groupID'] = $this->groupData['groupID'];
			
			if( $this->groupData['showSigSizeCol'] )
			{
					$insert['sigSize'] = ( is_numeric( $_POST['sigSize'] ) ? $_POST['sigSize'] : '' );
			}
			
			if( !empty( $this->groupData['charName'] ) )
			{
				$insert['creator'] = $this->groupData['charName'];
			}
			
			$sigID = DB::insert('systemsigs', array_keys($insert) )->values(array_values($insert))->execute();
			
			DB::update('activesystems')->set( array('lastUpdate' => time(),'lastActive' => time() ) )->where('systemID', '=', $insert['systemID'])->where('groupID', '=', $this->groupData['groupID'])->where('subGroupID', '=', $this->groupData['subGroupID'])->execute();
			
			if( $this->groupData['statsEnabled'] )
			{
				DB::query(Database::INSERT, 'INSERT INTO stats (`charID`,`charName`,`groupID`,`subGroupID`,`dayStamp`,`adds`) VALUES(:charID, :charName, :groupID, :subGroupID, :dayStamp, 1) ON DUPLICATE KEY UPDATE adds=adds+1')
									->param(':charID',  $this->groupData['charID'])->param(':charName', $this->groupData['charName'] )
									->param(':groupID', $this->groupData['groupID'] )->param(':subGroupID', $this->groupData['subGroupID'] )->param(':dayStamp', miscUtils::getDayStamp() )->execute();
	
			}
			$insert['sigID'] = $sigID[0];
			echo json_encode(array($sigID[0] => $insert ));
		}
		exit();
	}
	
	public function action_sigEdit()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		header('content-type: application/json');
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		
		if( isset($_POST['sigID']) )
		{
			$update['sig'] = strtoupper($_POST['sig']);
			$update['description'] = $_POST['desc'];
			$update['updated'] = time();
			$update['siteID'] = isset($_POST['siteID']) ? intval($_POST['siteID']) : 0;
			$update['type'] = $_POST['type'];
			
			if( $this->groupData['showSigSizeCol'] )
			{
					$update['sigSize'] = ( is_numeric( $_POST['sigSize'] ) ? $_POST['sigSize'] : ''  );
			}
			
			if( !empty( $this->groupData['charName']) )
			{
				$update['lastUpdater'] = $this->groupData['charName'];
			}
			
			$id = intval($_POST['sigID']);
			
			
			DB::update('systemsigs')->set( $update )->where('sigID', '=', $id)->execute();
			DB::update('activesystems')->set( array('lastUpdate' => time(),'lastActive' => time() ) )->where('systemID', '=', $_POST['systemID'])->where('groupID', '=', $this->groupData['groupID'])->where('subGroupID', '=', $this->groupData['subGroupID'])->execute();
			
			if( $this->groupData['statsEnabled'] )
			{
				DB::query(Database::INSERT, 'INSERT INTO stats (`charID`,`charName`,`groupID`,`subGroupID`,`dayStamp`,`updates`) VALUES(:charID, :charName, :groupID, :subGroupID, :dayStamp, 1) ON DUPLICATE KEY UPDATE updates=updates+1')
									->param(':charID',  $this->groupData['charID'] )->param(':charName', $this->groupData['charName'] )
									->param(':groupID', $this->groupData['groupID'] )->param(':subGroupID', $this->groupData['subGroupID'] )->param(':dayStamp', miscUtils::getDayStamp() )->execute();			
			}
			echo json_encode('1');
		}
		die();
	}
	
	public function action_sigRemove()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		header('content-type: application/json');
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		
		if( isset($_POST['sigID']) )
		{
			$id = intval($_POST['sigID']);
			$sigData = DB::query(Database::SELECT, 'SELECT *,ss.name as systemName FROM	 systemsigs s 
			INNER JOIN solarsystems ss ON ss.id = s.systemID
			WHERE s.sigID=:sigID AND s.groupID=:groupID')->param(':groupID', $this->groupData['groupID'])->param(':sigID', $id)->execute()->current();			
			
			DB::delete('systemsigs')->where('sigID', '=', $id)->execute();
			
			DB::update('activesystems')->set( array('lastUpdate' => time() ) )->where('systemID', '=', $_POST['systemID'])->where('groupID', '=', $this->groupData['groupID'])->where('subGroupID', '=', $this->groupData['subGroupID'])->execute();
			
			$message = $this->groupData['charName'].' deleted sig "'.$sigData['sig'].'" from system '.$sigData['systemName'];;
			if( $sigData['type'] != 'none' )
			{
				$message .= '" which was of type '.strtoupper($sigData['type']);
			}
			$this->__logAction('delsig', $message );
			echo json_encode('1');
		}
		die();
	}
	
	public function action_chainMapSave()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		header('content-type: application/json');	 
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		
		$systemData = json_decode($_POST['systemData'], TRUE);
		if( count( $systemData ) > 0 )
		{
			foreach( $systemData as $system )
			{
				if( $system['y'] < 0 || $system['y'] > 400 )
				{
					$system['y'] = 0;
				}
				
				if( $system['x'] < 0 )
				{
					$system['x'] = 0;
				}
				
				DB::update('activesystems')->set( array('x' => $system['x'], 'y' => $system['y']) )->where('systemID', '=', $system['id'])->where('groupID', '=', $this->groupData['groupID'])->where('subGroupID', '=', $this->groupData['subGroupID'])->execute();
			}
		
			$this->rebuildMapCache();
		}
		
		exit();
	}
	
	private function __logAction( $type, $message )
	{
		$insert = array( 'groupID' => $this->groupData['groupID'],
										'type' => $type,
										'message' => $message,
										'entryTime' => time()
						);
		DB::insert('logs', array_keys($insert) )->values(array_values($insert))->execute();
	}
	
	public function action_chainMapWHMassDelete()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		header('content-type: application/json');	 
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		
		$hashes = json_decode($_POST['hashes']);
		if( is_array($hashes) && count($hashes) > 0 )
		{
			foreach( $hashes as $k =>	 $v )
			{
				$hashes[$k] = "'".($v)."'";
			}
			$hashes = implode(',', $hashes);
			
			$wormholes = DB::query(Database::SELECT, 'SELECT * FROM	 wormholes WHERE hash IN('.$hashes.') AND groupID=:groupID AND subGroupID=:subGroupID')->param(':groupID', $this->groupData['groupID'])->param(':subGroupID', $this->groupData['subGroupID'])->execute();
			$systemIDs = array();
			foreach( $wormholes as $wh )
			{
				$systemIDs[] = $wh['to'];
				$systemIDs[] = $wh['from'];
			}
			$systemIDs = array_unique( $systemIDs );
			
			DB::query(Database::DELETE, 'DELETE FROM wormholes WHERE hash IN('.$hashes.') AND groupID=:groupID AND subGroupID=:subGroupID')->param(':groupID', $this->groupData['groupID'])->param(':subGroupID', $this->groupData['subGroupID'])->execute();
			
			
			DB::query(Database::DELETE, 'DELETE FROM wormholetracker WHERE whHash IN('.$hashes.') AND groupID=:groupID AND subGroupID=:subGroupID')->param(':groupID', $this->groupData['groupID'])->param(':subGroupID', $this->groupData['subGroupID'])->execute();
			
			$message = $this->groupData['charName'].' deleted wormholes with IDs: '.implode(',', $systemIDs);
			$this->__logAction('delwhs', $message );			
			
			$this->sysResetByMap( $systemIDs );
			
			$this->rebuildMapCache();
		}
		exit();
	}
	
	private function sysResetByMap($systemIDs)
	{
		if( !is_array($systemIDs) || !count($systemIDs)	 )
		{
			return;
		}
	
	
		$homeSystems = $this->getHomeSystems();
		
		//only enable this "Feature" if we have a home system, a.k.a. RAGE INSURANCE
		if( !count($homeSystems)	)
		{
			return;
		}
		
		foreach( $systemIDs as $systemID )
		{
			if( !in_array($systemID, $homeSystems) )
			{
				$check = DB::query(Database::SELECT, 'SELECT * FROM	 wormholes WHERE groupID=:groupID AND subGroupID=:subGroupID AND (`to`=:id OR `from`=:id)')->param(':groupID', $this->groupData['groupID'])->param(':subGroupID', $this->groupData['subGroupID'])->param(':id', $systemID)->execute()->current();
				if( !$check['hash'] )
				{ 
					DB::update('activesystems')->set( array('displayName' => '','inUse' => 0 , 'activity' => 0 ) )->where('systemID', '=', $systemID)->where('groupID', '=', $this->groupData['groupID'])->where('subGroupID', '=', $this->groupData['subGroupID'])->execute();
				}
			}
		}
	}
	
	public function action_chainMapWHDisconnect()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
	 // header('content-type: application/json');	 
	//	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		
		$hash = ($_POST['hash']);
	 
		$wormhole = DB::query(Database::SELECT, 'SELECT * FROM	wormholes WHERE hash=:hash AND groupID=:groupID AND subGroupID=:subGroupID')->param(':hash',$hash)->param(':groupID', $this->groupData['groupID'])->param(':subGroupID', $this->groupData['subGroupID'])->execute()->current();
				
		if( !$wormhole['hash'] )
		{
			return;
		}		 
				
		DB::query(Database::DELETE, 'DELETE FROM wormholes WHERE hash=:hash AND groupID=:groupID AND subGroupID=:subGroupID')->param(':hash',$hash)->param(':groupID', $this->groupData['groupID'])->param(':subGroupID', $this->groupData['subGroupID'])->execute();
		
		$message = $this->groupData['charName'].' deleted wormhole between system IDs: '.implode(',', array($wormhole['to'], $wormhole['from']) );
		$this->__logAction('delwh', $message );			
			
		$this->sysResetByMap( array($wormhole['to'], $wormhole['from']) );
		
		$this->rebuildMapCache();
		
	}
	
	
	public function action_chainMapWHSave()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		header('content-type: application/json');	 
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		
		if(	 !$this->isAuthed() )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid auth'));
			exit();
		}			 
		$mode = trim($_POST['mode']);
		if( $mode == 'edit' )
		{
			$update = array();
			$hash = ($_POST['hash']);
			
			$wormhole = DB::query(Database::SELECT, 'SELECT * FROM	wormholes WHERE hash=:hash AND groupID=:groupID AND subGroupID=:subGroupID')->param(':hash',$hash)->param(':groupID', $this->groupData['groupID'])->param(':subGroupID', $this->groupData['subGroupID'])->execute()->current();
					
			if( !$wormhole['hash'] )
			{
				echo json_encode(array('error' => 1, 'errorMsg' => 'Wormhole does not exist.'));
				exit();
			}		 
			
			$update['eol'] = intval($_POST['eol']);
			
			
			$update['mass'] = intval($_POST['mass']);
			
			if( !$wormhole['eol'] && $update['eol'] )
			{
				$update['eolToggled'] = time();
			}
			elseif( $wormhole['eol'] && !$update['eol'] )
			{
				$update['eolToggled'] = 0;
			}
		
			DB::update('wormholes')->set( $update )->where('hash', '=', $hash)->where('groupID', '=', $this->groupData['groupID'])->where('subGroupID', '=', $this->groupData['subGroupID'])->execute();
		}
		else
		{
			$fromSys = trim($_POST['fromSys']);
			$fromSysCurrent = intval($_POST['fromSysCurrent']);
			$toSys	= trim($_POST['toSys']);
			$toSysCurrent = intval($_POST['toSysCurrent']);
			
			$errors = array();
			if( !$fromSysCurrent && empty($fromSys) )
			{
				$errors[] = "No 'from' system selected!";
			}
			
			if( !$toSysCurrent && empty($toSys) )
			{
				$errors[] = "No 'to' system selected!";
			}
			
			if( $toSys == $fromSys || ($toSysCurrent && $fromSysCurrent ) )
			{
				$errors[] = "You cannot link a system to itself!";
			}
			
			$fromSysID = 0;
			if( $fromSysCurrent )
			{
				$fromSysID = $_SERVER['HTTP_EVE_SOLARSYSTEMID'];
			}
			elseif( !empty($fromSys) )
			{
				$fromSysID = $this->__findSystemByName($fromSys);
				if( !$fromSysID )
				{
					$errors[] = "The 'from' system could not be looked up by name.";
				}
			}
			
			$toSysID = 0;
			if( $toSysCurrent )
			{
				$toSysID = $_SERVER['HTTP_EVE_SOLARSYSTEMID'];
			}
			elseif( !empty($toSys) )
			{
				$toSysID = $this->__findSystemByName($toSys);
				if( !$toSysID )
				{
					$errors[] = "The 'to' system could not be looked up by name.";
				}
			}
			
			if( count($errors) > 0 )
			{
				echo json_encode(array('success' => 0, 'dataErrorMsgs' => $errors ) );
				exit();
			}
		
			$eol = intval($_POST['eol']);
			$mass = intval($_POST['mass']);	
			
			$whHash = $this->whHashByID($fromSysID , $toSysID);
			DB::query(Database::INSERT, 'INSERT INTO wormholes (`hash`, `to`, `from`, `eol`, `mass`, `groupID`, `subGroupID`, `lastJump`) VALUES(:hash, :to, :from, :eol, :mass, :groupID, :subGroupID, :lastJump) ON DUPLICATE KEY UPDATE eol=:eol, mass=:mass')
								->param(':hash', $whHash )->param(':to', $toSysID )->param(':from', $fromSysID	)->param(':eol', $eol	 )->param(':mass', $mass	)->param(':groupID', $this->groupData['groupID'] )->param(':subGroupID', $this->groupData['subGroupID'] )->param(':lastJump', time() )->execute();
			
		}
		$this->rebuildMapCache();
		echo json_encode( array('success' => 1) );
		
		exit();
	}
	
	//allows finding by display name or real name
	private function __findSystemByName($name)
	{
		$name = strtolower($name);
		$systemID = DB::query(Database::SELECT, 'SELECT systemID,displayName FROM activesystems WHERE LOWER(displayName) = :name AND groupID=:groupID AND subGroupID=:subGroupID')
													->param(':name', $name )->param(':groupID', $this->groupData['groupID'])->param(':subGroupID', $this->groupData['subGroupID'])->execute()->get('systemID', 0);
													
		if( $systemID == 0 )
		{
			$systemID = DB::query(Database::SELECT, 'SELECT id,name FROM solarsystems WHERE LOWER(name) = :name')
																->param(':name', $name )->execute()->get('id', 0);
																
		}
		
		return $systemID;
	}
	
	public function action_saveSystemOptions()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		header('content-type: application/json');	 
		
		if( isset($_POST['systemID']) )
		{
			$id = intval($_POST['systemID']);
			
			DB::update('activesystems')->set( array('displayName' => trim($_POST['label']),'inUse' => intval($_POST['inUse']) , 'activity' => intval($_POST['activity']) ) )->where('systemID', '=', $_POST['systemID'])->where('groupID', '=', $this->groupData['groupID'])->where('subGroupID', '=', $this->groupData['subGroupID'])->execute();
			echo json_encode('1');
			
			$this->rebuildMapCache();
		}
		exit();
	}
	
	public function action_autocompleteWH()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		
		$q = '';
		if ( isset($_GET['q']) ) 
		{
				$q = trim(strtolower($_GET['q']));
		}
		if ( empty($q) ) 
		{
				return;
		}
		
		$customsystems = DB::select(array('solarsystems.name', 'name'),array('activesystems.displayName', 'displayName'))->from('activesystems')
											->join('solarsystems', 'LEFT')->on('activesystems.systemID', '=', 'solarsystems.id')
											->where('displayName','like',$q.'%')->where('groupID', '=', $this->groupData['groupID'])->where('subGroupID', '=', $this->groupData['subGroupID'])->execute()->as_array();
		foreach($customsystems as $system)
		{
				print $system['displayName']."|".$system['name']."\n";
		}		

		$systems = DB::select(array('solarsystems.name', 'name'),array('regions.regionName', 'regionName'), array('solarsystems.sysClass', 'class'))->from('solarsystems')->join('regions', 'LEFT')->on('solarsystems.region', '=', 'regions.regionID')->where('name','like',$q.'%')->execute()->as_array();
		
		foreach($systems as $system)
		{
			if( $system['class'] >= 7 )
			{
				print $system['name']."|".$system['regionName']."\n";
			}
			else
			{
				print $system['name']."|\n";
			}
		}		
		
		die();
	}
	
	
	
	private function shouldSysListShowReds()
	{
		if( $this->groupData['subGroupID'] )
		{
			return $this->groupData['sgSysListShowReds'];
		}
		else
		{
			return $this->groupData['sysListShowReds'];
		}
	}

} // End Welcome

