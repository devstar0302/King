<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tutorial extends Model
{
    protected $table = 'tutorials';

    protected $fillable = [
        'name',
        'link'
    ];

    public $timestamps = false;
}
