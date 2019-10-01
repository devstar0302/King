<?php

namespace App\Http\Controllers;

use App\Models\SubSite;
use Illuminate\Http\Request;

class SubSiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $site = SubSite::create([
            'title' => $request->title,
            'site_id' => $request->site_id,
            'representative' => $request->representative
        ]);

        return response()->json($site);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $site = SubSite::find($id);
        $site->title = $request->title;
        $site->site_id = $request->site_id;
        $site->representative = $request->representative;
        $site->save();

        return response()->json($site);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json(SubSite::destroy($id));
    }

    public function getSitesById($id)
    {
        $sites = SubSite::where('site_id', $id)->get();

        return response()->json($sites);
    }
}
