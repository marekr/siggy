<?php

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class User extends Model {
	public $perms = null;
	public $timestamps = false;

	public $activeChainMap = 0;

	public function group()
	{
		return $this->hasOne('Group','id','groupID');
	}

	public function savePassword( $groupID, $pass )
	{
		$passes = DB::insert("REPLACE INTO user_group_passwords (user_id, group_id, group_password) VALUES(:user, :group, :pass)",
								[
									'user' => $this->id,
									'group' => $groupID,
									'pass' => $pass
								]);

		$this->recacheSavedPasswords();
	}

	public function getSavedPassword( $groupID )
	{
		$cache = Cache::instance(CACHE_METHOD);

		$saved_passwords = $cache->get('user-'.$this->id.'-group-passwords');
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
		$passes = DB::select("SELECT * FROM user_group_passwords WHERE user_id=?", [$this->id]);

		foreach($passes as $p)
		{
			$saved_passwords[ $p->group_id ] = $p->group_password;
		}

		$cache->set('user-'.$this->id.'-group-passwords', $saved_passwords);

		return $saved_passwords;
	}

	public function validateCorpChar()
	{
		if( $this->findSSOCharacter( $this->char_id ) == null )
		{
			return FALSE;
		}

		$character = Character::find( $this->char_id );
		if( $character == null )
		{
			return FALSE;
		}

		/* update the corp id */
		if( $character->corporation_id != $this->corp_id )
		{
			$this->corp_id = $character->corporation_id;
			$this->save();

			if( $this->id == Auth::$user->id )
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
		return $this->findSSOCharacter($this->char_id);
	}

	public function findSSOCharacter(int $characterId)
	{
		$char = $this->ssoCharacters()->where('character_id', $characterId)->first();

		return $char;
	}

	public function ssoCharacters()
	{
		return $this->hasMany('UserSSOCharacter');
	}

	public function removeSSOCharacter(int $characterId)
	{
		$this->ssoCharacters()->where('character_id', $characterId)->delete();

		return TRUE;
	}

	public function updateSSOCharacter(int $characterId, $token, $refreshToken, $expiration)
	{
		$char = $this->ssoCharacters()->where('character_id', $characterId)->first();
		$data = [
			'access_token' => $token,
			'access_token_expiration' => $expiration,
			'refresh_token' => $refreshToken,
			'valid' => 1
		];

		$char->fill($data);
		$char->save();
		
		return TRUE;
	}

	public function addSSOCharacter($hash, $characterId, $token, $expiration, $refreshToken)
	{
		$insert = [
			'user_id' => $this->id,
			'character_owner_hash' => $hash,
			'character_id' => $characterId,
			'access_token' => $token,
			'access_token_expiration' => $expiration,
			'refresh_token' => $refreshToken,
			'valid' => 1
		];

		$this->ssoCharacters()->create($insert);

		return TRUE;
	}

	public static function findByUsername(string $username)
	{
		return self::whereRaw('LOWER(username) = ?', [$username])->first();
	}

	public function loadBy($identifier, $key)
	{
		throw new Exception('test');
		$perms = DB::select('SELECT * FROM users_group_acl WHERE user_id = ?', [$this->id]);

		$this->perms = $perms;

	}

	public function perms()
	{
		if($this->perms == null)
		{
			$perms = UserGroupPermission::findByUser($this->id);

			$this->perms = $perms;
		}

		return $this->perms;
	}

	public function updatePassword($newPass)
	{
		$this->password = Auth::hash($newPass);
		$this->save();
	}

	public function isAdmin()
	{
		return ( (isset($this->admin) && $this->admin ) ? TRUE : FALSE);
	}

	public function isGroupAdmin()
	{
		if( isset( $this->perms[ $this->groupID ] ) )
		{
			return TRUE;
		}

		return FALSE;
	}
}
