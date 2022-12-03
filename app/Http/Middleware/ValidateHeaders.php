<?php
namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;

class ValidateHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
    try{
        if ($request->headers->has('X-localization')){
            if(!in_array($request->header('X-localization'), ['ar', 'en', 'fr']))
              $request->headers->remove('X-localization');
        }
        return $next($request);
    }
    catch (Exception $ex){
        echo $ex;
    }
    }
}
