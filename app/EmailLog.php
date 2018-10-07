<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = [
                            'module',
                            'module_id',
                            'to_address',
                            'from_address',
                            'subject',
                            'body'
                        ];
    protected $primaryKey = 'id';
    protected $table = 'email_logs';
    
    public function scopeCreatedAtDateBetween($q, $dates)
    {
        if ((! $dates['start_date'] || ! $dates['end_date']) && $dates['start_date'] <= $dates['end_date']) {
            return $q;
        }

        return $q->where('created_at', '>=', getStartOfDate($dates['start_date']))->where('created_at', '<=', getEndOfDate($dates['end_date']));
    }
}
