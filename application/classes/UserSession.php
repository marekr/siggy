<?php

use Carbon\Carbon;

class UserSession {

	public $charID = 0;
	public $charName = "";
	public $corpID = 0;
	public $groupID = 0;
	public $group = null;

	public $sessionID = "";

	public $accessData = array();
	public $sessionData = array();

	public function __construct()
	{
		// default char,corp id
		$this->charID = 0;
		$this->charName = '';
		$this->corpID = 0;

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
			$this->reloadUserSession();
		}


		//attempt to find existing session
		$this->sessionData = $this->__fetchSessionData();

		if( !isset($this->sessionData['id']) )
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


			$this->reloadUserSession();
			$this->sessionData = $this->__fetchSessionData();
		}

		// finally load user ID
		if( $this->sessionData['user_id'] != 0 )
		{
			Auth::$user->loadByID( $this->sessionData['user_id'] );
		}

		/* Don't use session data for char name, id and corp id to avoid
		   IGB header issues */
		$this->groupID = $this->sessionData['group_id'];
		$this->group = Group::find($this->groupID);

		$this->charName = $this->sessionData['character_name'];
		$this->charID = $this->sessionData['character_id'];
		$this->corpID = $this->sessionData['corporation_id'];

		$this->getAccessData();

		$this->__updateSession();
	}


	private function __fetchSessionData()
	{
		$sess = DB::query(Database::SELECT, 'SELECT id,user_id,group_id,character_id,character_name,corporation_id,csrf_token FROM sessions WHERE id=:id')
					->param(':id', $this->sessionID)
					->execute()
					->current();

		return $sess;
	}

	public function destroy()
	{
		DB::delete('sessions')->where('id', '=', $this->sessionID)->execute();

		Cookie::delete('sessionID');
	}

	private function __generateSessionID()
	{
		$sessionID = md5(uniqid(microtime()) . Request::$client_ip . Request::$user_agent);
		return $sessionID;
	}

	private function __generateCSRF()
	{
		$sessionID = sha1(uniqid(microtime()) . Request::$client_ip . Request::$user_agent);
		return $sessionID;
	}

	public function reloadUserSession()
	{
		if( empty($this->sessionID) || !Auth::loggedIn() )
		{
			return;
		}

		$update = array( 'user_id' => Auth::$user->data['id'],
						 'group_id' => Auth::$user->data['groupID'],
					//	 'chainmap_id' => Auth::$user->data['active_chain_map'],
                         'character_name' => Auth::$user->data['char_name'],
                         'character_id' => Auth::$user->data['char_id'],
                         'corporation_id' => Auth::$user->data['corp_id']
						 );

		DB::update('sessions')
			->set( $update )
			->where('id', '=',  $this->sessionID)
			->execute();


		$this->charID = $update['character_id'];
		$this->charName = $update['character_name'];
		$this->corpID = $update['corporation_id'];
		$this->groupID = $update['group_id'];

		$this->getAccessData();
	}

	private function __generateSession()
	{
		// corp_id will most likely only be "valid" for IGB/non-auth user sessions
		// so we must update it too
		$insert = array( 'id' => $this->sessionID,
					'character_id' => $this->charID,
					'character_name' => $this->charName,
					'corporation_id' => $this->corpID,
					'created_at' => Carbon::now()->toDateTimeString(),
					'ip_address' => Request::$client_ip,
					'user_agent' => Request::$user_agent,
					'csrf_token' => $this->__generateCSRF(),
					'type' => 'guest',
					'user_id' => ( isset(Auth::$user->data['id']) ? Auth::$user->data['id'] : 0 ),
					'group_id' => ( isset(Auth::$user->data['groupID']) ? Auth::$user->data['groupID'] : 0 ),
				//	'chainmap_id' =>  ( isset(Auth::$user->data['active_chain_map']) ? Auth::$user->data['active_chain_map'] : 0 ),
				  );

		DB::insert('sessions', array_keys($insert) )->values(array_values($insert))->execute();

		Cookie::set('sessionID', $this->sessionID);

		return TRUE;
	}

	private function __determineSessionType()
	{
		$type = 'guest';

		if( Auth::loggedIn() )
		{
			$type = 'user';
		}

		return $type;
	}

	private function __updateSession()
	{
		if( empty($this->sessionID) )
		{
			return;
		}

		$type = $this->__determineSessionType();

		/* Shitty fix, always update groupID because we don't on creaton have a valid one */
		$update = array( 'updated_at' => Carbon::now()->toDateTimeString(),
						 'group_id' => $this->groupID,
						 'type' => $type,
						 'chainmap_id' => ( isset($this->accessData['active_chain_map']) ? $this->accessData['active_chain_map'] : 0 )
						);


		DB::update('sessions')
			->set( $update )
			->where('id', '=',  $this->sessionID)
			->execute();
	}

	public function validateGroup()
	{
		if( Auth::$session->group->findGroupMember(GroupMember::TypeChar, $this->charID) != null )
		{
			return TRUE;
		}
		
		if( Auth::$session->group->findGroupMember(GroupMember::TypeCorp, $this->corpID) != null )
		{
			return TRUE;
		}

		if( $this->group != null )
		{
			$this->group = null;
			$this->groupID = 0;

			$this->__updateSession();
		}

		return FALSE;
	}

	public function getAccessData()
	{
		/* Result array */
		$accessData = [];

		//store the possible groups
		if($this->group != null)
		{
			$accessData['active_chain_map'] = $this->_getChainMapID($this->group->chainMaps());
		}

		$this->accessData = $accessData;
	}

	private $accessibleGroups = null;

	public function accessibleGroups()
	{
		if($this->accessibleGroups == null)
		{
			//start forming a list of possible groups
			$all_groups = [];
			$corp_data = groupUtils::getCorpData( $this->corpID );
			$char_data = groupUtils::getCharData( $this->charID );

			$access_type = 'char';
			if( $corp_data !== FALSE && $char_data != FALSE )
			{
				$all_groups = array_replace($corp_data['groups'],$char_data['groups']);
			}
			else if( $corp_data !== FALSE )
			{
				$all_groups = $corp_data['groups'];
			}
			else if ($char_data != FALSE )
			{
				$all_groups = $char_data['groups'];
			}

			$this->accessibleGroups = $all_groups;
		}

		return $this->accessibleGroups;
	}

	private $accessibleChainMaps = null;

	public function accessibleChainMaps()
	{
		if($this->accessibleChainMaps == null)
		{
			$this->accessibleChainMaps  = $this->_buildAccessChainmaps($this->group->chainMaps());
		}

		return $this->accessibleChainMaps;
	}

	private function _buildAccessChainmaps($chainmaps)
	{
		$accessibleChainmaps = [];
		foreach($chainmaps as $id => $c)
		{
			foreach($c['access'] as $p)
			{
				if( ($p['memberType'] == 'corp' && $p['eveID'] == $this->corpID)
						|| ($p['memberType'] == 'char' && $p['eveID'] == $this->charID) )
				{
					$accessibleChainmaps[$c['chainmap_id']] = $c;
				}
			}
		}
		return $accessibleChainmaps;
	}

	private function _getDefaultChainMapID($chainmaps)
	{
		//to make usage "neat" for now, we first see if we have access to a default chain map
		foreach($chainmaps as $id => $c)
		{
			foreach($c['access'] as $p)
			{
				if( $c['chainmap_type'] == 'default' && ( ($p['memberType'] == 'corp' && $p['eveID'] == $this->corpID)
														|| ($p['memberType'] == 'char' && $p['eveID'] == $this->charID) ) )
				{
					return $c['chainmap_id'];
				}
			}
		}

		//otherwise grab the first one we do have access to
		foreach($chainmaps as $id => $c)
		{
			foreach($c['access'] as $p)
			{
				if( ($p['memberType'] == 'corp' && $p['eveID'] == $this->corpID)
						|| ($p['memberType'] == 'char' && $p['eveID'] == $this->charID) )
				{
					return $c['chainmap_id'];
				}
			}
		}

		return 0;
	}

	private function _getChainMapID($chainmaps)
	{
		$desired_id = intval(Cookie::get('chainmap', 0));
		$default_id = 0;
		if( !$desired_id )
		{
			return $this->_getDefaultChainMapID($chainmaps);
		}

		if( isset($chainmaps[ $desired_id ]) )
		{
			foreach($chainmaps[ $desired_id ]['access'] as $p)
			{
				if( ($p['memberType'] == 'corp' && $p['eveID'] == $this->corpID)
						|| ($p['memberType'] == 'char' && $p['eveID'] == $this->charID) )
				{
					return $desired_id;
				}
			}
		}
		else
		{
			$desired_id = $this->_getDefaultChainMapID($chainmaps);
			if( $desired_id )
			{
				Cookie::set('chainmaps',$desired_id);
			}

			return $desired_id;
		}

		return $this->_getDefaultChainMapID($chainmaps);
	}
}
