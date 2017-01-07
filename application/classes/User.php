<?php

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model;

class User extends Model {
	public $perms = array();
	public $group = null;

	public $groupID = 0;
	public $activeChainMap = 0;

	public function userLoaded()
	{
		return (isset($this->data->id) && $this->data->id > 0);
	}

	public function group()
	{
		if($this->group == null || $this->groupID != $this->group->id)
		{
			$this->group = Group::find($this->groupID);
		}

		return $this->group;
	}

/*
	public function save()
	{
		if( !$this->userLoaded() )
		{
			return;
		}

		$userArray = array(	'id' => $this->data->id,
							'email' => $this->data->email,
							'password' => $this->data->password,
							'username' => $this->data->username,
							'groupID' => $this->data->groupID,
							'logins' => $this->data->logins,
							'reset_token' => $this->data->reset_token,
							'created' => $this->data->created,
							'active' => $this->data->active,
							'last_login' => $this->data->last_login,
							'admin' => $this->data->admin,
							'ip_address' => $this->data->ip_address,
							'char_id' => $this->data->char_id,
							'char_name' => $this->data->char_name,
							'corp_id' => $this->data->corp_id,
							'selected_apikey_id' => $this->data->selected_apikey_id,
							'provider' => $this->data->provider
						 );

		DB::table('users')
			->where('id', '=',  $this->data->id)
			->update( $userArray );

		//are we the current user?
		if( isset(Auth::$user->data->id) && $this->data->id == Auth::$user->data->id )
		{
			Auth::$session->reloadUserSession();
		}
		else
		{
			//we are editing someone that isnt the active session
			//purge sessions?
		}
	}
*/

	public function savePassword( $groupID, $pass )
	{
		$passes = DB::insert("REPLACE INTO user_group_passwords (user_id, group_id, group_password) VALUES(:user, :group, :pass)",
								[
									'user' => $this->data->id,
									'group' => $groupID,
									'pass' => $pass
								]);

		$this->recacheSavedPasswords();
	}

	public function getSavedPassword( $groupID )
	{
		$cache = Cache::instance(CACHE_METHOD);

		$saved_passwords = $cache->get('user-'.$this->data->id.'-group-passwords');
		if( $saved_passwords == null )
		{
			$saved_passwords = $this->recacheSavedPasswords();
		}

		return isset( $saved_passwords[ $groupID ] ) ? $saved_passwords[ $groupID ] : '';
	}

	public function recacheSavedPasswords()
	{
		$saved_passwords = array();

		$cache = Cache::instance(CACHE_METHOD);
		$passes = DB::select("SELECT * FROM user_group_passwords WHERE user_id=?", [$this->data->id]);

		foreach($passes as $p)
		{
			$saved_passwords[ $p['group_id'] ] = $p['group_password'];
		}

		$cache->set('user-'.$this->data->id.'-group-passwords', $saved_passwords);

		return $saved_passwords;
	}

	public function validateCorpChar()
	{
		if( !self::userLoaded() )
		{
			return FALSE;
		}

		if( $this->findSSOCharacter( $this->data->char_id ) == null )
		{
			return FALSE;
		}

		$character = Character::find( $this->data->char_id );
		if( $character == null )
		{
			return FALSE;
		}

		/* update the corp id */
		if( $character->corporation_id != $this->data->corp_id )
		{
			$this->data->corp_id = $character->corporation_id;
			$this->save();

			if( $this->data->id == Auth::$user->data->id )
			{
				Auth::$session->reloadUserSession();
			}
		}

		return TRUE;
	}

/*
	public static function create(array $data)
	{
		$insert = $data;

		$insert['password'] = Auth::hash($insert['password']);
		$insert['active'] = TRUE;

		$userID = DB::insert('users', array_keys($insert) )->values(array_values($insert))->execute();

		return TRUE;
	}
*/


	public function getActiveSSOCharacter()
	{
		return $this->findSSOCharacter($this->data->char_id);
	}

	public function getSSOCharacters()
	{
		$characters = DB::select("SELECT * FROM user_ssocharacter WHERE user_id=?",
								[$this->id]);

		return $characters;
	}

	public function findSSOCharacter(int $characterId)
	{
		$char = DB::selectOne("SELECT * FROM user_ssocharacter WHERE user_id=:userid AND character_id=:char_id",[$characterId, $this->data->id]);

		return $char;
	}

	public function removeSSOCharacter(int $characterId)
	{
		DB::table('user_ssocharacter')
			->where('user_id', '=',  $this->data->id)
			->where('character_id', '=',  $characterId)
			->delete();

		return TRUE;
	}

	public function updateSSOCharacter(int $characterId, $token, $refreshToken, $expiration)
	{
		$data = [
			'access_token' => $token,
			'access_token_expiration' => $expiration,
			'refresh_token' => $refreshToken,
			'valid' => 1,
			'updated_at' => Carbon::now()->toDateTimeString(),
		];

		DB::table('user_ssocharacter')
			->where('user_id', '=',  $this->data->id)
			->where('character_id', '=',  $characterId)
			->update( $data );
		
		return TRUE;
	}

	public function addSSOCharacter($hash, $characterId, $token, $expiration, $refreshToken)
	{
		$insert = [
			'user_id' => $this->data->id,
			'character_owner_hash' => $hash,
			'character_id' => $characterId,
			'access_token' => $token,
			'access_token_expiration' => $expiration,
			'refresh_token' => $refreshToken,
			'valid' => 1,
			'created_at' => Carbon::now()->toDateTimeString(),
		];

		DB::table('user_ssocharacter')->insert($insert);

		return TRUE;
	}

	public function loadByEmail($email)
	{
		$this->loadBy('email', $email);
	}

	public function loadByID($id)
	{
		$this->loadBy('id', $id);
	}

	public static function findByUsername(string $username)
	{
		return self::whereRaw('LOWER(username) = ?', [$username])->firstOrFail();
	}

	public function loadBy($identifier, $key)
	{
		throw new Exception('test');
		$perms = DB::select('SELECT * FROM users_group_acl WHERE user_id = ?', [$this->data->id]);

		$this->perms = $perms;

	}

	public function updatePassword($newPass)
	{
		$this->data->password = Auth::hash($newPass);
		$this->save();
	}

	public function isAdmin()
	{
		return ( (isset($this->data->admin) && $this->data->admin ) ? TRUE : FALSE);
	}

	public function isGroupAdmin()
	{
		if( isset( $this->perms[ $this->data->groupID ] ) )
		{
			return TRUE;
		}

		return FALSE;
	}
}
