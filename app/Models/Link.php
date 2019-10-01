<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Link extends Model
{
//    protected $connection = 'mysql2';
    protected $table = 'links';
    protected $fillable = [
        'user_id', 'company_id', 'site_id', 'sub_site_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    public function site()
    {
        return $this->belongsTo('App\Models\Site');
    }

    public function subSite()
    {
        return $this->belongsTo('App\Models\SubSite');
    }
}
