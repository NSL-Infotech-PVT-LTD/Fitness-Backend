<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Space;
use Illuminate\Http\Request;
use DataTables;

class SpacesController extends AdminCommonController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
     protected $__rulesforindex = ['name' => 'required', 'description'=>'required','price_hourly' => 'required'];
    public function index(Request $request)
    {
      if ($request->ajax()) {
            $spaces = Space::all();
            return Datatables::of($spaces)
                            ->addIndexColumn()
                            ->addColumn('action', function($item) {
                                $return = '';

                                if ($item->state == '0'):
                                    $return .= "<button class='btn btn-danger btn-sm changeStatus' title='UnBlock'  data-id=" . $item->id . " data-status='UnBlock'>UnBlock / Active</button>";
                                else:
                                    $return .= "<button class='btn btn-success btn-sm changeStatus' title='Block' data-id=" . $item->id . " data-status='Block' >Block / Inactive</button>";
                                endif;
                                $return .= "<a href=" . url('/admin/spaces/' . $item->id) . " title='View Space'><button class='btn btn-info btn-sm'><i class='fa fa-eye' aria-hidden='true'></i></button></a>
                                        <a href=" . url('/admin/spaces/' . $item->id . '/edit') . " title='Edit Event'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></button></a>"
                                        . "<button class='btn btn-danger btn-sm btnDelete' type='submit' data-remove='" . url('/admin/spaces/' . $item->id) . "'><i class='fa fa-trash-o' aria-hidden='true'></i></button>";
                                return $return;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('admin.spaces.index', ['rules' => array_keys($this->__rulesforindex)]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.spaces.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $this->validate($request, [
			'name' => 'required',
			'price_hourly' => 'required'
		]);
        $requestData = $request->all();

        Space::create($requestData);

        return redirect('admin/spaces')->with('flash_message', 'Space added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $space = Space::findOrFail($id);

        return view('admin.spaces.show', compact('space'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $space = Space::findOrFail($id);

        return view('admin.spaces.edit', compact('space'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
			'name' => 'required',
			'price_hourly' => 'required'
		]);
        $requestData = $request->all();

        $space = Space::findOrFail($id);
        $space->update($requestData);

        return redirect('admin/spaces')->with('flash_message', 'Space updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        Space::destroy($id);

        return redirect('admin/spaces')->with('flash_message', 'Space deleted!');
    }
     public function changeStatus(Request $request) {
        $appointment = Space::findOrFail($request->id);
        $appointment->state = $request->status == 'Block' ? '0' : '1';
        $appointment->save();
        return response()->json(["success" => true, 'message' => 'Space updated!']);
    }
}
