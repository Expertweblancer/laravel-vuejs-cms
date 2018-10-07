<?php

namespace App\Listeners;

use JWTAuth;
use App\Events\UserLogin;
use App\Repositories\AuthRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserLoginListener
{
    protected $auth;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(AuthRepository $auth)
    {
        $this->auth = $auth;
    }

    /**
     * To check logged in user has profile & user preferences associated with it
     *
     * @param  UserLogin  $event
     * @return void
     */
    public function handle(UserLogin $event)
    {
        $this->auth->checkProfileAndPreference($event->user);
    }
}
