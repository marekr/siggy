<?php

require_once APPPATH.'classes/groupUtils.php';
require_once APPPATH.'classes/mapUtils.php';
require_once APPPATH.'classes/miscUtils.php';

class AuthStatus
{
	const TRUSTREQUIRED = 0;
	const NOACCESS = 1;
	const GPASSWRONG = 2;

	const ACCEPTED = 3;

	//igb state default state/not logged in
	const APILOGINREQUIRED = 4;

	//bad key or api errored
	const APILOGININVALID = 5;

	//nuff said
	const APILOGINNOTENABLED = 6;

	//api char has no access/group
	const APILOGINNOACCESS = 7;
}


class access
{
	private $trusted = false;
	private $igb = false;

	private $authPassword = '';
	private $authStatus = AuthStatus::NOACCESS;
	public $accessData = array();

	function __construct()
	{
		Cookie::$salt = 'y[$e.swbDs@|Gd(ndtUSy^';

		$this->trusted = miscUtils::getTrust();
		$this->igb = miscUtils::isIGB();
	}

	public function authenticate()
	{
		if( $this->igb )
		{
			if( $this->trusted )
			{
				$this->accessData = $this->get_access_data( $_SERVER['HTTP_EVE_CORPID'], $_SERVER['HTTP_EVE_CHARID'] );
				$this->accessData['charID'] = $_SERVER['HTTP_EVE_CHARID'];
				$this->accessData['corpID'] = $_SERVER['HTTP_EVE_CORPID'];
				$this->accessData['charName'] = $_SERVER['HTTP_EVE_CHARNAME'];

				if( $this->accessData['groupID'] )
				{
					if( $this->accessData['api_login_required'] )
					{
						return $this->__checkAccountAuth();
					}
					else if( $this->accessData['group_password_required'] )	//group password only?
					{
						$groupID = intval($this->accessData['groupID']);
						$this->authPassword = Cookie::get('auth-password-' .$groupID, '');

						if( $this->authPassword == $this->accessData['group_password'] )
						{
							$this->authStatus = AuthStatus::ACCEPTED;
						}
						else
						{
							$this->authStatus = AuthStatus::GPASSWRONG;
						}
					}
					else
					{
						$this->authStatus = AuthStatus::ACCEPTED;
					}
				}
				else
				{
					$this->authStatus = AuthStatus::NOACCESS;
				}
			}
			else
			{
				$this->authStatus = AuthStatus::TRUSTREQUIRED;
			}
		}
		else
		{
			return $this->__checkAccountAuth();
		}

		return $this->authStatus;
	}

	private function __checkAccountAuth()
	{
		if ( Auth::loggedIn() )
		{
			if( $this->apiCharInfo = Auth::$user->apiCharCheck() )
			{
				$this->accessData = $this->get_access_data( $this->apiCharInfo['corpID'], $this->apiCharInfo['charID'] );
				$this->accessData['charID'] = $this->apiCharInfo['charID'];
				$this->accessData['corpID'] = $this->apiCharInfo['corpID'];
				$this->accessData['charName'] = $this->apiCharInfo['charName'];

				if( $this->accessData['groupID'] )
				{
					if( $this->accessData['group_password_required'] )
					{
						$groupID = intval($this->accessData['groupID']);
						$this->authPassword = Cookie::get('auth-password-' .$groupID, '');

						if( $this->authPassword == $this->accessData['group_password'] )
						{
							$this->authStatus = AuthStatus::ACCEPTED;
						}
						else
						{
							$this->authStatus = AuthStatus::GPASSWRONG;
						}
					}
					else
					{
						$this->authStatus = AuthStatus::ACCEPTED;
					}
				}
				else
				{
					$this->authStatus = AuthStatus::APILOGINNOACCESS;
				}
			}
			else
			{
				$this->authStatus = AuthStatus::APILOGININVALID;
			}
		}
		else
		{
			$this->authStatus = AuthStatus::APILOGINREQUIRED;
		}

		return $this->authStatus;
	}

	public function get_access_data($corp_id = 0, $char_id = 0)
	{
		/* Result array */
		$access_data = array();

		$corp_id = intval($corp_id);
		$char_id = intval($char_id);

		$default = array('groupID' =>0, 'group_password_required' => false, 'group_password' => '', 'api_login_required' => false, 'data_type' => 'none');

		if( empty( $corp_id ) || empty($char_id)  )
		{
			return $default;
		}

		$chosenGroupID = intval(Cookie::get('membershipChoice', -1));

		$accessGroupID = 0;

		//start forming a list of possible groups
		$all_groups = array();
		$corp_data = groupUtils::getCorpData( $corp_id );
		$char_data = groupUtils::getCharData( $char_id );

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
		$groupData['corpID'] = $corp_id;
		$groupData['charID'] = $char_id;
		$groupData['accessible_chainmaps'] = $this->_buildAccessChainmaps($groupData);
		$groupData['active_chain_map'] = $this->_getChainMapID($groupData);
		$groupData['access_groups'] = $all_groups;



		$out = $groupData;

		return $out;
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
