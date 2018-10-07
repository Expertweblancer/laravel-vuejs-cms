<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\EmailLogRepository;
use App\Repositories\ActivityLogRepository;

class EmailLogController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;

    protected $module = 'email_log';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, EmailLogRepository $repo, ActivityLogRepository $activity)
    {
        $this->request  = $request;
        $this->repo     = $repo;
        $this->activity = $activity;

        $this->middleware('permission:access-configuration');
        $this->middleware('feature.available:email_log');
    }

    /**
     * Used to get email logs
     * @get ("/api/email-log")
     * @return Response
     */
    public function index()
    {
        return $this->ok($this->repo->paginate($this->request->all()));
    }

    /**
     * Used to get email log detail
     * @get ("/api/email-log/{id}")
     * @return Response
     */
    public function show($id)
    {
        return $this->ok($this->repo->findOrFail($id));
    }

    /**
     * Used to delete email log
     * @delete ("/api/email-log/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of email log to be deleted"),
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $email_log = $this->repo->findOrFail($id);

        $this->activity->record([
            'module'   => $this->module,
            'module_id' => $email_log->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($email_log);

        return $this->success(['message' => trans('mail.email_log_deleted')]);
    }
}
