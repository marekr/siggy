<?php defined('SYSPATH') or die('No direct script access.');

class RestUser extends Kohana_RestUser {

	/**
	 * This function validates the hashed signature.
	 * Check out the implementation of get_auth() to understand
	 * how a valid hashed signature must be generated.
	 */
	protected function _auth_hash($hash)
	{
		// When the source is a header, it's expected that it'll begin
		// with "Basic ", so let's remove it.
		$prefix = 'siggy-HMAC-SHA256 Credential=';
		if (substr($hash, 0, strlen($prefix)) == $prefix)
		{
			$hash = substr($hash, strlen($prefix));
		}
		else
		{
			throw $this->_altered_401_exception('Invalid '. self::AUTH_KEY_HASH .' value');
		}

		$split = explode(':', $hash);
		if (count($split) != 3)
		{
			throw $this->_altered_401_exception('Invalid '. self::AUTH_KEY_HASH .' value');
		}

		$this->_api_key = $split[0];
		//$this->request->method()
		$timestamp = $split[1];
		$secret_hash = base64_decode($split[2]);

		// Validate timestamp.
		if (time() > ($timestamp + (60 * self::MAX_AUTH_TIME))) {
			throw $this->_altered_401_exception('Invalid '. self::AUTH_KEY_HASH .' value');
		}

		// We load the user now, so that we can validate the hashed timestamp with the secret key.
		$this->_load();


		$stringToSign = $this->_verb . "\n".
						$timestamp;

		$checkHash = hash_hmac('sha256', $stringToSign, $this->_secret_key, true);

		if (!$this->_secret_key || $secret_hash !== $checkHash) {
			throw $this->_altered_401_exception('Invalid '. self::AUTH_KEY_HASH .' value');
		}
	}

	/**
	 * A mock loading of a user object.
	 */
	protected function _find()
	{
		$api = DB::query(Database::SELECT, 'SELECT * FROM siggyapikeys WHERE keyID = :keyID')->param(':keyID', $this->_api_key )->execute()->current();


		$this->_id = $api['groupID'];
		$this->_secret_key = $api['keyCode'];
		/*
		switch ($this->_api_key)
		{
			case 'alon':
				$this->_id = 1000;
				$this->_secret_key = 'abc';
				$this->_roles = array('developer', 'manager');
				break;

			case 'adi':
				$this->_id = 2000;
				$this->_secret_key = 'def';
				$this->_roles = array('developer');
				break;

			default:
				break;
		}*/
	}

} // END
