<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Localization
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
        // جلب اللغة
        $local = ($request->hasHeader('X-localization')) ? $request->header('X-localization') : 'ar';
        // وضع لغة المختارة من قبل المستخدم
        app()->setLocale($local);

        return $next($request);
    }
}
