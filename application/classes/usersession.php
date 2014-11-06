<?php

class UserSession
{
	public $charID = 0;
	public $charName = "";
	public $corpID = 0;
	public $groupID = 0;
	
	public $trusted = false;
	public $igb = false;
	
	private $sessionID = "";
	
	public $accessData = array();
	

	public function __construct()
	{
		// default char,corp id
		$this->charID = isset($_SERVER['HTTP_EVE_CHARID']) ? $_SERVER['HTTP_EVE_CHARID'] : 0;
		$this->charName = isset($_SERVER['HTTP_EVE_CHARNAME']) ? $_SERVER['HTTP_EVE_CHARNAME'] : '';
		$this->corpID = isset($_SERVER['HTTP_EVE_CORPID']) ? $_SERVER['HTTP_EVE_CORPID'] : 0;
		
		$this->igb = miscUtils::isIGB();
		$this->trusted = miscUtils::getTrust();

		//try to find existing session
		$this->sessionID = Cookie::get('sessionID');
		if( $this->sessionID == NULL )
		{
			$this->sessionID = $this->__generateSessionID();

			$userData = array();

			//reauth the member
			//remember me check
			$memberID = Cookie::get('userID');
			$passHash = Cookie::get('passHash');
			if( $memberID && $passHash )
			{

				if( !Auth::autoLogin($memberID, $passHash) )
				{
                    Cookie::delete('userID');
                    Cookie::delete('passHash');
				}

			}

			$this->__generateSession();
		}
		

		//attempt to find existing session
		$sess = $this->__fetchSessionData();
		
		if( !isset($sess['sessionID']) )
		{
			$this->sessionID = $this->__generateSessionID();

			$memberID = Cookie::get('userID');
			$passHash = Cookie::get('passHash');
			if( $memberID && $passHash )
			{
				if( !Auth::autoLogin($memberID, $passHash) )
				{
					Cookie::delete('userID');
					Cookie::delete('passHash');
				}
			}

			$this->__generateSession();
			
			$sess = $this->__fetchSessionData();
		}
		
		// finally load user ID
		if( $sess['userID'] != 0 )
		{
			Auth::$user->loadByID( $sess['userID'] );
		}
		
		$this->charID = $sess['char_id'];
		$this->charName = $sess['char_name'];
		$this->corpID = $sess['corp_id'];
		$this->groupID = $sess['groupID'];
		
		$this->getAccessData();
		
		$this->__updateSession();
	}
	
	private function __fetchSessionData()
	{			
		$sess = DB::query(Database::SELECT, 'SELECT sessionID,userID,groupID,char_id,char_name,corp_id FROM siggysessions WHERE sessionID=:id')
					->param(':id', $this->sessionID)
					->execute()
					->current();
		
		return $sess;
	}

	public function destroy()
	{
		DB::delete('siggysessions')->where('sessionID', '=', $this->sessionID)->execute();

		Cookie::delete('sessionID');
	}

	private function __generateSessionID()
	{
		$sessionID = md5(uniqid(microtime()) . Request::$client_ip . Request::$user_agent);
		return $sessionID;
	}

	public function reloadUserSession()
	{
		if( empty($this->sessionID) )
		{
			return;
		}

		$update = array( 'userID' => Auth::$user->data['id'],
						 'groupID' => Auth::$user->data['groupID'],
					//	 'chainmap_id' => Auth::$user->data['active_chain_map'],
                         'char_name' => Auth::$user->data['char_name'],
                         'char_id' => Auth::$user->data['char_id'],
                         'corp_id' => Auth::$user->data['corp_id']
						 );

		DB::update('siggysessions')->set( $update )->where('sessionID', '=',  $this->sessionID)->execute();
		
		
		$this->charID = $update['char_id'];
		$this->charName = $update['char_name'];
		$this->corpID = $update['corp_id'];
		$this->groupID = $update['groupID'];
	}

	private function __generateSession()
	{
		$insert = array( 'sessionID' => $this->sessionID,
					'char_id' => $this->charID,
					'char_name' => $this->charName,
					'created' => time(),
					'ipAddress' => Request::$client_ip,
					'userAgent' => Request::$user_agent,
					'sessionType' => ( $this->igb ? 'igb' : 'oog' ),
					'userID' => ( isset(Auth::$user->data['id']) ? Auth::$user->data['id'] : 0 ),
					'groupID' => ( isset(Auth::$user->data['groupID']) ? Auth::$user->data['groupID'] : 0 ),
				//	'chainmap_id' =>  ( isset(Auth::$user->data['active_chain_map']) ? Auth::$user->data['active_chain_map'] : 0 ),
				  );

		DB::insert('siggysessions', array_keys($insert) )->values(array_values($insert))->execute();

		Cookie::set('sessionID', $this->sessionID);

		return TRUE;
	}

	private function __updateSession()
	{
		if( empty($this->sessionID) )
		{
			return;
		}

		$type = '';
		
		if( $this->igb )
		{
			$type = 'igb';
		}
		
		if( Auth::loggedIn() )
		{
			if( Auth::$user->isLocal() )
			{
				$type = 'siggy';
			}
			else
			{
				$type = 'sso';
			}
		}
		
		$update = array( 'lastBeep' => time(),
						 'groupID' => ( isset($this->groupID) ? $this->groupID : 0 ),
						 'sessionType' => $type,
						 'chainmap_id' => ( isset($this->accessData['active_chain_map']) ? $this->accessData['active_chain_map'] : 0 )
						);

		DB::update('siggysessions')->set( $update )->where('sessionID', '=',  $this->sessionID)->execute();
	}
	
	
	public function getAccessData()
	{
		/* Result array */
		$access_data = array();


		$default = array('groupID' =>0, 'group_password_required' => false, 'group_password' => '', 'api_login_required' => false, 'data_type' => 'none');

		if( empty( $this->corpID ) || empty($this->charID)  )
		{
			return $default;
		}

		$chosenGroupID = intval(Cookie::get('membershipChoice', -1));

		$accessGroupID = 0;

		//start forming a list of possible groups
		$all_groups = array();
		$corp_data = groupUtils::getCorpData( $this->corpID );
		$char_data = groupUtils::getCharData( $this->charID );

		$access_type = 'char';
		if( $corp_data !== FALSE && $char_data != FALSE )
		{
			$all_groups = array_replace($corp_data['groups'],$char_data['groups']);

			//helper property for the chain map acl
			$access_type = 'char_corp';
		}
		else if( $corp_data !== FALSE )
		{
			$all_groups = $corp_data['groups'];

			//helper property for the chain map acl
			$access_type = 'corp';
		}
		else if ($char_data != FALSE )
		{
			$all_groups = $char_data['groups'];

			$access_type = 'char';
		}

		//check if we have any groups
		if( !count($all_groups) )
		{
			return $default;
		}

		//did we want to select a group? check if we have access :)
		if( !empty($chosenGroupID) )
		{
			if( array_key_exists ($chosenGroupID,$all_groups) )
			{
				$accessGroupID = $chosenGroupID;
			}
		}

		//no group selected? try and pick the first one
		if( !$accessGroupID  )
		{
			$tmp = current($all_groups);
			$accessGroupID = $tmp['group_id'];
		}

		//finally load the group data!
		$groupData = groupUtils::getGroupData( $accessGroupID );
		if( !$groupData )
		{
			return $default;
		}



		//store the possible groups
		$groupData['access_type'] = $access_type;
		$groupData['corpID'] = $this->corpID;
		$groupData['charID'] = $this->charID;
		$groupData['accessible_chainmaps'] = $this->_buildAccessChainmaps($groupData);
		$groupData['active_chain_map'] = $this->_getChainMapID($groupData);
		$groupData['access_groups'] = $all_groups;

		$this->accessData = $groupData;
		
		$this->groupID = $accessGroupID;
	}


	private function _buildAccessChainmaps($groupData)
	{
		$accessibleChainmaps = array();
		foreach($groupData['chainmaps'] as $id => $c)
		{
			foreach($c['access'] as $p)
			{
				if( ($p['memberType'] == 'corp' && $p['eveID'] == $groupData['corpID'])
						|| ($p['memberType'] == 'char' && $p['eveID'] == $groupData['charID']) )
				{
					$accessibleChainmaps[$c['chainmap_id']] = $c;
				}
			}
		}
		return $accessibleChainmaps;
	}

	private function _getDefaultChainMapID($groupData)
	{
		//to make usage "neat" for now, we first see if we have access to a default chain map
		foreach($groupData['chainmaps'] as $id => $c)
		{
			foreach($c['access'] as $p)
			{
				if( $c['chainmap_type'] == 'default' && ($p['memberType'] == 'corp' && $p['eveID'] == $groupData['corpID'])
														|| ($p['memberType'] == 'char' && $p['eveID'] == $groupData['charID']) )
				{
					return $c['chainmap_id'];
				}
			}
		}
		
		//otherwise grab the first one we do have access to
		foreach($groupData['chainmaps'] as $id => $c)
		{
			foreach($c['access'] as $p)
			{
				if( ($p['memberType'] == 'corp' && $p['eveID'] == $groupData['corpID'])
						|| ($p['memberType'] == 'char' && $p['eveID'] == $groupData['charID']) )
				{
					return $c['chainmap_id'];
				}
			}
		}

		return 0;
	}

	private function _getChainMapID($groupData)
	{
		$desired_id = intval(Cookie::get('chainmap', 0));
		$default_id = 0;
		if( !$desired_id )
		{
			return $this->_getDefaultChainMapID($groupData);
		}

		if( isset($groupData['chainmaps'][ $desired_id ]) )
		{
			foreach($groupData['chainmaps'][ $desired_id ]['access'] as $p)
			{
				if( ($p['memberType'] == 'corp' && $p['eveID'] == $groupData['corpID'])
						|| ($p['memberType'] == 'char' && $p['eveID'] == $groupData['charID']) )
				{
					return $desired_id;
				}
			}
		}
		else
		{
			$desired_id = $this->_getDefaultChainMapID($groupData);
			if( $desired_id )
			{
				Cookie::set('chainmaps',$desired_id);
			}

			return $desired_id;
		}

		return $this->_getDefaultChainMapID($groupData);
	}
}
