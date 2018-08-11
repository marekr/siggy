<?php

namespace Siggy;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class SiggyUserProvider extends EloquentUserProvider
{
	public function __construct($hasher)
	{
		parent::__construct($hasher, \Siggy\User::class);
	}
	
	public static function oldHash(string $str): string
	{
		return hash_hmac("sha256", $str, "876D309BE9025C2F2A2C0532F9BAA0784F23139C31FF9BC515ED3FCFA10580DC");
	}

	public static function newHash(string $str): string
	{
		return password_hash(
				$str, 
				PASSWORD_ARGON2I, [
					'memory_cost' => 32768,
					'time_cost' => 3,
					'threads' => 1,
				]);
	}

	/**
	 * Validate a user against the given credentials.
	 *
	 * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
	 * @param  array  $credentials
	 * @return bool
	 */
	public function validateCredentials(UserContract $user, array $credentials)
	{
		$plain = $credentials['password'];

		return (password_verify(self::oldHash($plain),$user->getAuthPassword()));
	}

}