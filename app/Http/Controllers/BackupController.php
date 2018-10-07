<?php

namespace App\Http\Controllers;

use JWTAuth;
use Illuminate\Http\Request;
use App\Repositories\BackupRepository;
use App\Repositories\ActivityLogRepository;

class BackupController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;

    protected $module = 'backup';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, BackupRepository $repo, ActivityLogRepository $activity)
    {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;

        $this->middleware('permission:access-configuration');
        $this->middleware('feature.available:backup');
        $this->middleware('prohibited.test.mode')->only('download');
    }

    /**
     * Used to get all Backups
     * @get ("/api/backup")
     * @return Response
     */
    public function index()
    {
        return $this->ok($this->repo->paginate($this->request->all()));
    }

    /**
     * Used to store Backup
     * @post ("/api/backup")
     * @param ({
     *      @Parameter("deletePrevious", type="checkbox", required="true", description="Delete or not to delete previous backup"),
     * })
     * @return Response
     */
    public function store()
    {
        $backup = $this->repo->generate($this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $backup->id,
            'activity'  => 'generated'
        ]);

        return $this->success(['message' => trans('backup.generated')]);
    }

    /**
     * Used to download Backup
     * @get ("/backup/{id}/download")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of backup to be downloaded"),
     * })
     * @return Response download
     */
    public function download($id)
    {
        $backup = $this->repo->findOrFail($id);

        try {
            \Storage::exists('uploads/backup/'.$backup->file);
        } catch (\Exception $e) {
        }

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $backup->id,
            'activity'  => 'downloaded'
        ]);

        $download_path = storage_path('app/uploads/backup/'.$backup->file);

        return response()->download($download_path);
    }

    /**
     * Used to delete Backup
     * @delete ("/api/backup/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of backup to be deleted"),
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $backup = $this->repo->findOrFail($id);

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $backup->id,
            'activity'  => 'deleted'
        ]);

        if (\Storage::exists('uploads/backup/'.$backup->file)) {
            \Storage::delete('uploads/backup/'.$backup->file);
        }

        $this->repo->delete($backup);

        return $this->success(['message' => trans('backup.deleted')]);
    }
}
