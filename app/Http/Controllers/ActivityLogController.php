<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\ActivityLogRepository;

class ActivityLogController extends Controller
{
    protected $module = 'activity_log';

    protected $request;
    protected $repo;
    protected $user;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, ActivityLogRepository $repo, UserRepository $user)
    {
        $this->request = $request;
        $this->repo    = $repo;
        $this->user    = $user;

        $this->middleware('permission:access-configuration');
    }

    /**
     * Used to get activity logs
     * @get ("/api/activity-log")
     * @return Response
     */
    public function index()
    {
        $activity_logs = $this->repo->paginate($this->request->all());

        $users = $this->user->getAll();

        return $this->success(compact('users', 'activity_logs'));
    }

    /**
     * Used to delete activity log
     * @delete ("/api/activity-log/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of activity log to be deleted"),
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $activity_log = $this->repo->findOrFail($id);

        $this->repo->record([
            'module'   => $this->module,
            'module_id' => $activity_log->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($activity_log);

        return $this->success(['message' => trans('activity.deleted', ['activity' => trans('activity.activity')])]);
    }
}
