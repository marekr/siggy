<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Dingo\Api\Routing\Route;
use Dingo\Api\Auth\Provider\Authorization;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

use Carbon\Carbon;
use Siggy\ApiKey;

class CustomApiAuthenticationProvider extends Authorization
{
	public const MAX_AUTH_TIME = 5;
    public function authenticate(Request $request, Route $route)
    {
        $this->validateAuthorizationHeader($request);

		return $this->validateSignature($request, $route);
		
        // If the authorization header passed validation we can continue to authenticate.
        // If authentication then fails we must throw the UnauthorizedHttpException.
    }

	public function validateSignature(Request $request, Route $route)
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
		if (count($split) != 2)
		{
			throw new UnauthorizedHttpException('HMAC', 'Invalid signature');
		}

		$keyId = $split[0];
		$secretHash = base64_decode($split[1]);

		$timestamp = $request->header('x-siggy-date', '');
		if(empty($timestamp))
		{
			$timestamp = $request->headers('Date');
		}

		// Validate timestamp.
		if( Carbon::parse($timestamp)->diffInMinutes(Carbon::now()) > self::MAX_AUTH_TIME) {
			throw new UnauthorizedHttpException('HMAC', 'Invalid signature');
		}

		$apiKey = ApiKey::find($keyId);

		if($apiKey == null) {
			throw new UnauthorizedHttpException('HMAC', 'Invalid signature');
		}

		$contentType = '';
		if($request->method() != "GET")
		{
			$contentType = $request->getContentType();
		}

		$stringToSign = $request->method() . "\n".
						$request->path() . "\n".
						$timestamp . "\n".
						$contentType . "\n".
						base64_encode(hash('sha256', $request->getContent(), true));
						
		$checkHash = hash_hmac('sha256', $stringToSign, $apiKey->secret, true);

		if ($secretHash !== $checkHash) {
			throw new UnauthorizedHttpException('HMAC', 'Invalid signature');
		}

		$this->validateAnyRouteScopes($apiKey, $route);

		return $apiKey;
	}

	protected function validateAnyRouteScopes(ApiKey $token, Route $route)
	{
		$scopes = $route->scopes();
		if (empty($scopes)) {
			return true;
		}

		foreach ($scopes as $scope) {
			if ($token->can($scope)) {
				return true;
			}
		}

		throw new UnauthorizedHttpException('Api key does not have the required scope.');
	}

	public function getAuthorizationMethod()
	{
		return strtolower('siggy-HMAC-SHA256');
	}
}