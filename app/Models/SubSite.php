<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubSite extends Model
{
    protected $table = 'sub_sites';

    protected $fillable = [
        'title', 'site_id', 'representative'
    ];

    public function sites()
    {
        return $this->belongsToMany('App\Models\Site');
    }

    public static function getAllSubSites()
    {
        return SubSite::all();
    }
}
