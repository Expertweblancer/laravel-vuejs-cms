<?php
namespace App\Repositories;

use App\Todo;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class TodoRepository
{
    private $todo;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(Todo $todo)
    {
        $this->todo = $todo;
    }

    /**
     * Get todo query
     *
     * @return Todo query
     */

    public function getQuery()
    {
        return $this->todo;
    }

    /**
     * Find todo with given id or throw an error.
     *
     * @param integer $id
     * @return Todo
     */

    public function findOrFail($id)
    {
        $todo = $this->todo->find($id);

        if (! $todo) {
            throw ValidationException::withMessages(['message' => trans('todo.could_not_find')]);
        }

        return $todo;
    }

    /**
     * Paginate all todos using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */

    public function paginate($params)
    {
        $sort_by     = isset($params['sort_by']) ? $params['sort_by'] : 'created_at';
        $order      = isset($params['order']) ? $params['order'] : 'desc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');
        $keyword     = isset($params['keyword']) ? $params['keyword'] : null;
        $status     = isset($params['status']) ? $params['status'] : 0;
        $start_date = isset($params['start_date']) ? $params['start_date'] : null;
        $end_date   = isset($params['end_date']) ? $params['end_date'] : null;

        $query = $this->todo->filterByUserId(\Auth::user()->id)->filterByTitleOrDescription($keyword)->filterCompletedTodo($status)->dateBetween([
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);

        return $query->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Create a new todo.
     *
     * @param array $params
     * @return Todo
     */
    public function create($params)
    {
        return $this->todo->forceCreate($this->formatParams($params));
    }

    /**
     * Prepare given params for inserting into database.
     *
     * @param array $params
     * @param string $type
     * @return array
     */
    private function formatParams($params, $action = 'create')
    {
        $formatted = [
            'title'       => isset($params['title']) ? $params['title'] : null,
            'description' => isset($params['description']) ? $params['description'] : null,
            'date'        => isset($params['date']) ? $params['date'] : null
        ];

        if ($action === 'create') {
            $formatted['user_id'] = \Auth::user()->id;
        }

        return $formatted;
    }

    /**
     * Update given todo.
     *
     * @param Todo $todo
     * @param array $params
     *
     * @return Todo
     */
    public function update(Todo $todo, $params)
    {
        $todo->forceFill($this->formatParams($params, 'update'))->save();

        return $todo;
    }

    /**
     * Delete todo.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(Todo $todo)
    {
        return $todo->delete();
    }

    /**
     * Delete multiple todos.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->todo->whereIn('id', $ids)->delete();
    }

    /**
     * Toggle given todo status.
     *
     * @param Todo $todo
     * @param array $params
     *
     * @return Todo
     */
    public function toggle(Todo $todo)
    {
        $todo->forceFill([
            'completed_at' => ( $todo->status <= 0 ) ? Carbon::now() : null,
            'status'       => ( $todo->status <= 0 ) ? 1 : 0
        ])->save();

        return $todo;
    }
}
