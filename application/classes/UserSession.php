<?php

use Carbon\Carbon;

class UserSession {

	public $id = "";
	public $user_id = 0;
	public $group_id = 0;
	public $corporation_id = 0;

	public $character_id = 0;
	public $character_name = "";

	public $csrf_token = "";

	public $group = null;
	public $accessData = array();

	private $accessibleGroups = null;
	private $accessibleChainMaps = null;

	public function __construct()
	{
		//try to find existing session
		$this->id = Cookie::get('sessionID','');

		//try to load the session data
		//session data fetch failed? then recreate it, or create one if no id
		if( !(!empty($this->id) && $this->__fetchSessionData())
			|| empty($this->id))
		{
			//create a new session ID
			$this->id = $this->__generateSessionID();

			$userData = [];

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
			//reload user session will have reloaded the session data from db
		}

		// finally load user ID
		if( $this->user_id != 0 )
		{
			Auth::$user->loadByID( $this->user_id );
		}

		$this->group = Group::find($this->group_id);
		$this->getAccessData();

		$this->__updateSession();
	}


	private function __fetchSessionData(): bool
	{
		$session = DB::query(Database::SELECT, 'SELECT s.*,c.name as character_name,c.corporation_id
											FROM sessions s
											LEFT JOIN characters c ON(c.id=s.character_id)
											WHERE s.id=:id')
					->param(':id', $this->id)
					->execute()
					->current();

		if($session != null)
		{
			$this->group_id = $session['group_id'];

			///crappy error handling...
			if(isset($session['character_name']))
			{
				$this->character_name = $session['character_name'];
			}

			if(isset($session['corporation_id']))
			{
				$this->corporation_id = $session['corporation_id'];
			}

			$this->character_id = $session['character_id'];
			$this->corporation_id = $session['corporation_id'];
			$this->user_id = $session['user_id'];
			$this->csrf_token = $session['csrf_token'];

			return TRUE;
		}
		
		return FALSE;
	}

	public function destroy()
	{
		DB::delete('sessions')->where('id', '=', $this->id)->execute();

		Cookie::delete('sessionID');
	}

	private function __generateSessionID(): string
	{
		$sessionID = md5(uniqid(microtime()) . Request::$client_ip . Request::$user_agent);
		return $sessionID;
	}

	private function __generateCSRF(): string
	{
		$sessionID = sha1(uniqid(microtime()) . Request::$client_ip . Request::$user_agent);
		return $sessionID;
	}

	public function reloadUserSession()
	{
		if( empty($this->id) || !Auth::loggedIn() )
		{
			return;
		}

		$update = array( 'user_id' => Auth::$user->data['id'],
						 'group_id' => Auth::$user->data['groupID'],
					//	 'chainmap_id' => Auth::$user->data['active_chain_map'],
                         'character_id' => Auth::$user->data['char_id']
						 );

		DB::update('sessions')
			->set( $update )
			->where('id', '=',  $this->id)
			->execute();

		$this->__fetchSessionData();

		$this->getAccessData();
	}

	private function __generateSession(): bool
	{
		// corp_id will most likely only be "valid" for IGB/non-auth user sessions
		// so we must update it too
		$insert = array( 'id' => $this->id,
					'character_id' => $this->character_id,
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

		Cookie::set('sessionID', $this->id);

		return TRUE;
	}

	private function __determineSessionType(): string
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
		if( empty($this->id) )
		{
			return;
		}

		$type = $this->__determineSessionType();

		/* Shitty fix, always update groupID because we don't on creaton have a valid one */
		$update = array( 'updated_at' => Carbon::now()->toDateTimeString(),
						 'group_id' => $this->group_id,
						 'type' => $type,
						 'chainmap_id' => ( isset($this->accessData['active_chain_map']) ? $this->accessData['active_chain_map'] : 0 )
						);


		DB::update('sessions')
			->set( $update )
			->where('id', '=',  $this->id)
			->execute();
	}

	public function validateGroup(): bool
	{
		if( Auth::$session->group->findGroupMember(GroupMember::TypeChar, $this->character_id) != null )
		{
			return TRUE;
		}
		
		if( Auth::$session->group->findGroupMember(GroupMember::TypeCorp, $this->corporation_id) != null )
		{
			return TRUE;
		}

		if( $this->group != null )
		{
			$this->group = null;
			$this->group_id = 0;

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

	public function accessibleGroups()
	{
		if($this->accessibleGroups == null)
		{
			//start forming a list of possible groups
			$all_groups = [];
			$corp_data = Group::findAllByGroupMembership('corp', $this->corporation_id);
			$char_data = Group::findAllByGroupMembership('char', $this->character_id);

			$access_type = 'char';
			if( count($corp_data) && count($char_data) !== FALSE )
			{
				$all_groups = array_replace($corp_data,$char_data);
			}
			else if( count($corp_data) )
			{
				$all_groups = $corp_data;
			}
			else if (count($char_data) )
			{
				$all_groups = $char_data;
			}
			$this->accessibleGroups = $all_groups;
		}

		return $this->accessibleGroups;
	}

	public function accessibleChainMaps()
	{
		if($this->accessibleChainMaps == null)
		{
			$this->accessibleChainMaps  = $this->_buildAccessChainmaps($this->group->chainMaps());
		}

		return $this->accessibleChainMaps;
	}

	private function _buildAccessChainmaps(array $chainmaps)
	{
		$accessibleChainmaps = [];
		foreach($chainmaps as $id => $c)
		{
			foreach($c['access'] as $p)
			{
				if( ($p['memberType'] == 'corp' && $p['eveID'] == $this->corporation_id)
						|| ($p['memberType'] == 'char' && $p['eveID'] == $this->character_id) )
				{
					$accessibleChainmaps[$c['chainmap_id']] = $c;
				}
			}
		}
		return $accessibleChainmaps;
	}

	private function _getDefaultChainMapID(array $chainmaps): int
	{
		//to make usage "neat" for now, we first see if we have access to a default chain map
		foreach($chainmaps as $id => $c)
		{
			foreach($c['access'] as $p)
			{
				if( $c['chainmap_type'] == 'default' && ( ($p['memberType'] == 'corp' && $p['eveID'] == $this->corporation_id)
														|| ($p['memberType'] == 'char' && $p['eveID'] == $this->character_id) ) )
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
				if( ($p['memberType'] == 'corp' && $p['eveID'] == $this->corporation_id)
						|| ($p['memberType'] == 'char' && $p['eveID'] == $this->character_id) )
				{
					return $c['chainmap_id'];
				}
			}
		}

		return 0;
	}

	private function _getChainMapID(array $chainmaps): int
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
				if( ($p['memberType'] == 'corp' && $p['eveID'] == $this->corporation_id)
						|| ($p['memberType'] == 'char' && $p['eveID'] == $this->character_id) )
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
