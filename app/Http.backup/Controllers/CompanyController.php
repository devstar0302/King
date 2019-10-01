<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Link;
use App\Models\Role;
use App\Models\Site;
use App\Models\SubSite;
use App\Models\User;
use Illuminate\Http\Request;

class CompanyController extends Controller
{

    protected $company;
    protected $role;

    public function __construct(Company $company, Role $role)
    {
        $this->company = $company;
        $this->role = $role;
    }

    public function index()
    {
        $companies = $this->company->getAllCompanies();
        $usersType = Role::find([1, 2]);
        $sites = Site::all();
        $links = Link::with('user', 'company', 'site', 'subSite')->get();
        $links = $links->reject(function($link) {
            return !isset($link->user['id']);
        });

        $breadcrumbs_url = url('/') . '/companies';

        $this->breadcrumbs[] = array('url' => action('CompanyController@index'), 'label' => __('Site linking'));

        $view_data = compact('companies', 'usersType', 'sites', 'links', 'breadcrumbs_url');
        $view_with_data = ['breadcrumbs' => $this->breadcrumbs];
        
        return view('company.index', $view_data)->with($view_with_data);
    }

    public function create()
    {
        $sites = $this->company->getAllSites();
        return view('company.create', compact('sites'));
    }

    public function store(Request $request)
    {
        $company = $this->company->createCompany($request);
        return response()->json($company);
    }

    public function show($id)
    {
    }

    public function edit($id)
    {
    }

    public function update(Request $request, $id)
    {
        $company = $this->company->updateCompany($request, $id);
        return response()->json($company);
    }

    public function change(Request $request)
    {
        User::find($request->id)->update(['name' => $request->value]);
        return response()->json(['status' => __('ok')]);
    }

    public function destroy($id)
    {
        $links = Link::query()->where('company_id', '=', $id)->get();
        Link::query()->where('company_id', '=', $id)->delete();
        foreach ($links as $link) {
            SubSite::query()->where('id', '=', $link->sub_site_id)->delete();
            Site::query()->where('id', '=', $link->site_id)->delete();
        }

        $this->company->destroyCompany($id);
        return response()->json(['status' => __('ok')]);
    }

    public function getUserByRoleId($id)
    {
        $users = User::where('role_id', $id)->get();
        return response()->json($users);
    }
}
