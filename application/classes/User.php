<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Siggy\ScribeCommandBus;

use App\Facades\Auth;
use App\Facades\SiggySession;
use Laravel\Passport\HasApiTokens;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Siggy\SiggyUserProvider;

class User extends Authenticatable {
    use Notifiable;
    use HasApiTokens;

	public $perms = null;
	public $timestamps = true;

	public $activeChainMap = 0;

	protected $fillable = [
		'username',
		'password',
		'email',
		'active',
		'theme_id',
		'combine_scan_intel',
		'language',
		'default_activity',
		'char_id',
		'remember_token',
		'groupID',
	];

	/**
		* The attributes that should be hidden for arrays.
		*
		* @var array
		*/
	protected $hidden = [
		'password', 'remember_token',
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
		if( $character->corporation_id != SiggySession::getCorporationId() )
		{
			if( $this->id == Auth::user()->id )
			{
				SiggySession::reloadUserSession();
			}
		}

		return TRUE;
	}

	protected static function boot()
	{
		parent::boot();
		
		static::creating(function ($model)
		{
			$model->password = SiggyUserProvider::hash($model->password);
			$model->remember_token = Str::random(60);
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

	public function updateSSOCharacter(int $characterId, string $token, ?string $refreshToken, $expiration, array $scopes = [])
	{
		$char = $this->ssoCharacters()->where('character_id', $characterId)->first();
		if($char == null)
		{
			throw new ErrorException("Trying to update sso character data when it isnt attached to the user");
		}

		$data = [
			'access_token' => $token,
			'access_token_expiration' => $expiration,
			'refresh_token' => $refreshToken,
			'valid' => 1
		];

		if(!empty($scopes))
		{
			$data = array_merge($data, $scopes);
		}

		$char->fill($data);
		$char->save();
		
		ScribeCommandBus::UpdateSSOCharacter($char->character_owner_hash);
		
		return TRUE;
	}

	public function addSSOCharacter(string $hash, int $characterId, string $token, $expiration, ?string $refreshToken, array $scopes = [])
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

		if(!empty($scopes))
		{
			$insert = array_merge($insert, $scopes);
		}

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
	
	public static function findByResetToken(string $token)
	{
		return self::whereRaw('reset_token = ?', [$token])->first();
	}

	public static function retrieveByRememberToken(int $id, string $token)
	{
		return self::where('id', $id)->where('remember_token', $token)->whereNotNull('remember_token')->first();
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
		$this->password = SiggyUserProvider::hash($newPass);
		$this->remember_token = Str::random(60);
		$this->save();
	}

	public function cycleRememberToken()
	{
		$this->remember_token = Str::random(60);
		$this->save();
	}

	public function getRememberToken(): string
	{
		if(empty($this->remember_token))
		{
			$this->cycleRememberToken();
		}

		return $this->remember_token;
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

	public static function findUserByCharacterOwnerHash(string $hash): ?User
	{
		$user = self::select('users.*')->join('user_ssocharacter','user_ssocharacter.user_id','users.id')->where('user_ssocharacter.character_owner_hash','=',$hash)->first();
		return $user;
	}
}
