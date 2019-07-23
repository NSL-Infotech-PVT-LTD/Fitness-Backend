<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use HasRoles;

class UsersController extends AdminController {

    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index(Request $request) {
        $keyword = $request->get('search');
        $perPage = 15;

        if (!empty($keyword)) {
            $users = User::where('firstname', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")
                            ->latest()->paginate($perPage);
        } else {
            $users = User::latest()->paginate($perPage);
        }

        return view('admin.users.index', compact('users'));
    }

    public function indexByRoleId(Request $request, $role_id) {
        $keyword = $request->get('search');
        $perPage = 5;

        $roleusers = \DB::table('role_user')->where('role_id', $role_id)->pluck('user_id');
        if (!empty($keyword)) {
            $users = User::wherein('id', $roleusers)->where('firstname', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->latest()->paginate($perPage);
        } else {
            $users = User::wherein('id', $roleusers)->latest()->paginate($perPage);
        }
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create() {
        $roles = Role::select('id', 'name', 'label')->get();
        $roles = $roles->pluck('label', 'name');
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function store(Request $request) {
        $this->validate(
                $request,
                [
                    'firstname' => 'required',
                    'lastname' => 'required',
                    'email' => 'required|string|max:255|email|unique:users',
                    'password' => 'required',
                ]
        );

        $data = $request->all();
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);
        foreach ($request->roles as $role) {
            $user->assignRole($role);
        }
        return redirect(url()->previous())->with('flash_message', 'user Added');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function show($id) {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function edit($id) {
        $roles = Role::select('id', 'name', 'label')->get();
        $roles = $roles->pluck('label', 'name');
        $user = User::with('roles')->select('id', 'firstname', 'lastname', 'email', 'password')->findOrFail($id);
        $user_roles = [];
        foreach ($user->roles as $role) {
            $user_roles[] = $role->name;
        }

        return view('admin.users.edit', compact('user', 'roles', 'user_roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int      $id
     *
     * @return void
     */
    public function update(Request $request, $id) {
        $this->validate(
                $request,
                [
                    'firstname' => 'required',
                    'lastname' => 'required',
                    'email' => 'required|string|max:255|email|unique:users,email,' . $id,
                    'roles' => 'required'
                ]
        );
        $data = $request->except('password');
        if ($request->has('password')) {
            $data['password'] = bcrypt($request->password);
        }
        $user = User::findOrFail($id);
        $user->update($data);
        $user->roles()->detach();
        foreach ($request->roles as $role) {
            $user->assignRole($role);
        }
        return redirect(url()->previous())->with('flash_message', 'User Updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function destroy($id) {
        User::destroy($id);
        return redirect(url()->previous())->with('flash_message', 'User deleted!');
    }

}
