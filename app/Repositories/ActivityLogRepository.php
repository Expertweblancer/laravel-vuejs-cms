<?php
namespace App\Repositories;

use App\ActivityLog;
use Illuminate\Validation\ValidationException;

class ActivityLogRepository
{
    protected $activity_log;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(ActivityLog $activity_log)
    {
        $this->activity_log = $activity_log;
    }

    /**
     * Get activity log query
     *
     * @return ActivityLog query
     */
    public function getQuery()
    {
        return $this->activity_log->with('user', 'user.profile');
    }

    /**
     * Find activity log with given id or throw an error.
     *
     * @param integer $id
     * @return ActivityLog
     */
    public function findOrFail($id)
    {
        $activity_log = $this->activity_log->find($id);

        if (! $activity_log) {
            throw ValidationException::withMessages(['message' => trans('activity.could_not_find')]);
        }

        return $activity_log;
    }

    /**
     * Paginate all activity logs using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($params)
    {
        $sort_by               = isset($params['sort_by']) ? $params['sort_by'] : 'created_at';
        $order                 = isset($params['order']) ? $params['order'] : 'desc';
        $page_length           = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');
        $user_id               = isset($params['user_id']) ? $params['user_id'] : null;
        $created_at_start_date = isset($params['created_at_start_date']) ? $params['created_at_start_date'] : null;
        $created_at_end_date   = isset($params['created_at_end_date']) ? $params['created_at_end_date'] : null;

        $query = $this->activity_log->with('user', 'user.profile')->filterByUserId($user_id)->createdAtDateBetween([
            'start_date' => $created_at_start_date,
            'end_date' => $created_at_end_date
        ]);

        return $query->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Record a new activity.
     *
     * @param array $params
     * @return ActivityLog
     */
    public function record($params)
    {
        return $this->activity_log->forceCreate($this->formatParams($params));
    }

    /**
     * Prepare given params for inserting into database.
     *
     * @param array $params
     * @param string $type
     * @return array
     */
    private function formatParams($params)
    {
        $formatted = [
            'user_id'       => isset($params['user_id']) ? $params['user_id'] : \Auth::user()->id,
            'module'        => isset($params['module']) ? $params['module'] : null,
            'module_id'     => isset($params['module_id']) ? $params['module_id'] : null,
            'sub_module'    => isset($params['sub_module']) ? $params['sub_module'] : null,
            'sub_module_id' => isset($params['sub_moduleId']) ? $params['sub_moduleId'] : null,
            'activity'      => isset($params['activity']) ? $params['activity'] : null,
            'ip'            => getClientIp(),
            'user_agent'    => \Request::header('User-Agent')
        ];

        return $formatted;
    }

    /**
     * Delete activity log.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(ActivityLog $activity_log)
    {
        return $activity_log->delete();
    }

    /**
     * Delete multiple activity logs.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->activity_log->whereIn('id', $ids)->delete();
    }
}
