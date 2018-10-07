<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Repositories\RoleRepository;
use App\Http\Requests\PermissionRequest;
use Spatie\Permission\Models\Permission;
use App\Repositories\PermissionRepository;
use App\Repositories\ActivityLogRepository;

class PermissionController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $role;

    protected $module = 'permission';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, PermissionRepository $repo, ActivityLogRepository $activity, RoleRepository $role)
    {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;

        $this->middleware('permission:access-configuration');
        $this->role = $role;
    }

    /**
     * Used to get all Permissions
     * @get ("/api/permission")
     * @return Response
     */
    public function index()
    {
        return $this->ok($this->repo->paginate($this->request->all()));
    }

    /**
     * Used to fetch Pre-Requisites during Permission Assign
     * @get ("/api/permission/assign/pre-requisite")
     * @return Response
     */
    public function preRequisite()
    {
        $roles = $this->role->getAll();

        $permissions = $this->repo->getAll();

        $assigned_permissions = \DB::table('role_has_permissions')->get();

        $data = array();
        foreach ($permissions as $permission) {
            foreach ($roles as $role) {
                $data[$role->id][$permission->id] = $assigned_permissions->where('role_id', $role->id)->where('permission_id', $permission->id)->count() ? true : false;
            }
        }

        return $this->success(compact('roles', 'permissions', 'data'));
    }

    /**
     * Used to assign Permission
     * @post ("/api/permission/assign")
     * @param ({
     *      @Parameter("data", type="array", required="true", description="Array of Permissions"),
     * })
     * @return Response
     */
    public function assignPermission()
    {
        $input = request('data');
        $roles = $this->role->list();

        foreach ($input as $role_id => $data) {
            $role = Role::findByName($roles[$role_id]);
            $permissions = array();
            foreach ($data as $permission_id => $value) {
                if ($value) {
                    $permissions[] = $permission_id;
                }
            }
            if ($role->name === $this->role->findByName(config('system.default_role.admin'))->name) {
                $role->syncPermissions($this->repo->listByName());
            } else {
                $role->syncPermissions($this->repo->listByName($permissions));
            }
        }

        $this->activity->record([
            'module' => $this->module,
            'activity' => 'assigned'
        ]);

        return $this->success(['message' => trans('permission.assigned')]);
    }

    /**
     * Used to store Permission
     * @post ("/api/permission")
     * @param ({
     *      @Parameter("name", type="string", required="true", description="Name of Permission"),
     * })
     * @return Response
     */
    public function store(PermissionRequest $request)
    {
        $permission = Permission::create(['name' => strtolower(request('name'))]);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $permission->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('permission.added')]);
    }

    /**
     * Used to get Permission detail
     * @get ("/api/permission/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Permission"),
     * })
     * @return Response
     */
    public function show($id)
    {
        return $this->ok($this->repo->findOrFail($id));
    }

    /**
     * Used to delete Permission
     * @delete ("/api/permission")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Permission to be deleted"),
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $permission = $this->repo->findOrFail($id);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $permission->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($permission);

        return $this->success(['message' => trans('permission.deleted')]);
    }
}
