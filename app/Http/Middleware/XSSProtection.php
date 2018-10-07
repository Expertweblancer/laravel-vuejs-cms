<?php

namespace App\Http\Middleware;

use Closure;

class XSSProtection
{
    /**
     * Used to prevent Cross Site-Scripting
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $except = array();
        foreach (getVar('xss') as $key => $value) {
            if ($request->is($key)) {
                $except = $value;
            }
        }

        $input = $request->except($except);

        array_walk_recursive($input, function (&$input) {
            $input = strip_tags($input);
        });

        $request->merge($input);

        return $next($request);
    }
}
