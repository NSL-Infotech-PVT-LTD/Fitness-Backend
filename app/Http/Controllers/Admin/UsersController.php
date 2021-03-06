<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Role;
use App\User;
use App\OrganiserCoach;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use HasRoles;
use DataTables;

class UsersController extends AdminCommonController {

    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    protected $__rulesforindex = ['name' => 'required', 'email' => 'required'];

    public function index(Request $request) {
        $keyword = $request->get('search');
        $perPage = 15;

        if (!empty($keyword)) {
            $users = User::where('name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")
                            ->latest()->paginate($perPage);
        } else {
            $users = User::latest()->paginate($perPage);
        }

        return view('admin.users.index', compact('users'));
    }

    public function indexByRoleId(Request $request, $role_id) {
//        $keyword = $request->get('search');
//        $perPage = 5;
//
//        $roleusers = \DB::table('role_user')->where('role_id', $role_id)->pluck('user_id');
//        if (!empty($keyword)) {
//            $users = User::wherein('id', $roleusers)->where('firstname', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->latest()->paginate($perPage);
//        } else {
//            $users = User::wherein('id', $roleusers)->latest()->paginate($perPage);
//        }
////        dd($role_id);
//        return view('admin.users.index', compact('users', 'role_id'));


        if ($request->ajax()) {
            $roleusers = \DB::table('role_user')->where('role_id', $role_id)->pluck('user_id');
            $users = User::wherein('id', $roleusers)->latest();
            return Datatables::of($users)
                            ->addIndexColumn()
                            ->addColumn('action', function($item)use($role_id) {
//                                $return = 'return confirm("Confirm delete?")';
                                $return = '';
                             
                                    if ($item->state == '0'):
                                        $return .= "<button class='btn btn-danger btn-sm changeStatus' title='UnBlock'  data-id=" . $item->id . " data-status='UnBlock'>UnBlock / Active</button>";
                                    else:
                                        $return .= "<button class='btn btn-success btn-sm changeStatus' title='Block' data-id=" . $item->id . " data-status='Block' >Block / Inactive</button>";
                                    endif;
                              
                                $return .= " <a href=" . url('/admin/users/' . $item->id) . " title='View User'><button class='btn btn-info btn-sm'><i class='fa fa-eye' aria-hidden='true'></i></button></a>
                                      <a href=" . url('/admin/users/' . $item->id . '/edit') . " title='Edit User'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></button></a>"
                        . " <button class='btn btn-danger btn-sm btnDelete' type='submit' data-remove='" . url('/admin/users/' . $item->id) . "'><i class='fa fa-trash-o' aria-hidden='true'></i></button>";

                                return $return;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('admin.users.index', ['rules' => array_keys($this->__rulesforindex), 'role_id' => $role_id]);
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
                    'name' => 'required',
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
        $orgcoach = \App\OrganiserCoach::whereOrganisationId($id)->get();
//      
        if ($user->hasRole('super admin')):
            return view('admin.users.show.admin', compact('user'));
        else:
            if ($user->hasRole('coach')):
                return view('admin.users.show.coach', compact('user'));
            else:
                if ($user->hasRole('organizer')):
                    return view('admin.users.show.organizer', compact('user','orgcoach'));
                else:
                    return view('admin.users.show.athlete', compact('user'));
                endif;
            endif;
        endif;
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
        $user = User::with('roles')->select('id', 'name', 'email', 'password')->findOrFail($id);
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
                    'name' => 'required',
                    'email' => 'required|string|max:255|email|unique:users,email,' . $id,
                    'roles' => 'required'
                ]
        );
        $data = $request->except('password');
//        if ($request->has('password')) {
//            $data['password'] = bcrypt($request->password);
//        }
        $user = User::findOrFail($id);
//        dd($user->toArray());
        $user->update($data);
        $user->roles()->detach();
        foreach ($request->roles as $role) {
            $user->assignRole($role);
        }
//        dd($user->roles->toArray());
//         $role_id = \DB::table('role_user')->select('role_id')->get();
        $role_id = $user->roles->first()->id;
//        dd($role_id);
        return redirect('admin/users/role/' . $role_id)->with('flash_message', 'User Updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function destroy($id) {
        if (User::destroy($id)) {
            $data = 'Success';
        } else {
            $data = 'Failed';
        }
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function changeStatus(Request $request) {
        $user = User::findOrFail($request->id);
        $user->state = $request->status == 'Block' ? '0' : '1';
        $user->save();
        return response()->json(["success" => true, 'message' => 'User updated!']);
    }

}
