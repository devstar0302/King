<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

use Session;

class UserController extends Controller
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function index()
    {
        $users = $this->user->getAllUsers();

        $this->breadcrumbs[] = array('url' => action('UserController@index'), 'label'=> __('Users management'));

        return view('user.index', compact('users'))->with(['breadcrumbs' => $this->breadcrumbs]);
    }

    public function create()
    {
        $roles = $this->user->getAllRoles();

        $this->breadcrumbs[] = array('url' => action('UserController@index'), 'label'=> __('Users management'));
        $this->breadcrumbs[] = array('url' => action('UserController@create'), 'label'=> __('Create'));

        return view('user.create', compact('roles'))->with(['breadcrumbs' => $this->breadcrumbs]);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required',
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ])->validate();

        $user = $this->user->createUser($request);
        return redirect()->route('users.index')->with('status', __('Successfully added'));
    }

    public function show($id)
    {
    }

    public function edit($id)
    {
        $user = $this->user->getUserById($id);
        $roles = $this->user->getAllRoles();

        $this->breadcrumbs[] = array('url' => action('UserController@index'), 'label'=> __('Users management'));
        $this->breadcrumbs[] = array('url' => action('UserController@edit', $id), 'label'=> __('Edit'));

        return view('user.edit', compact('user', 'roles'))->with(['breadcrumbs' => $this->breadcrumbs]);
    }

    public function update(Request $request, $id)
    {
        $this->user->updateUser($request, $id);
        return response()->json(['status'=> __('ok')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->user->destroyUser($id);
        return response()->json(['msg'=> __('deleted')]);
    }

    public function ajaxGenPassword(Request $request) {
        return response()->json(['status' => __('ok'), 'password' => str_random(8)]);
    }
}
