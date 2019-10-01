<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Risk extends Model
{
    protected $table = 'risks';
    protected $fillable = [
        'malfunction_id', 'level'
    ];
}
