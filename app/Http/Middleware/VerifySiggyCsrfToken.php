<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Cookie;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

use App\Facades\Auth;

class VerifySiggyCsrfToken extends BaseVerifier
{
	/**
	 * Determine if the session and input CSRF tokens match.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return bool
	 */
	protected function tokensMatch($request)
	{
        $token = $this->getTokenFromRequest($request);
		
        return is_string(Auth::session()->csrf_token) &&
               is_string($token) &&
               hash_equals(Auth::session()->csrf_token, $token);
	}

    /**
     * Add the CSRF token to the response cookies.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function addCookieToResponse($request, $response)
    {
        $config = config('session');
        $response->headers->setCookie(
            new Cookie(
                'XSRF-TOKEN', Auth::session()->csrf_token, Carbon::now()->getTimestamp() + 60 * $config['lifetime'],
                $config['path'], $config['domain'], $config['secure'], false
            )
        );
        return $response;
    }
}