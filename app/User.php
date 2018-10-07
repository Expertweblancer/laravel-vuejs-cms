<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','activation_token'
    ];

    public function profile()
    {
        return $this->hasOne('App\Profile');
    }

    public function userPreference()
    {
        return $this->hasOne('App\UserPreference');
    }

    public function getNameAttribute()
    {
        return $this->Profile->first_name.' '.$this->Profile->last_name;
    }

    public function getNameWithEmailAttribute()
    {
        return $this->Profile->first_name.' '.$this->Profile->last_name.' ('.$this->email.')';
    }

    public function scopeFilterByEmail($q, $email = null)
    {
        if (! $email) {
            return $q;
        }

        return $q->where('email', 'like', '%'.$email.'%');
    }

    public function scopeFilterByName($q, $name = null)
    {
        if (! $name) {
            return $q;
        }

        return $q->whereHas('profile', function ($q1) use ($name) {
            $q1->where(function($q2) use($name) {
                $q2->where('first_name', 'like', '%'.$name.'%')->orWhere('last_name', 'like', '%'.$name.'%');
            });
        });
    }

    public function scopeFilterByRoleId($q, $role_id = null)
    {
        if (! $role_id) {
            return $q;
        }

        return $q->whereHas('roles', function ($q) use ($role_id) {
            $q->where('role_id', '=', $role_id);
        });
    }

    public function scopeFilterByStatus($q, $status = null)
    {
        if (! $status) {
            return $q;
        }

        return $q->where('status', '=', $status);
    }

    public function scopeCreatedAtDateBetween($q, $dates)
    {
        if ((! $dates['start_date'] || ! $dates['end_date']) && $dates['start_date'] <= $dates['end_date']) {
            return $q;
        }

        return $q->where('created_at', '>=', getStartOfDate($dates['start_date']))->where('created_at', '<=', getEndOfDate($dates['end_date']));
    }
}
