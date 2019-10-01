<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Site extends Model
{
    protected $table = 'sites';

    protected $fillable = [
        'title', 'site_id', 'representative', 'company_id'
    ];

    public function companies()
    {
        return $this->belongsToMany('App\Models\Company');
    }

    public function subSites()
    {
        return $this->belongsToMany('App\Models\SubSite');
    }


    public function getAllCompanies()
    {
        return Company::with('sites')->get();
    }

    public static function getAllSites()
    {
        return Site::all();
    }


    public function getAllSubSites()
    {
        return SubSite::all();
    }

    public function createSite(Request $request)
    {
        return Site::create([
            'title' => $request->title,
            'representative' => $request->representative,
            'company_id' => NULL //$request->company_id,
        ]);
    }

    public function updateSite(Request $request, $id)
    {
        $site = Site::find($id);
        $site->title = $request->title;
        $site->representative = $request->representative;
        $site->company_id = NULL;//$request->company_id;
        $site->save();

        return $site;
    }
}
