<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redirect;

class Malfunction extends Model
{
//    protected $guarded = [];
    protected $fillable = ['data', 'user_id', 'created_at', 'updated_at'];

    public function getNameCodeAttribute()
    {
        $name = 'A'.str_repeat('0', 6 - strlen( (string)$this->id) ).$this->id;
        return $name;
    }

    public function getDataAttribute($value)
    {
        return json_decode($value, true);
    }
}
