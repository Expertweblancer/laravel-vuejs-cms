<?php
namespace App;

use Eloquent;

class Configuration extends Eloquent
{
    protected $fillable = [
                            'name',
                            'numeric_value',
                            'text_value',
                        ];
    protected $primaryKey = 'id';
    protected $table = 'config';
    public $timestamps = false;

    public function getValueAttribute()
    {
        return ($this->numeric_value != null) ?  $this->numeric_value : $this->text_value;
    }

    public function getPublicValueAttribute()
    {
        $value = ($this->numeric_value != null) ?  $this->numeric_value : $this->text_value;
        if ($this->is_private && $value) {
            return config('system.hidden_field');
        } else {
            return $value;
        }
    }

    public function scopeFilterByName($q, $name = null)
    {
        if (! $name) {
            return $q;
        }

        return $q->where('name', '=', $name);
    }
}
