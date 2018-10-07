<?php

namespace App\Http\Controllers;

use JWTAuth;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Repositories\AuthRepository;
use App\Repositories\UserRepository;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\RegisterRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\ResetPasswordRequest;
use App\Repositories\ActivityLogRepository;
use App\Http\Requests\ChangePasswordRequest;

class AuthController extends Controller
{
    protected $request;
    protected $repo;
    protected $user;
    protected $activity;
    protected $module = 'user';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, AuthRepository $repo, UserRepository $user, ActivityLogRepository $activity)
    {
        $this->request = $request;
        $this->repo = $repo;
        $this->user = $user;
        $this->activity = $activity;
        
        $this->middleware('prohibited.test.mode')->only('changePassword');
    }

    /**
     * Used to authenticate user
     * @post ("/api/auth/login")
     * @param ({
     *      @Parameter("email", type="email", required="true", description="Email of User"),
     *      @Parameter("password", type="password", required="true", description="Password of User"),
     * })
     * @return authentication token
     */
    public function authenticate(LoginRequest $request)
    {
        $auth = $this->repo->auth($this->request->all());

        $auth_user       = $auth['user'];
        $token           = $auth['token'];
        $two_factor_code = $auth['two_factor_code'];

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $auth_user->id,
            'user_id'   => $auth_user->id,
            'activity'  => 'logged_in'
        ]);
        \Cache::put('locale',$auth_user->UserPreference->locale,config('jwt.ttl'));
        \Cache::put('direction',$auth_user->UserPreference->direction,config('jwt.ttl'));

        $reload = (config('app.locale') != cache('locale') || config('config.direction') != cache('direction')) ? 1 : 0;

        return $this->success([
            'message'         => trans('auth.logged_in'),
            'token'           => $token,
            'user'            => $auth_user,
            'two_factor_code' => $two_factor_code,
            'reload'          => $reload
        ]);
    }

    /**
     * Used to check user authenticated or not
     * @post ("/api/auth/check")
     * @return Response
     */
    public function check()
    {
        return $this->success($this->repo->check());
    }

    /**
     * Used to logout user
     * @post ("/api/auth/logout")
     * @return Response
     */
    public function logout()
    {
        $auth_user = \Auth::user();

        try {
            $token = JWTAuth::getToken();
        } catch (JWTException $e) {
            return $this->error($e->getMessage());
        }

        \Cache::forget('direction');
        \Cache::forget('locale');
        
        JWTAuth::invalidate($token);

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $auth_user->id,
            'user_id'   => $auth_user->id,
            'activity'  => 'logged_out'
        ]);

        return $this->success(['message' => trans('auth.logged_out')]);
    }

    /**
     * Used to create user
     * @post ("/api/auth/register")
     * @param ({
     *      @Parameter("first_name", type="text", required="true", description="First Name of User"),
     *      @Parameter("last_name", type="text", required="true", description="Last Name of User"),
     *      @Parameter("email", type="email", required="true", description="Email of User"),
     *      @Parameter("password", type="password", required="true", description="Password of User"),
     *      @Parameter("password_confirmation", type="password", required="true", description="Confirm Password of User"),
     *      @Parameter("tnc", type="checkbox", required="optional", description="Accept Terms & Conditions"),
     * })
     * @return Response
     */
    public function register(RegisterRequest $request)
    {
        $this->repo->validateRegistrationStatus();

        $new_user = $this->user->create($this->request->all(), 1);

        return $this->success(['message' => trans('auth.account_created')]);
    }

    /**
     * Used to activate new user
     * @get ("/api/auth/activate/{token}")
     * @param ({
     *      @Parameter("token", type="string", required="true", description="Activation Token of User"),
     * })
     * @return Response
     */
    public function activate($activation_token)
    {
        $this->repo->activate($activation_token);

        return $this->success(['message' => trans('auth.account_activated')]);
    }

    /**
     * Used to request password reset token for user
     * @post ("/api/auth/password")
     * @param ({
     *      @Parameter("email", type="email", required="true", description="Registered Email of User"),
     * })
     * @return Response
     */
    public function password(PasswordRequest $request)
    {
        $this->repo->password($this->request->all());

        return $this->success(['message' => trans('passwords.sent')]);
    }

    /**
     * Used to validate user password
     * @post ("/api/auth/validate-password-reset")
     * @param ({
     *      @Parameter("token", type="string", required="true", description="Reset Password Token"),
     * })
     * @return Response
     */
    public function validatePasswordReset()
    {
        $this->repo->validateResetPasswordToken(request('token'));

        return $this->success(['message' => '']);
    }

    /**
     * Used to reset user password
     * @post ("/api/auth/reset")
     * @param ({
     *      @Parameter("token", type="string", required="true", description="Reset Password Token"),
     *      @Parameter("email", type="email", required="true", description="Email of User"),
     *      @Parameter("password", type="password", required="true", description="New Password of User"),
     *      @Parameter("password_confirmation", type="password", required="true", description="New Confirm Password of User"),
     * })
     * @return Response
     */
    public function reset(ResetPasswordRequest $request)
    {
        $this->repo->reset($this->request->all());

        return $this->success(['message' => trans('passwords.reset')]);
    }

    /**
     * Used to change user password
     * @post ("/api/change-password")
     * @param ({
     *      @Parameter("current_password", type="password", required="true", description="Current Password of User"),
     *      @Parameter("new_password", type="password", required="true", description="New Password of User"),
     *      @Parameter("new_password_confirmation", type="password", required="true", description="New Confirm Password of User"),
     * })
     * @return Response
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $this->repo->validateCurrentPassword(request('current_password'));

        $this->repo->resetPassword(request('new_password'));

        $this->activity->record([
            'module'     => $this->module,
            'module_id'  => \Auth::user()->id,
            'sub_module' => 'password',
            'activity'   => 'resetted'
        ]);

        return $this->success(['message' => trans('passwords.change')]);
    }

    /**
     * Used to verify password during Screen Lock
     * @post ("/api/auth/lock")
     * @param ({
     *      @Parameter("password", type="password", required="true", description="Password of User"),
     * })
     * @return Response
     */
    public function lock(LoginRequest $request)
    {
        $this->repo->validateCurrentPassword(request('password'));

        $this->activity->record([
            'module'     => $this->module,
            'module_id'  => \Auth::user()->id,
            'sub_module' => 'screen',
            'activity'   => 'unlocked'
        ]);

        return $this->success(['message' => trans('auth.lock_screen_verified')]);
    }
}
