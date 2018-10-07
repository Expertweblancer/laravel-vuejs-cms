<?php
namespace App\Repositories;

use App\Notifications\TwoFactorSecurity;

class TwoFactorSecurityRepository
{

    /**
     * Set two factor security code.
     *
     * @param User
     * @return two factor code
     */

    public function set($user)
    {
        if (! config('config.two_factor_security')) {
            return;
        }

        $two_factor_code = rand(100000, 999999);

        $user->notify(new TwoFactorSecurity($two_factor_code));

        return $two_factor_code;
    }
}
