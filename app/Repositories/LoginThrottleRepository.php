<?php
namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class LoginThrottleRepository
{

    /**
     * Validate login throttle.
     *
     * @return null
     */
    public function validate()
    {
        $ip = getRemoteIPAddress();

        if (
            config('config.login_throttle') &&
            \Cache::has($ip) &&
            \Cache::has('last_login_attempt') &&
            \Cache::get($ip) >= config('config.login_throttle_attempt')
        ) {
            $last_login_attempt = \Cache::get('last_login_attempt');

            $throttle_timeout = Carbon::parse($last_login_attempt)->addMinutes(config('config.login_throttle_timeout'))->toDateTimeString();

            if ($throttle_timeout >= Carbon::now()->toDateTimeString()) {
                throw ValidationException::withMessages(['email' => trans('auth.login_throttle_limit_crossed', ['time' => showTime($throttle_timeout)])]);
            } else {
                \Cache::forget($ip);

                \Cache::forget('last_login_attempt');
            }
        }
    }

    /**
     * Update login throttle cache.
     *
     * @return null
     */
    public function update()
    {
        if (! config('config.login_throttle')) {
            return;
        }

        $ip = getRemoteIPAddress();

        if (\Cache::has($ip)) {
            $throttle_attempt = \Cache::get($ip) + 1;
        } else {
            $throttle_attempt = 1;
        }

        cache([$ip => $throttle_attempt], 300);
        cache(['last_login_attempt' => Carbon::now()->toDateTimeString()], 300);
    }

    /**
     * Clear login throttle cache.
     *
     * @return null
     */
    public function clearCache()
    {
        $ip = getRemoteIPAddress();

        \Cache::forget($ip);
        \Cache::forget('last_login_attempt');
    }
}
