<?php

require_once APPPATH.'classes/usersession.php';
require_once APPPATH.'classes/user.php';

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
	
	const GUEST = 8;
}


class Auth
{
	const LOGIN_INVALID = 1;
	const LOGIN_PASSFAIL = 2;
	const LOGIN_SUCCESS = 3;

	private static $hashKey = "876D309BE9025C2F2A2C0532F9BAA0784F23139C31FF9BC515ED3FCFA10580DC";
	
	public static $session = null;
	
	public static $user = null;
	public static $authStatus = AuthStatus::NOACCESS;
	
	public static function initialize()
	{
		self::$user = new User();
		self::$session = new UserSession();
	}
	
	public static function authenticate()
	{
		if( !self::$session->charID  )
		{
			self::$authStatus = AuthStatus::GUEST;
			return self::$authStatus;
		}
		
		if( miscUtils::isIGB() )
		{
			if( !miscUtils::getTrust() )
			{
				self::$authStatus = AuthStatus::TRUSTREQUIRED;
				return self::$authStatus;
			}
		}
		
		$success = self::$user->validateCorpChar();
		
		if( !$success || !self::$session->charID || !self::$session->corpID )
		{
			self::$authStatus = AuthStatus::NOACCESS;
			return self::$authStatus;
		}
		
		if( self::$session->accessData['groupID'] )
		{
			if( self::$session->accessData['api_login_required'] && !self::loggedIn() )
			{
				self::$authStatus = AuthStatus::APILOGINREQUIRED;
			}
			else if( self::$session->accessData['group_password_required'] )	//group password only?
			{
				$groupID = intval(self::$session->accessData['groupID']);
				$authPassword = Cookie::get('auth-password-' .$groupID, '');

				if( $authPassword === self::$session->accessData['group_password'] )
				{
					self::$authStatus = AuthStatus::ACCEPTED;
				}
				else
				{
					self::$authStatus = AuthStatus::GPASSWRONG;
				}
			}
			else
			{
				self::$authStatus = AuthStatus::ACCEPTED;
			}
		}
		else
		{
			self::$authStatus = AuthStatus::NOACCESS;
		}
	
		return self::$authStatus;
	}
	
	
	public static function hash($str = '')
	{
		return hash_hmac("sha256", $str, self::$hashKey);
	} 
	
	public static function loadMember($userID)
	{
		//User::$data = array();
	}
	
	public static function autoLogin($id, $passHash)
	{
		$tmp = new User();
		$tmp->loadByID($id);
		
		if( $tmp->data['password'] == $passHash )
		{
			Auth::$user = $tmp;
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	public static function loggedIn()
	{
		return (self::$user->userLoaded());
	}
	
	public static function usernameExists($username)
	{
		$user = DB::query(Database::SELECT, 'SELECT id FROM users WHERE LOWER(username)=:username')->param(':username', strtolower($username))->execute()->current();
		
		if( isset($user['id']) && $user['id'] > 0)
		{
			return $user['id'];
		}
		
		return FALSE;
	}
	
	
	public static function emailExists($email)
	{
		$user = DB::query(Database::SELECT, 'SELECT id FROM users WHERE LOWER(email)=:email')->param(':email', strtolower($email))->execute()->current();
		
		if( isset($user['id']) && $user['id'] > 0)
		{
			return TRUE;
		}
		
		return FALSE;
	}
	
	public static function processProviderLogin()
	{
		
	}
	
	public static function processLogin($username, $password, $rememberMe = FALSE)
	{
		$tmp = new User();
		$tmp->loadByUsername($username);
		
		
		if( !isset($tmp->data['id']) )
		{
			return self::LOGIN_INVALID;
		}
		
		if( self::hash($password) === $tmp->data['password'] )
		{
			//success!
			self::$user = $tmp;
			
			$lifetime = 0;
			if( $rememberMe )
			{
				$lifetime = 60*60*24*365;	//1 year
			}
			
			Cookie::set('userID', self::$user->data['id'], $lifetime);
			Cookie::set('passHash', self::$user->data['password'], $lifetime);
			
			self::$session->reloadUserSession();
			
			return self::LOGIN_SUCCESS;
		}
		else
		{
			return self::LOGIN_PASSFAIL;
		}
		
	}
	
	public static function processLogout()
	{
		self::$session->destroy();
		
		Cookie::delete('userID');
		Cookie::delete('passHash');
	}
	
	
	public static function generatePassword($length = 8)
	{
		// start with a blank password
		$password = "";
		// define possible characters (does not include l, number relatively likely)
		$possible = "123456789abcdefghjkmnpqrstuvwxyz123456789";
		$i = 0;
		// add random characters to $password until $length is reached
		while ($i < $length)
		{
			// pick a random character from the possible ones
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);

			$password .= $char;
			$i++;

		}
		return $password;
	}
	
	
	private function __validateCorpChar()
	{
		if( Auth::loggedIn() )
		{
			if( Auth::$user->isLocal() )
			{
				$apiCharInfo = Auth::$user->apiKeyCheck();
				
				if( $apiCharInfo !== FALSE )
				{
					/* update the corp id */
					if( Auth::$user->data['corp_id'] != $apiCharInfo['corpID'] )
					{
						Auth::$user->data['corp_id'] = $corpID;
						Auth::$user->save();
					}
				}
				else
				{
					return FALSE;
				}
			}
			else
			{
				$corpID = Auth::$user->apiCharacterAffiliationGetCorp();
				
				/* update the corp id */
				if( $corpID != Auth::$user->data['corp_id'] )
				{
					Auth::$user->data['corp_id'] = $corpID;
					Auth::$user->save();
				}
			}
		}
		
		return TRUE;
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