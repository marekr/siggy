<?php

namespace App\Http\Middleware;

use Closure;

class NoCache
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $response->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->header('pragma', 'no-cache');
        $response->header('expires', 'Mon, 28 Feb 1996 01:00:00 GMT');

        return $response;
    }
}