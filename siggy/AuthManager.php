<?php

namespace Siggy;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;

use Closure;
use \User;

class AuthManager {
	private static $hashKey = "876D309BE9025C2F2A2C0532F9BAA0784F23139C31FF9BC515ED3FCFA10580DC";

	public $session = null;

	public $user = null;
	public $authStatus = AuthStatus::NOACCESS;

	public function __construct()
	{
	}

	public function initialize()
	{
		$this->session = new UserSession();
	}

	public function session()
	{
		return $this->session;
	}

	public function user()
	{
		return $this->user;
	}

	public function setUser(User $user)
	{
		$this->user = $user;
	}

	public function getAuthStatus()
	{
		return $this->authStatus;
	}


    /**
     * Register a custom driver creator Closure.
     *
     * @param  string  $driver
     * @param  \Closure  $callback
     * @return $this
     */
    public function extend($driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback;

        return $this;
    }

	public function authenticate()
	{
		if( !$this->session->character_id  || !$this->loggedIn() )
		{
			$this->authStatus = AuthStatus::GUEST;
			return $this->authStatus;
		}

		if( !$this->user->validateCorpChar() )
		{
			$this->authStatus = AuthStatus::CHAR_CORP_INVALID;
			return $this->authStatus;
		}

		if( $this->session->group == null || !$this->session->validateGroup() )
		{
			$this->authStatus = AuthStatus::GROUP_SELECT_REQUIRED;
			return $this->authStatus;
		}

		if( $this->session->group != null )
		{
			if( count( $this->session->group->blacklistCharacters() ) &&
						array_key_exists( $this->session->character_id, $this->session->group->blacklistCharacters() ) )
			{
				$this->authStatus = AuthStatus::BLACKLISTED;
			}
			else if( $this->session->group->password_required )	//group password only?
			{
				$authPassword = $this->user->getSavedGroupPassword( $this->session->group->id );

				if( $authPassword === $this->session->group->password )
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

		return $this->authStatus;
	}


	public static function hash($str = '')
	{
		return hash_hmac("sha256", $str, self::$hashKey);
	}

	public function autoLogin(int $id, string $remember_token)
	{
		$tmp = User::retrieveByRememberToken($id, $remember_token);

		if($tmp == null)
		{
			return FALSE;
		}
		
		$this->user = $tmp;
		return TRUE;
	}

	public function loggedIn()
	{
		return ($this->user != null);
	}

	public function characterOwnerHashTied($hash)
	{
		$user = DB::selectOne('SELECT u.id FROM users u
											JOIN user_ssocharacter sc ON(sc.user_id=u.id)
											WHERE sc.character_owner_hash=:hash',[
												'hash' => $hash
											]);

		if( $user != null )
		{
			return $user->id;
		}

		return FALSE;
	}

	public function usernameExists($username)
	{
		$user = DB::selectOne('SELECT id FROM users WHERE LOWER(username)=?',[strtolower($username)]);

		if( $user != null )
		{
			return $user->id;
		}

		return FALSE;
	}


	public function emailExists($email)
	{
		$user = DB::selectOne('SELECT id FROM users WHERE LOWER(email)=?',[strtolower($email)]);

		if( $user != null )
		{
			return $user->id;
		}

		return FALSE;
	}

	/*
	 * Forces the login on a given user id because...SSO, better solution later
	 */
	public function forceLogin($id, $rememberMe = FALSE)
	{
		$tmp = User::find($id);

		if( $tmp == null )
		{
			return false;
		}

		$lifetime = 0;
		if( $rememberMe )
		{
			$lifetime = 60*60*24*365;	//1 year
		}

		$this->user = $tmp;
		
		Cookie::queue('remember', self::$user->id.'|'.self::$user->remember_token, $lifetime);

		$this->session->reloadUserSession();

		return true;
	}

	public function processLogin($username, $password, $rememberMe = FALSE): bool
	{
		$tmp = User::findByUsername($username);
		if( $tmp == null )
		{
			return false;
		}

		if( $this->hash($password) === $tmp->password )
		{
			//success!
			$this->user = $tmp;

			$lifetime = 0;
			if( $rememberMe )
			{
				$lifetime = 60*60*24*365;	//1 year
			}

			if( $rememberMe )
			{
				Cookie::queue('remember', $this->user->id.'|'.$this->user->remember_token, $lifetime);
			}

			$this->session->reloadUserSession();

			return true;
		}
		else
		{
			return false;
		}

	}

	public function processLogout()
	{
		if($this->loggedIn())
		{
			$this->user->cycleRememberToken();
		}

		$this->session->destroy();

		Cookie::queue(Cookie::forget('remember'));

		$this->user = null;
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
