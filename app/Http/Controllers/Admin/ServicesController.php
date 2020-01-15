<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Service;
use Illuminate\Http\Request;
use DataTables;

class ServicesController extends AdminCommonController {

    public static $_basePath = '/public/uploads/services/';
    public static $_urlPath = '/uploads/services/';
    protected $__rulesforindex = ['name' => 'required'];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request) {
        if ($request->ajax()) {
            $services = Service::all();
            return Datatables::of($services)
                            ->addIndexColumn()
                            ->addColumn('action', function($item) {
                                $return = '';

                                if ($item->state == '0'):
                                    $return .= "<button class='btn btn-danger btn-sm changeStatus' title='UnBlock'  data-id=" . $item->id . " data-status='UnBlock'>UnBlock / Active</button>";
                                else:
                                    $return .= "<button class='btn btn-success btn-sm changeStatus' title='Block' data-id=" . $item->id . " data-status='Block' >Block / Inactive</button>";
                                endif;
                                $return .= " <a href=" . url('/admin/services/' . $item->id) . " title='View Service'><button class='btn btn-info btn-sm'><i class='fa fa-eye' aria-hidden='true'></i></button></a>
                                        <a href=" . url('/admin/services/' . $item->id . '/edit') . " title='Edit Service'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></button></a>"
                                        . " <button class='btn btn-danger btn-sm btnDelete' type='submit' data-remove='" . url('/admin/services/' . $item->id) . "'><i class='fa fa-trash-o' aria-hidden='true'></i></button>";
                                return $return;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('admin.services.index', ['rules' => array_keys($this->__rulesforindex)]);
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
            'description' => 'required'
        ]);
        $requestData = $request->all();
//        $imageName = uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
//        $request->file('image')->move(base_path() . '/public/uploads/services/', $imageName);
//        $requestData['image'] = $imageName;
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
            'description' => 'required'
        ]);
        $requestData = $request->all();

        $service = Service::findOrFail($id);
//        if (isset($request->image)):
//            $imageName = uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
//            $request->file('image')->move(base_path() . '/public/uploads/services/', $imageName);
//            $requestData['image'] = $imageName;
//        endif;

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
        if (Service::destroy($id)) {
            $data = 'Success';
        } else {
            $data = 'Failed';
        }
        return response()->json($data);
    }

    public function changeStatus(Request $request) {
        $appointment = Service::findOrFail($request->id);
        $appointment->state = $request->status == 'Block' ? '0' : '1';
        $appointment->save();
        return response()->json(["success" => true, 'message' => 'Service updated!']);
    }

}
