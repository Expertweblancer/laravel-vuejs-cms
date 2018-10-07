<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
                            'name'
                        ];
    protected $primaryKey = 'id';
    protected $table = 'permissions';

    public function scopeFilterByName($q, $name = null)
    {
        if (! $name) {
            return $q;
        }

        return $q->where('name', 'like', '%'.$name.'%');
    }
}
