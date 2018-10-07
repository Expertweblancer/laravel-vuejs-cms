<?php

namespace App\Http\Middleware;

use Closure;

class FeatureAvailable
{
    /**
     * Used to check a feature is available or not.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  feature  type="string"  required="true"
     * @return mixed
     */
    public function handle($request, Closure $next, $feature)
    {
        if (! config('config.'.$feature)) {
            return response()->json(['error' => 'feature_not_available'], 422);
        }

        return $next($request);
    }
}
