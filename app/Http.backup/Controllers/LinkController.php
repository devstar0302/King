<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function store(Request $request)
    {
        $user_id = null;
        if ($request->has('user_id')) {
            if ($request->input('user_id') != 'Name') {
                $user_id = $request->user_id;
            }
        }
        $link = Link::create([
            'user_id' => $user_id,
//            'company_id' => $request->company_id,
            'site_id' => $request->site_id,
            'sub_site_id' => $request->sub_site_id
        ]);

        return response()->json($link);
    }

    public function destroy($id)
    {
        return response()->json(Link::destroy($id));
    }
}
