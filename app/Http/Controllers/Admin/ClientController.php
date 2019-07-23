<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use DB;

class ClientController extends AdminCommonController {

    public function index(Request $request) {
        $keyword = $request->get('search');
        $perPage = 5;

        if (!empty($keyword)) {
            $clients = User::where('firstname', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->latest()->paginate($perPage);
        } else {
            $role = \App\Role::where('name', 'client')->first();
            $roleusers = DB::table('role_user')->where('role_id', $role->id)->pluck('user_id');
            $clients = User::wherein('id', $roleusers)->latest()->paginate($perPage);
        }
        return view('admin.client.index', compact('clients'));
    }

    public function create() {
        return view('admin.client.create');
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
        return redirect('/admin/clientdashboard')->with('flash_message', 'Category added!');
    }

    public function show($id) {
        $clients = User::findOrFail($id);
        //return $freelancer; die('grggreer');

        return view('admin.client.show', compact('clients'));
    }

    public function edit($id) {
        $clients = User::findOrFail($id);
        return view('admin.client.edit', compact('clients'));
    }

    public function update(Request $request, $id) {
        $this->validate($request, [
            'firstname' => '',
            'lastname' => '',
            'phone' => ''
        ]);
        $requestData = $request->all();
        $category = User::findOrFail($id);
        $category->update($requestData);

        return redirect('/admin/clientdashboard')->with('flash_message', 'Category updated!');
    }

    public function destroy($id) {
        User::destroy($id);

        return redirect('/admin/clientdashboard')->with('flash_message', 'Category deleted!');
    }

}
