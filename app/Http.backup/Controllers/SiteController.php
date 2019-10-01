<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;

class SiteController extends Controller
{

    protected $site;

    public function __construct(Site $site)
    {
        $this->site = $site;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sites = $this->site->getAllSites();
        $breadcrumbs_url = url('/') . '/sites';

        return view('site.index', compact('sites', 'breadcrumbs_url'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $subsites = $this->site->getAllSubSites();
        $breadcrumbs_url = url('/') . '/sites';
        return view('site.create', compact('subsites', 'breadcrumbs_url'));
    }

    public function store(Request $request)
    {
        $site = $this->site->createSite($request);

        return response()->json($site);
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        $site = $this->site->updateSite($request, $id);

        return response()->json($site);
    }

    public function destroy($id)
    {
        return response()->json(Site::destroy($id));
    }

    public function getSitesById($id)
    {
        $sites = Site::where('company_id', $id)->get();

        return response()->json($sites);
    }
}
