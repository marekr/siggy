<?php

namespace Siggy;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class SiggyUserProvider extends EloquentUserProvider
{
	private static $hashKey = "876D309BE9025C2F2A2C0532F9BAA0784F23139C31FF9BC515ED3FCFA10580DC";

	public function __construct($hasher)
	{
		parent::__construct($hasher, '\User');
	}
	
	public static function hash($str = '')
	{
		return hash_hmac("sha256", $str, self::$hashKey);
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

        return (self::hash($plain) === $user->getAuthPassword());
    }

}