<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $fillable = [];
    protected $hidden = [
        'user_id','is_temp_delete','status','module','module_id'
    ];
    protected $primaryKey = 'id';
    protected $table = 'uploads';

    public function scopeFilterByModule($q, $module)
    {
        if (! $module) {
            return $q;
        }

        return $q->where('module', '=', $module);
    }

    public function scopeFilterByModuleId($q, $module_id)
    {
        if (! $module_id) {
            return $q;
        }

        return $q->where('module_id', '=', $module_id);
    }

    public function scopeFilterByUploadToken($q, $upload_token)
    {
        if (! $upload_token) {
            return $q;
        }

        return $q->where('upload_token', '=', $upload_token);
    }

    public function scopeFilterByIsTempDelete($q, $temp_delete)
    {
        return $q->where('is_temp_delete', '=', $temp_delete);
    }

    public function scopeFilterByStatus($q, $status)
    {
        return $q->where('status', '=', $status);
    }

    public function scopeFilterByUuId($q, $uuid)
    {
        return $q->where('uuid', '=', $uuid);
    }

    public function scopeFilterById($q, $id)
    {
        return $q->where('id', '=', $id);
    }
}
