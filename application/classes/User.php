<?php

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class User extends Model {
	public $perms = null;
	public $timestamps = true;

	public $activeChainMap = 0;

	protected $fillable = [
		'username',
		'password',
		'email',
		'active'
	];

	protected $hidden = [
		'password'
	];

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

	public function groupPasswords()
	{
		return $this->hasMany('UserGroupPassword');
	}

	public function getSavedGroupPassword( $groupID )
	{
		$entry = $this->groupPasswords()->where('group_id',$groupID)->first();

		if($entry != null)
		{
			return $entry->group_password;
		}

		return '';
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

	protected static function boot()
	{
		parent::boot();
		
		static::creating(function ($model)
		{
			$model->password = Auth::hash($model->password);
			$model->active = TRUE;
		});
	}

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
		return self::whereRaw('LOWER(username) = ?', [strtolower($username)])->first();
	}

	public static function findByEmail(string $email)
	{
		return self::whereRaw('LOWER(email) = ?', [strtolower($email)])->first();
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
