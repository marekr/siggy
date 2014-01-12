<?php

require_once APPPATH.'classes/usersession.php';

class User
{
	public $data = array();
	public $apiKeys = array();
	public $perms = array();
	
	public $groupID = 0;
	public $subGroupID = 0;
	
	public function userLoaded()
	{
		return (isset($this->data['id']) && $this->data['id'] > 0);
	}
	
	public function save()
	{
		if( !$this->userLoaded() )
		{
			return;
		}
		
		$userArray = array(	'id' => $this->data['id'],
						'email' => $this->data['email'],
						'password' => $this->data['password'],
						'username' => $this->data['username'],
						'groupID' => $this->data['groupID'],
						'logins' => $this->data['logins'],
						'reset_token' => $this->data['reset_token'],
						'created' => $this->data['created'],
						'active' => $this->data['active'],
						'last_login' => $this->data['last_login'],
						'admin' => $this->data['admin'],
						'ip_address' => $this->data['ip_address'],
						'apiCharID' => $this->data['apiCharID'],
						'apiCharName' => $this->data['apiCharName'],
						'apiCorpID' => $this->data['apiCorpID'],
						'apiKeyEntryID' => $this->data['apiKeyEntryID']
					 );
		DB::update('users')->set( $userArray )->where('id', '=',  $this->data['id'])->execute();
		
		
		if( $this->data['apiKeyEntryID'] != 0 )
		{
			$apiArray = array( 'apiKeyInvalid' => $this->data['apiKeyInvalid'],
								'apiFailures' => $this->data['apiFailures'],
                                'apiLastCheck' => $this->data['apiLastCheck']
							 );
			DB::update('apikeys')->set( $apiArray )->where('entryID', '=',  $this->data['apiKeyEntryID'])->execute();
		}
		
		//are we the current user?
		if( isset(Auth::$user->data['id']) && $this->data['id'] == Auth::$user->data['id'] )
		{
			Auth::$session->reloadUserSession();
		}
		else
		{
			//we are editing someone that isnt the active session
			//purge sessions?
		}
	}
	
	public function getAPIKeys()
	{
		if( !$this->userLoaded() )
		{
			return;
		}
	
		$keys = DB::query(Database::SELECT, "SELECT * FROM apikeys WHERE userID=:userid")->param(':userid', $this->data['id'])
										->execute()->as_array();

		foreach($keys as &$key)
		{
			if( $key['apiID'] == 0 || $key['apiKey'] == '' )
			{
					$status = 'Missing';
			}
			elseif( $key['apiFailures'] > 3 )
			{
					$status = 'Failed';
			}
			elseif ( $key['apiKeyInvalid'] )
			{
					$status = 'Invalid';
			}
			else
			{
					$status = 'Good';
			}
			
			$key['status'] = $status;
		}
		
		return $keys;				
	}
	
	public function loadByEmail($email)
	{
		$this->loadBy('email', $email);
	}
	
	public function loadByID($id)
	{
		$this->loadBy('id', $id);
	}
	
	public function loadByUsername($username)
	{
		$this->loadBy('username', $username);
	}
	
	public function loadBy($identifier, $key)
	{
		$baseSQL = "SELECT u.*, 
					COALESCE(ak.apiID,0) as apiID, 
					COALESCE(ak.apiKey,0) as apiKey, 
					COALESCE(ak.apiKeyInvalid,0) as apiKeyInvalid, 
					COALESCE(ak.apiFailures,0) as apiFailures, 
					COALESCE(ak.apiLastCheck,0) as apiLastCheck 
					FROM users u
					LEFT JOIN apikeys ak ON(ak.entryID = u.apiKeyEntryID)";
					
		if( $identifier == 'username' )
		{
			$user = DB::query(Database::SELECT, $baseSQL .' WHERE LOWER(u.username)=:username')
											->param(':username', strtolower($key))->execute()->current();
		}
		else if( $identifier == 'id' )
		{
			$user = DB::query(Database::SELECT, $baseSQL .' WHERE u.id=:id')
											->param(':id', intval($key))->execute()->current();
		}
		else if( $identifier == 'email' )
		{		
			$user = DB::query(Database::SELECT, $baseSQL .' WHERE LOWER(u.email)=:email')
												->param(':email', strtolower($key))->execute()->current();
		}
		
		$this->data = $user;
		

		$perms = DB::query(Database::SELECT, 'SELECT * FROM users_group_acl WHERE user_id = :id')
										->param(':id', $this->data['id'])->execute()->as_array('group_id');
		
		$this->perms = $perms;
		
	}
	
	public function updatePassword($newPass)
	{
		$this->data['password'] = Auth::hash($newPass);
		$this->save();
	}
	
	public function isAdmin()
	{
		return ( (isset($this->data['admin']) && $this->data['admin'] ) ? TRUE : FALSE);
	}
	
	public function isGroupAdmin()
	{
        if( isset( $this->perms[ $this->data['groupID'] ] ) )
        {
            return TRUE;
        }

        return FALSE;
	}
	
    public function apiCharCheck()
    {
		if( !self::userLoaded() )
		{
			return FALSE;
		}

		//compatbility issue with last update, make them reselect
		if( !isset($this->data['apiLastCheck']) || !isset($this->data['apiKeyInvalid']) )
		{
			return FALSE;
		}
		
		if( $this->data['apiLastCheck'] < time()-60*120 )
		{
				if( $this->data['apiID'] == 0 ||  $this->data['apiKey'] == '' || $this->data['apiCharID'] == 0 || $this->data['apiKeyInvalid'] || ($this->data['apiFailures'] >= 3) )
				{
                    $this->data['apiCharID'] = 0;
                    $this->data['apiCorpID'] = 0;		
                    $this->save();
                                        
					return FALSE;
				}
				
				//recheck
				require_once( Kohana::find_file('vendor', 'pheal/Pheal') );
				spl_autoload_register( "Pheal::classload" );
				PhealConfig::getInstance()->cache = new PhealFileCache(APPPATH.'cache/api/');
				$pheal = new Pheal( $this->data['apiID'], $this->data['apiKey'], 'eve' );
				
				try 
				{
						//get all chars on the key
						$result = $pheal->accountScope->Characters();
						foreach($result->characters as $char )
						{
							if( $char->characterID == $this->data['apiCharID'] )
							{
								$this->data['apiCorpID'] = $char->corporationID;								
								
								$this->data['apiLastCheck'] = time();
								$this->data['apiFailures'] = 0;
								$this->data['apiKeyInvalid'] = 0;
								$this->save();
						
								return array( 'corpID' => $this->data['apiCorpID'], 'charID' => $this->data['apiCharID'], 'charName' => $this->data['apiCharName'] );
							}
						}
					
						
					//key no longer exists?
					return FALSE;
				}
				catch( PhealException $e )
				{
					if ($e instanceof PhealAPIException OR $e instanceof PhealHTTPException)
					{
						 switch( $e->getCode() )
						 {
								//bad char
								case 105:
									//$this->update_user( $this->data['id'], array('apiCharID' => 0, 'apiCorpID' => 0 ) );
									//$this->reload_user();
									$this->data['apiCharID'] = 0;
									$this->data['apiCorpID'] = 0;
									$this->save();
									return FALSE;
									break;
									
								case 202:
								case 203:
								case 204:
								case 205:
								case 210:
								case 212:
									//increment fuck you count
									//$this->update_user( $this->data['id'], array('apiFailures' => $this->data['apiFailures']+1 ) );
									//$this->reload_user();
									if( $this->data['apiFailures'] < 3 )
									{
										$this->data['apiFailures'] = $this->data['apiFailures']+1;
									}
									else
									{
										$this->data['apiKeyInvalid'] = 1;
									}
									
									$this->save();
									return FALSE;
									break;
									
								case 221:
								case 222:
								case 223:
								case 521:
								case 403:
									//bad api entirely
									//$this->update_user( $this->data['id'], array('apiInvalid' => 1) );
									$this->data['apiKeyInvalid'] = 1;
									
									
									$this->save();
									return FALSE;
									break;

								case 211:
									//expired account
									return FALSE;
									break;
									
								default:
									return FALSE;
									break;
						 }
					}
				}
				
		}
		else
		{
				return array( 'corpID' => $this->data['apiCorpID'], 'charID' => $this->data['apiCharID'], 'charName' => $this->data['apiCharName'] );
		}
		return FALSE;
    }
}
	
class Auth
{
	const LOGIN_INVALID = 1;
	const LOGIN_PASSFAIL = 2;
	const LOGIN_SUCCESS = 3;

	private static $hashKey = "876D309BE9025C2F2A2C0532F9BAA0784F23139C31FF9BC515ED3FCFA10580DC";
	
	public static $session = null;
	
	public static $user = null;
	
	public static function initialize()
	{
		
		
		self::$user = new User();
		self::$session = new UserSession();
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
	
	public static function createUser($data)
	{
		$insert = array();
		$insert = $data;
		
		$insert['password'] = self::hash($insert['password']);
		$insert['active'] = TRUE;
		
		$sigID = DB::insert('users', array_keys($insert) )->values(array_values($insert))->execute();
		
		return TRUE;
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
				Cookie::set('userID', self::$user->data['id'],$lifetime);
				Cookie::set('passHash', self::$user->data['password'],$lifetime);
				
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
}