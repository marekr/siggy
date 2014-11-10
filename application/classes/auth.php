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
	
	const BLACKLISTED = 9;
	
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
		
		$success = TRUE;
		if( self::loggedIn() )
		{
			$success = self::$user->validateCorpChar();
		}
		
		if( !$success || !self::$session->charID || !self::$session->corpID )
		{
			self::$authStatus = AuthStatus::NOACCESS;
			return self::$authStatus;
		}
		
		if( self::$session->groupID )
		{
			if( self::$session->accessData['api_login_required'] && !self::loggedIn() )
			{
				self::$authStatus = AuthStatus::APILOGINREQUIRED;
			}
			else if( isset( self::$session->accessData['character_blacklist'] ) && 
						array_key_exists( self::$session->charID, self::$session->accessData['character_blacklist'] ) )
			{
				self::$authStatus = AuthStatus::BLACKLISTED;
			}
			else if( self::$session->accessData['group_password_required'] )	//group password only?
			{
				$groupID = intval(self::$session->accessData['groupID']);
				$authPassword = '';
				
				if( self::loggedIn() )
				{
					$authPassword = self::$user->getSavedPassword( $groupID );
				}
				else
				{
					$authPassword = Cookie::get('auth-password-' .$groupID, '');
				}

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
}