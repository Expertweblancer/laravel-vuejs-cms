<?php

namespace App\Http\Controllers;

use JWTAuth;
use Socialite;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class SocialLoginController extends Controller
{

    /**
     * Used to redirect to provider
     * @get ("/auth/social/{provider}")
     * @param ({
     *      @Parameter("provider", type="string", required="true", description="Name of Provider"),
     * })
     * @redirect to provider
     */
    public function providerRedirect($provider = '')
    {
        if (!config('config.social_login')) {
            return redirect('/');
        }

        if (!in_array($provider, config('system.social_login_providers'))) {
            return redirect('/login')->withErrors('This is not a valid link.');
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Used to get token from callback URL
     * @get ("/auth/{provider}/callback")
     * @param ({
     *      @Parameter("provider", type="string", required="true", description="Name of Provider"),
     * })
     * @redirect to URL '/auth/social'
     */
    public function providerRedirectCallback($provider = '')
    {
        if (!config('config.social_login')) {
            return redirect('/');
        }

        try {
            $user = Socialite::driver($provider)->user();
        } catch (Exception $e) {
            return redirect('/auth/social');
        }

        $user_exists = \App\User::whereEmail($user->email)->first();

        if ($user_exists) {
            $token = JWTAuth::fromUser($user_exists);
            $user_id = $user_exists->id;

            if ($user_exists->status === 'pending_activation') {
                \Cache::put('message', trans('auth.pending_activation'), 1);
            }

            if ($user_exists->status === 'pending_approval') {
                \Cache::put('message', trans('auth.pending_approval'), 1);
            }

            if ($user_exists->status === 'disapproved') {
                \Cache::put('message', trans('auth.not_activated'), 1);
            }

            if ($user_exists->status === 'banned') {
                \Cache::put('message', trans('auth.account_banned'), 1);
            }

            if ($user_exists->status != 'activated') {
                \Cache::put('message', trans('auth.not_activated'), 1);
            }

            if (!$user_exists->hasPermissionTo('enable-login')) {
                \Cache::put('message', trans('auth.login_permission_disabled'), 1);
            }
        } else {
            $new_user = new \App\User;
            $new_user->email = $user->email;
            $new_user->status = 'activated';
            $new_user->activation_token = Str::uuid();
            $new_user->save();
            $new_user->assignRole((\App\User::count()) ? config('system.default_role.user') : config('system.default_role.admin'));
            $name = explode(' ', $user->name);
            $profile = new \App\Profile;
            $new_user->profile()->save($profile);
            $profile->first_name = array_key_exists(0, $name) ? $name[0] : 'John';
            $profile->last_name = array_key_exists(1, $name) ? $name[1] : 'Doe';
            $profile->provider = $provider;
            $profile->provider_unique_id = $user->id;
            $profile->save();
            $user_preference = new \App\UserPreference;
            $new_user->userPreference()->save($user_preference);
            $token = JWTAuth::fromUser($new_user);
            $user_id = $new_user->id;
        }

        \Cache::put('jwt_token', $token, 1);
        return redirect('/auth/social');
    }

    /**
     * Used to get token stored in cache & then delete
     * @post ("/auth/social/token")
     * @return Response
     */
    public function getToken()
    {
        if (!config('config.social_login')) {
            return $this->error(['message' => trans('general.invalid_link')]);
        }

        if (!\Cache::has('jwt_token')) {
            return $this->error(['message' => trans('general.invalid_link')]);
        }

        if (\Cache::has('message')) {
            $message = \Cache::get('message');
            \Cache::forget('message');
            return $this->error(['message' => $message]);
        }

        $token = \Cache::get('jwt_token');

        \Cache::forget('jwt_token');
        return $this->success(['message' => trans('auth.logged_in'),'token' => $token]);
    }
}
