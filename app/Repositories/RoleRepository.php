<?php
namespace App\Repositories;

use App\Role;
use Illuminate\Validation\ValidationException;

class RoleRepository
{
    protected $role;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    /**
     * Get all roles
     *
     * @return Role
     */

    public function getAll()
    {
        return $this->role->all();
    }

    /**
     * List all roles by name & id
     *
     * @return Role
     */

    public function list()
    {
        return $this->role->all()->pluck('name', 'id')->all();
    }

    /**
     * Get role by name
     *
     * @return Role
     */

    public function findByName($name = null)
    {
        return $this->role->filterByName($name)->first();
    }

    /**
     * List (name,id) all roles by name where given name is not included
     *
     * @return Role
     */

    public function listExceptName($names = array())
    {
        return $this->role->whereNotIn('name', $names)->get()->pluck('name', 'id')->all();
    }

    /**
     * List (name) all roles by id
     *
     * @return Role
     */

    public function listNameById($ids = array())
    {
        $ids = is_array($ids) ? $ids : ($ids) ? [$ids] : [];

        return $this->role->whereIn('id', $ids)->get()->pluck('name')->all();
    }

    /**
     * List all names
     *
     * @return Role
     */

    public function listName()
    {
        return $this->role->all()->pluck('name')->all();
    }

    /**
     * Find role with given id or throw an error.
     *
     * @param integer $id
     * @return Role
     */

    public function findOrFail($id)
    {
        $role = $this->role->find($id);

        if (! $role) {
            throw ValidationException::withMessages(['message' => trans('role.could_not_find')]);
        }

        return $role;
    }

    /**
     * Paginate all roles using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */

    public function paginate($params)
    {
        $sort_by     = isset($params['sort_by']) ? $params['sort_by'] : 'created_at';
        $order      = isset($params['order']) ? $params['order'] : 'desc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->role->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Find role & check it can be deleted or not.
     *
     * @param integer $id
     * @return Locale
     */
    public function deletable($id)
    {
        $role = $this->findOrFail($id);

        if (in_array($role->name, config('system.default_role'))) {
            throw ValidationException::withMessages(['message' => trans('role.default_cannot_be_deleted')]);
        }
        
        return $role;
    }

    /**
     * Delete role.
     *
     * @param integer $id
     * @return bool|null
     */

    public function delete(Role $role)
    {
        return $role->delete();
    }

    /**
     * Delete multiple roles.
     *
     * @param array $ids
     * @return bool|null
     */

    public function deleteMultiple($ids = array())
    {
        return $this->role->whereIn('id', $ids)->delete();
    }
}
