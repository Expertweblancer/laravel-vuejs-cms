<?php

namespace App\Http\Middleware;

use Closure;

class ProhibitedInTestMode
{
    /**
     * Restric action if test mode is turned on.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! config('config.mode')) {
            return response()->json(['error' => trans('general.restricted_test_mode_action')], 422);
        }

        return $next($request);
    }
}
