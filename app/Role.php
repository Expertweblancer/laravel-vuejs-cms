<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
                            'name'
                        ];
    protected $primaryKey = 'id';
    protected $table = 'roles';

    public function getDetailAttribute()
    {
        return ucfirst($this->name);
    }

    public function scopeFilterByName($q, $name = null)
    {
        if (! $name) {
            return $q;
        }

        return $q->where('name', 'like', '%'.$name.'%');
    }
}
