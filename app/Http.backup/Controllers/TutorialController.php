<?php

namespace App\Http\Controllers;

use App\Mail\TutorialShareEmail;
use App\Models\Role;
use App\Models\Tutorial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TutorialController extends Controller
{
    public function index()
    {
        $tutorials = Tutorial::all();

        foreach ($tutorials as $tutorial) {
            $roles = [];
            foreach (DB::table('tutorials_roles')->where('tutorial_id', $tutorial->id)->get() as $data) {
                $roles[] = Role::find($data->role_id)->title;
            }

            $tutorial['user_types'] = $roles;
        }

        return $tutorials;
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $tutorial = Tutorial::create([
            'name'  => $request->name,
            'link'  => $request->link
        ]);

        foreach (explode(',', $request->user_types) as $userType) {
            switch ($userType) {
              case __('Admin'):       $userType = 'Admin'; break;
              case __('Client'):      $userType = 'Client'; break;
              case __('Contractor'):  $userType = 'Contractor'; break;
              case __('Employee'):    $userType = 'Employee'; break;
            }

            $role = Role::where('title', trim($userType))->first();

            if ($role) {
                DB::table('tutorials_roles')->insert([
                    'role_id'       => $role->id,
                    'tutorial_id'   => $tutorial->id
                ]);
            }
        }
    }

    public function show($id)
    {
        return Tutorial::find($id);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $tutorial = Tutorial::find($id);

        $tutorial->update([
          'name'  => $request->name,
          'link'  => $request->link
        ]);

        if (isset($request->user_types)) {
            DB::table('tutorials_roles')->where('tutorial_id', $id)->delete();

            foreach (explode(',', $request->user_types) as $userType) {
                switch ($userType) {
                  case __('Admin'):       $userType = 'Admin'; break;
                  case __('Client'):      $userType = 'Client'; break;
                  case __('Contractor'):  $userType = 'Contractor'; break;
                  case __('Employee'):    $userType = 'Employee'; break;
                }

              $role = Role::where('title', trim($userType))->first();

              if ($role) {
                DB::table('tutorials_roles')->insert([
                  'role_id'       => $role->id,
                  'tutorial_id'   => $tutorial->id
                ]);
              }
            }
        }
    }

    public function destroy($id)
    {
        Tutorial::find($id)->delete();
    }

    public function sendEmail(Request $request)
    {
        Mail::send(new TutorialShareEmail($request->all()));
    }
}
