<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Company extends Model
{
//    protected $connection = 'mysql2';
    protected $table = 'companies';


    protected $fillable = [
        'title'
    ];

    public static function getAllCompanies()
    {
//        return Company::with('sites')->get();
        return Company::all();
    }

    public static function getAllSites()
    {
        return Site::all();
    }

    public function createCompany(Request $request)
    {
        return Company::create([
            'title' => $request->title
        ]);
    }

    public function destroyCompany($id)
    {
        return Company::destroy($id);
    }

    public function updateCompany(Request $request, $id)
    {
        $company = Company::find($id);
        $company->title = $request->title;
        $company->save();
        return $company;
    }
}
