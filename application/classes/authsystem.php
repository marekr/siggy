<?php

require_once APPPATH.'classes/usersession.php';

class User
{
	public $data = array();
	public $apiKeys = array();
	
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
		
		$toSaveArray = $this->data;
		//unset($toSaveArray['password']);
		
		
		DB::update('users')->set( $toSaveArray )->where('id', '=',  $this->data['id'])->execute();
		
		//are we the current user?
		if( $this->data['id'] == Auth::$user->data['id'] )
		{
			Auth::$session->reloadUserSession();
		}
		else
		{
			//we are editing someone that isnt the active session
			//purge sessions?
		}
	}
	
	public function loadByEmail($email)
	{
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
		return ( (isset($this->data['gadmin']) && $this->data['gadmin']) ? TRUE : FALSE);
	}
	
    public function apiCharCheck()
    {
		if( !self::userLoaded() )
		{
			return;
		}
		if( $this->data['apiLastCheck'] < time()-60*120 )
		{
				if( $this->data['apiID'] == 0 ||  $this->data['apiKey'] == '' || $this->data['apiCharID'] == 0 || $this->data['apiInvalid'] || ($this->data['apiFailures'] >= 3) )
				{
					return FALSE;
				}
				
				//recheck
				require_once( Kohana::find_file('vendor', 'pheal/Pheal') );
				spl_autoload_register( "Pheal::classload" );
				PhealConfig::getInstance()->cache = new PhealFileCache(APPPATH.'cache/api/');
				$pheal = new Pheal( $this->data['apiID'], $this->data['apiKey'], 'eve' );
				
				try 
				{
						try 
						{
								$results = $pheal->CharacterInfo( array( 'characterID' => $this->data['apiCharID'] )  );
								
			//					$this->update_user( $this->data['id'], array('apiCorpID' => $results->corporationID, 'apiLastCheck' => time(),'apiInvalid' => 0, 'apiFailures' => 0 ) );
								$this->data['apiCorpID'] = $results->corporationID;
								$this->data['apiLastCheck'] = time();
								$this->data['apiFailures'] = 0;
								$this->data['apiInvalid'] = 0;
								$this->save();
								
			//					$this->reload_user();
								
								return array( 'corpID' => $this->data['apiCorpID'], 'charID' => $this->data['apiCharID'], 'charName' => $this->data['apiCharName'] );
						}
						catch( PhealAPIException $e )
						{
								 switch( $e->code )
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
											$this->data['apiFailures'] = $this->data['apiFailures']+1;
											$this->save();
											return FALSE;
											break;
											
										case 221:
										case 222:
										case 223:
										case 521:
											//bad api entirely
											//$this->update_user( $this->data['id'], array('apiInvalid' => 1) );
											$this->data['apiInvalid'] = 1;
											$this->save();
											return FALSE;
											break;

										case 211:
											//expired account
											return FALSE;
											break;
											
										default:
											echo 'error: ' . $e->code . ' meesage: ' . $e->getMessage();
											return array( 'corpID' => $this->data['apiCorpID'], 'charID' => $this->data['apiCharID'], 'charName' => $this->data['apiCharName'] );
											break;
								 }
						}
				}
				catch( PhealException $e )
				{
						return array( 'corpID' => $this->data['apiCorpID'], 'charID' => $this->data['apiCharID'], 'charName' => $this->data['apiCharName'] );
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
		
		Cookie::$salt = 'y[$e.swbDs@|Gd(ndtUSy^';
		
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
	
	public static function getUser($id)
	{
		$user = DB::query(Database::SELECT, 'SELECT * FROM users WHERE id=:id')->param(':id', $id)->execute()->current();
		
		return $user;
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
			return TRUE;
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
		
		$user = DB::query(Database::SELECT, 'SELECT * FROM users WHERE LOWER(username)=:username')->param(':username', strtolower($username))->execute()->current();
		if( !isset($user['id']) )
		{
			return self::LOGIN_INVALID;
		}
		
		if( self::hash($password) === $user['password'] )
		{
				//success!
				self::$user->data = $user;
				
				$lifetime = 0;
				if( $rememberMe )
				{
					$lifetime = 60*60*24*365;	//1 year
				}
				Cookie::set('userID', $user['id'],$lifetime);
				Cookie::set('passHash', $user['password'],$lifetime);
				
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