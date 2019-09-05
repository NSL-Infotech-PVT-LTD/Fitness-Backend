<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Service;
use Illuminate\Http\Request;

class ServicesController extends Controller {

    public static $_basePath = '/public/uploads/services/';
    public static $_urlPath = '/uploads/services/';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request) {
        $keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $services = Service::where('name', 'LIKE', "%$keyword%")
                            ->orWhere('price', 'LIKE', "%$keyword%")
                            ->orWhere('image', 'LIKE', "%$keyword%")
                            ->orWhere('description', 'LIKE', "%$keyword%")
                            ->latest()->paginate($perPage);
        } else {
            $services = Service::latest()->paginate($perPage);
        }

        return view('admin.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create() {
        return view('admin.services.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'image' => 'required',
            'description' => 'required'
        ]);
        $requestData = $request->all();
        $imageName = uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
        $request->file('image')->move(base_path() . '/public/uploads/services/', $imageName);
        $requestData['image'] = $imageName;
        Service::create($requestData);

        return redirect('admin/services')->with('flash_message', 'Service added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id) {
        $service = Service::findOrFail($id);

        return view('admin.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id) {
        $service = Service::findOrFail($id);

        return view('admin.services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id) {
        $this->validate($request, [
            'name' => 'required',
            'image' => 'required',
            'description' => 'required'
        ]);
        $requestData = $request->all();

        $service = Service::findOrFail($id);
        $imageName = uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
        $request->file('image')->move(base_path() . '/public/uploads/services/', $imageName);
        $requestData['image'] = $imageName;
        $service->update($requestData);

        return redirect('admin/services')->with('flash_message', 'Service updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id) {
        Service::destroy($id);

        return redirect('admin/services')->with('flash_message', 'Service deleted!');
    }

}
