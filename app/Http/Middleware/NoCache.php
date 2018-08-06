<?php

namespace App\Http\Middleware;

use Closure;

class NoCache
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $response->header('Cache-Control', 'no-cache, no-store, must-revalidate');

        return $response;
    }
}