<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HandelXss
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
        // حلب الريكواست هيلبر
        // جلب كل المدخلات
        $input = $request->all();

        array_walk_recursive($input, function (&$input) {
            $input = strip_tags($input, '<p><a><b><i><u><strong><em><ul><ol><li><h1><h2><h3><h4><h5><h6><div><span><img><iframe><embed><object><param><video><audio><source><track><map><area><canvas><svg><table><caption><tbody><thead><tfoot><col><colgroup><tr><td><th><form><fieldset><legend><label><textarea><select><optgroup><option><button><datalist><output><progress><meter><details><summary><menu><menuitem><pre><code><blockquote><cite><q><del><ins><time><mark><small><big><sub><sup><bdo><button>');
        });

        $request->merge($input);
        return $next($request);
    }
}
