<?php
namespace App\Repositories;

use App\EmailLog;
use Illuminate\Validation\ValidationException;

class EmailLogRepository
{
    protected $email_log;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(EmailLog $email_log)
    {
        $this->email_log = $email_log;
    }

    /**
     * Find email log with given id or throw an error.
     *
     * @param integer $id
     * @return EmailLog
     */
    public function findOrFail($id)
    {
        $email_log = $this->email_log->find($id);

        if (! $email_log) {
            throw ValidationException::withMessages(['message' => trans('template.could_not_find_email_log')]);
        }

        return $email_log;
    }

    /**
     * Paginate all email logs using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($params)
    {
        $sort_by     = isset($params['sort_by']) ? $params['sort_by'] : 'created_at';
        $order      = isset($params['order']) ? $params['order'] : 'desc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->email_log->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Record a new email log.
     *
     * @param array $params
     * @return EmailLog
     */
    public function record($params)
    {
        return $this->email_log->forceCreate($this->formatParams($params));
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
            'to_address'   => isset($params['to']) ? $params['to'] : null,
            'subject'      => isset($params['subject']) ? $params['subject'] : null,
            'body'         => isset($params['body']) ? $params['body'] : null,
            'from_address' => config('mail.from.address'),
            'module'       => isset($params['module']) ? $params['module'] : null,
            'module_id'    => isset($params['module_id']) ? $params['module_id'] : null
        ];

        return $formatted;
    }

    /**
     * Delete email log.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(EmailLog $email_log)
    {
        return $email_log->delete();
    }

    /**
     * Delete multiple email logs.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->email_log->whereIn('id', $ids)->delete();
    }
}
