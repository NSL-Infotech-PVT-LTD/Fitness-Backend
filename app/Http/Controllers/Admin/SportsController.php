<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Sport;
use Illuminate\Http\Request;
use DataTables;

class SportsController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    protected $__rulesforindex = ['name' => 'required'];

    public function index(Request $request) {
        if ($request->ajax()) {
            $sports = Sport::all();
            return Datatables::of($sports)
                            ->addIndexColumn()
                            ->addColumn('action', function($item) {
                                $return = '';

                                if ($item->state == '0'):
                                    $return .= "<button class='btn btn-danger btn-sm changeStatus' title='UnBlock'  data-id=" . $item->id . " data-status='UnBlock'>UnBlock / Active</button>";
                                else:
                                    $return .= "<button class='btn btn-success btn-sm changeStatus' title='Block' data-id=" . $item->id . " data-status='Block' >Block / Inactive</button>";
                                endif;
                                $return .= "<a href=" . url('/admin/sports/' . $item->id) . " title='View Sports'><button class='btn btn-info btn-sm'><i class='fa fa-eye' aria-hidden='true'></i></button></a>
                                        <a href=" . url('/admin/sports/' . $item->id . '/edit') . " title='Edit Sports'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></button></a>"
                                        . "<button class='btn btn-danger btn-sm btnDelete' type='submit' data-remove='" . url('/admin/sports/' . $item->id) . "'><i class='fa fa-trash-o' aria-hidden='true'></i></button>";
                                return $return;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('admin.sports.index', ['rules' => array_keys($this->__rulesforindex)]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create() {
        return view('admin.sports.create');
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
        ]);
        $requestData = $request->all();

        Sport::create($requestData);

        return redirect('admin/sports')->with('flash_message', 'Sports added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id) {
        $sports = Sport::findOrFail($id);

        return view('admin.sports.show', compact('sports'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id) {
        $sports = Sport::findOrFail($id);

        return view('admin.sports.edit', compact('sports'));
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
        ]);
        $requestData = $request->all();

        $sports = Sport::findOrFail($id);
        $sports->update($requestData);

        return redirect('admin/sports')->with('flash_message', 'Sport updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id) {
        Sport::destroy($id);

        return redirect('admin/sports')->with('flash_message', 'sport deleted!');
    }

    public function changeStatus(Request $request) {
        $sports = Sport::findOrFail($request->id);
        $sports->state = $request->status == 'Block' ? '0' : '1';
        $sports->save();
        return response()->json(["success" => true, 'message' => 'Sport updated!']);
    }

}
