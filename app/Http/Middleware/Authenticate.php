<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */

    public function handle($request, Closure $next, ...$guards)

    {
        // Send Token with All requests
        if ($timwoork_token = $request->cookie('timwoork_token')) {
            $request->headers->set('Authorization', 'Bearer ' . $timwoork_token);
        }

        $this->authenticate($request, $guards);

        return $next($request);
    }


    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('login');
        }
    }
}
