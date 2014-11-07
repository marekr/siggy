<?php 

class User
{
	public $data = array();
	public $apiKeys = array();
	public $perms = array();
	
	public $groupID = 0;
	public $activeChainMap = 0;
	
	public function userLoaded()
	{
		return (isset($this->data['id']) && $this->data['id'] > 0);
	}
	
	public function isLocal()
	{
		return ($this->data['provider'] == 0);
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
							'char_id' => $this->data['char_id'],
							'char_name' => $this->data['char_name'],
							'corp_id' => $this->data['corp_id'],
							'apiKeyEntryID' => $this->data['apiKeyEntryID'],
							'provider' => $this->data['provider']
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
	
	public function validateCorpChar()
	{
		if( !self::userLoaded() )
		{
			return FALSE;
		}

		if( $this->isLocal() )
		{
			$apiCharInfo = $this->apiKeyCheck();
			
			if( $apiCharInfo !== FALSE )
			{
				/* update the corp id */
				if( $this->data['corp_id'] != $apiCharInfo['corpID'] )
				{
					$this->data['corp_id'] = $corpID;
					$this->save();
				}
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			$corpID = $this->apiCharacterAffiliationGetCorp();
			
			/* update the corp id */
			if( $corpID != $this->data['corp_id'] )
			{
				$this->data['corp_id'] = $corpID;
				$this->save();
			}
		}
		
		return TRUE;
	}
	
	public static function create($data)
	{
		$insert = array();
		$insert = $data;
		
		$insert['password'] = Auth::hash($insert['password']);
		$insert['active'] = TRUE;
		
		$userID = DB::insert('users', array_keys($insert) )->values(array_values($insert))->execute();
		
		return TRUE;
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
	
	public function apiCharacterAffiliationGetCorp()
	{
		//recheck
		require_once( Kohana::find_file('vendor', 'pheal/Pheal') );
		spl_autoload_register( "Pheal::classload" );
		PhealConfig::getInstance()->cache = new PhealFileCache(APPPATH.'cache/api/');
		$pheal = new Pheal( null, null, 'eve' );
		
		
		$result = $pheal->eveScope->CharacterAffiliation(array('ids' => $this->data['char_id']));
		if( isset ($result->characters[0]) )
		{
			if( $this->data['char_id'] == $result->characters[0]['characterID'] )
			{
				return $result->characters[0]['corporationID'];
			}
		}
		
		return 0;
	}
	
	
    public function apiKeyCheck()
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
			if( $this->data['apiID'] == 0 ||  $this->data['apiKey'] == '' || $this->data['char_id'] == 0 || $this->data['apiKeyInvalid'] || ($this->data['apiFailures'] >= 3) )
			{
				$this->data['char_id'] = 0;
				$this->data['corp_id'] = 0;		
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
					if( $char->characterID == $this->data['char_id'] )
					{
						$this->data['corp_id'] = $char->corporationID;								
						
						$this->data['apiLastCheck'] = time();
						$this->data['apiFailures'] = 0;
						$this->data['apiKeyInvalid'] = 0;
						$this->save();
				
						return array( 'corpID' => $this->data['corp_id'], 'charID' => $this->data['char_id'], 'charName' => $this->data['char_name'] );
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
							$this->data['char_id'] = 0;
							$this->data['corp_id'] = 0;
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
			return array( 'corpID' => $this->data['corp_id'], 'charID' => $this->data['char_id'], 'charName' => $this->data['char_name'] );
		}
		return FALSE;
    }
}
	