<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Event;
use App\Session;
use App\Space;
use Carbon\Carbon;
use DB;

class AdminController extends AdminCommonController {

    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index() {
        $users = [];
        foreach (\App\Role::all() as $role):
            if ($role->name == 'super admin')
                continue;
            $users[$role->name]['role_id'] = $role->id;
            $users[$role->name]['count'] = User::wherein('id', DB::table('role_user')->where('role_id', $role->id)->pluck('user_id'))->get()->count();
        endforeach;
        $events = Event::all();
        $session = Session::all();
        $spaces = Space::all();
        return view('admin.dashboard', compact('users', 'events', 'session','spaces'));
    }

    public function display(Request $request) {
        $keyword = $request->get('search');
        $perPage = 5;

        if (!empty($keyword)) {
            $getfreelancers = User::where('firstname', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->latest()->paginate($perPage);
        } else {
            $role = \App\Role::where('name', 'freelancer')->first();
            $roleusers = DB::table('role_user')->where('role_id', $role->id)->pluck('user_id');
            $getfreelancers = User::wherein('id', $roleusers)->latest()->paginate($perPage);
        }
        return view('admin.freelancer.index', compact('getfreelancers'));
    }

    public function createform() {
        return view('admin.freelancer.create');
    }

    public function store(Request $request) {
        $this->validate($request, [
            'firstname' => 'required',
            'lastname' => 'required',
            'phone' => 'required'
        ]);
        $requestData = $request->all();
        $category = User::create($requestData);
        //$role = \App\Role::first();
        //$subcategories = DB::table('role_user')->insert(['' => $category->id]);
        return redirect('admin/display')->with('flash_message', 'Category added!');
    }

    public function show($id) {
        $freelancer = User::findOrFail($id);
        //return $freelancer; die('grggreer');

        return view('admin.freelancer.show', compact('freelancer'));
    }

    public function edit($id) {
        $category = User::findOrFail($id);

        return view('admin.freelancer.edit', compact('category'));
    }

    public function update(Request $request, $id) {
        $this->validate($request, [
            'firstname' => 'required',
            'lastname' => 'required',
            'phone' => 'required'
        ]);
        $requestData = $request->all();
        $category = User::findOrFail($id);
        $category->update($requestData);

        return redirect('admin/display')->with('flash_message', 'Category updated!');
    }

    public function destroy($id) {
        User::destroy($id);

        return redirect('admin/display')->with('flash_message', 'Category deleted!');
    }

}
