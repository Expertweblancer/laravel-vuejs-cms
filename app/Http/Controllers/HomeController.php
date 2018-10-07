<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Repositories\TodoRepository;
use App\Repositories\UserRepository;
use App\Repositories\ActivityLogRepository;

class HomeController extends Controller
{
    protected $user;
    protected $activity;
    protected $todo;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $user, ActivityLogRepository $activity, TodoRepository $todo)
    {
        $this->user = $user;
        $this->activity = $activity;
        $this->todo = $todo;
    }

    /**
     * Used to test web route
     */
    public function test()
    {
    }

    /**
     * Used to get Dashboard statistics
     */
    public function dashboard()
    {
        if (\Auth::user()->hasRole(config('system.default_role.admin'))) {
            $users = $this->user->count();
            $today_registered_users = $this->user->countDateBetween(date('Y-m-d'), date('Y-m-d'));
            $weekly_registered_users = $this->user->countDateBetween(date('Y-m-d', strtotime("-7 days")), date('Y-m-d'));
            $monthly_registered_users = $this->user->countDateBetween(date('Y-m-d', strtotime("-1 months")), date('Y-m-d'));
        }

        $activity_logs = $this->activity->getQuery();

        if (! \Auth::user()->hasRole(config('system.default_role.admin'))) {
            $activity_logs->filterByUserId(\Auth::user()->id);
        }

        $activity_logs = $activity_logs->orderBy('created_at', 'desc')->take(10)->get();

        $todos = $this->todo->getQuery()->filterByUserId(\Auth::user()->id)->orderBy('created_at', 'desc')->take(5)->get();

        return $this->success(compact('users', 'today_registered_users', 'weekly_registered_users', 'monthly_registered_users', 'activity_logs', 'todos'));
    }

    /**
     * Used to get demo message for test mode
     */
    public function demoMessage()
    {
        return view('message')->render();
    }
}
