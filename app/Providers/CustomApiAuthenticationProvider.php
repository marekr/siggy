<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Dingo\Api\Routing\Route;
use Dingo\Api\Auth\Provider\Authorization;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

use Siggy\ApiKey;

class CustomApiAuthenticationProvider extends Authorization
{
	public const MAX_AUTH_TIME = 60*5;
    public function authenticate(Request $request, Route $route)
    {
        $this->validateAuthorizationHeader($request);

		return $this->validateSignature($request);
		
        // If the authorization header passed validation we can continue to authenticate.
        // If authentication then fails we must throw the UnauthorizedHttpException.
    }

	public function validateSignature(Request $request)
	{
		$prefix = 'siggy-HMAC-SHA256 Credential=';
		$authorization = $request->header('authorization');

		if (substr($authorization, 0, strlen($prefix)) == $prefix)
		{
			$authorization = substr($authorization, strlen($prefix));
		}
		else
		{
			throw new UnauthorizedHttpException('HMAC', 'Invalid signature');
		}

		$split = explode(':', $authorization);
		if (count($split) != 3)
		{
			throw new UnauthorizedHttpException('HMAC', 'Invalid signature');
		}

		$keyId = $split[0];
		$timestamp = $split[1];
		$secretHash = base64_decode($split[2]);

		// Validate timestamp.
		if (time() > ($timestamp + (60 * self::MAX_AUTH_TIME))) {
			throw new UnauthorizedHttpException('HMAC', 'Invalid signature');
		}

		$apiKey = ApiKey::find($keyId);

		if($apiKey == null) {
			throw new UnauthorizedHttpException('HMAC', 'Invalid signature');
		}

		$stringToSign = $request->method() . "\n".
						$request->path() . "\n".
						$timestamp . "\n".
						base64_encode($request->getContent());
		$checkHash = hash_hmac('sha256', $stringToSign, $apiKey->secret, true);

		if ($secretHash !== $checkHash) {
			throw new UnauthorizedHttpException('HMAC', 'Invalid signature');
		}

		return $apiKey;
	}

    public function getAuthorizationMethod()
    {
        return strtolower('siggy-HMAC-SHA256');
    }
}