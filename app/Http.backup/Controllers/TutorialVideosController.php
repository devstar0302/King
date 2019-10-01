<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Tutorial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TutorialVideosController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $tutorials = Tutorial::all();

        foreach ($tutorials as $tutorial) {
            $roles = [];
            foreach (DB::table('tutorials_roles')->where('tutorial_id', $tutorial->id)->get() as $data) {
                $roles[] = __(Role::find($data->role_id)->title);
            }

            $tutorial['user_types'] = $roles;

            $tutorial['is_editing'] = false;
        }

        $is_admin = $this->getUserRole() == 'admin';

        $view_data = compact('tutorials', 'is_admin');

        $this->breadcrumbs[] = array('url' => action('TutorialVideosController@index'), 'label'=> __('Tutorial videos'));

        return view('tutorial-videos.index', array_merge($view_data, ['breadcrumbs' => $this->breadcrumbs]));
    }

    protected function getUser()
    {
        $user_id = auth()->user()->id;
        return DB::select('select users.id, roles.title, `name` from users left join roles on users.role_id=roles.id where users.id=' . $user_id);
    }

    protected function getUserRole()
    {
        $user = $this->getUser();
        $user_role = '';
        if (count($user) && isset($user[0]->title)) {
            $user_role = strtolower($user[0]->title);
        }
        return $user_role;
    }

    public function destroy($id)
    {
        Tutorial::find($id)->delete();
    }
}
