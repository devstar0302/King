<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'orig'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }

    public function links()
    {
        return $this->hasMany('App\Models\Link');
    }

    public function getAllUsers()
    {
        return User::with('role')->get();
    }
    
    public function getAllRoles()
    {
        return Role::all();
    }

    public function createUser(Request $request)
    {
        return User::create([
            'name' => $request->name,
            'role_id' => $request->role,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'orig' => $request->password,
        ]);
    }

    public function getUserById($id)
    {
        return User::with('role')->find($id);
    }

    public function updateUser(Request $request, $id)
    {
        $roles = $this->getAllRoles();
        $roleID = 1;
        foreach ($roles as $role) {
            if ($request->role == __($role->title)) {
                $roleID = $role->id;
            }
        }

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $roleID;
        $user->password = Hash::make($request->pwd);
        $user->orig = $request->pwd;
        $user->save();

        return $user;
    }

    public function destroyUser($id)
    {
        return User::destroy($id);
    }
}
