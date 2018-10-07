<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'user_preferences';

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
