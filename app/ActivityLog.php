<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
                            'user_id',
                            'module',
                            'sub_module',
                            'user_agent',
                            'module_id',
                            'sub_module_id',
                            'activity',
                            'ip'
                        ];
    protected $primaryKey = 'id';
    protected $table = 'activity_logs';

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function scopeFilterByUserId($q, $user_id = null)
    {
        if (! $user_id) {
            return $q;
        }

        return $q->whereUserId($user_id);
    }

    public function scopeCreatedAtDateBetween($q, $dates)
    {
        if ((! $dates['start_date'] || ! $dates['end_date']) && $dates['start_date'] <= $dates['end_date']) {
            return $q;
        }

        return $q->where('created_at', '>=', getStartOfDate($dates['start_date']))->where('created_at', '<=', getEndOfDate($dates['end_date']));
    }
}
