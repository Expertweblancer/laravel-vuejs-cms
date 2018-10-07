<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RoleRequest;
use Spatie\Permission\Models\Role;
use App\Repositories\RoleRepository;
use App\Http\Requests\PermissionRequest;
use App\Repositories\ActivityLogRepository;

class RoleController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $module = 'role';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, RoleRepository $repo, ActivityLogRepository $activity)
    {
        $this->request = $request;
        $this->repo = $repo;
        $this->activity = $activity;

        $this->middleware('permission:access-configuration');
    }

    /**
     * Used to get all Roles
     * @get ("/api/role")
     * @return Response
     */
    public function index()
    {
        return $this->ok($this->repo->paginate($this->request->all()));
    }

    /**
     * Used to store Role
     * @post ("/api/role")
     * @param ({
     *      @Parameter("name", type="string", required="true", description="Name of Role"),
     * })
     * @return Response
     */
    public function store(RoleRequest $request)
    {
        $role = Role::create(['name' => strtolower(request('name'))]);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $role->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('role.added')]);
    }

    /**
     * Used to get Role detail
     * @post ("/api/role/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Role"),
     * })
     * @return Response
     */
    public function show($id)
    {
        return $this->ok($this->repo->findOrFail($id));
    }

    /**
     * Used to delete role
     * @delete ("/api/role")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Role to be deleted"),
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $role = $this->repo->deletable($id);
        
        $this->activity->record([
            'module' => $this->module,
            'module_id' => $role->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($role);

        return $this->success(['message' => trans('role.deleted')]);
    }
}
