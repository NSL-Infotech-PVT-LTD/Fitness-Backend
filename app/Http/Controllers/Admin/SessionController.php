<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Session;
use App\User;
use Illuminate\Http\Request;
use DataTables;

class SessionController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    protected $__rulesforindex = ['name' => 'required', 'hourly_rate' => 'required', 'phone' => 'required'];

    public function index(Request $request) {
        if ($request->ajax()) {
            $session = Session::all();
            return Datatables::of($session)
                            ->addIndexColumn()
                            ->editColumn('hourly_rate', function($item) {
                                return ' <i class="fa fa-' . config('app.stripe_default_currency') . '" aria-hidden="true"></i> ' . $item->hourly_rate;
                            })
                            ->addColumn('status', function($item) {
                                if ($item->start_date <= \Carbon\Carbon::now()) {
                                    return 'expired';
                                } elseif ($item->start_date >= \Carbon\Carbon::now()) {
                                    return 'not yet started';
                                }
                            })
                            ->addColumn('action', function($item) {
                                $return = '';

                                if ($item->state == '0'):
                                    $return .= "<button class='btn btn-danger btn-sm changeStatus' title='UnBlock'  data-id=" . $item->id . " data-status='UnBlock'>UnBlock / Active</button>";
                                else:
                                    $return .= "<button class='btn btn-success btn-sm changeStatus' title='Block' data-id=" . $item->id . " data-status='Block' >Block / Inactive</button>";
                                endif;
                                $return .= "<a href=" . url('/admin/session/' . $item->id) . " title='View Session'><button class='btn btn-info btn-sm'><i class='fa fa-eye' aria-hidden='true'></i></button></a>";
                                return $return;
                            })
                            ->rawColumns(['status', 'action', 'hourly_rate'])
                            ->make(true);
        }
        return view('admin.session.index', ['rules' => array_keys($this->__rulesforindex)]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create() {
        return view('admin.session.create');
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
            'description' => 'required',
            'business_hour' => 'required',
            'date' => 'required',
            'hourly_rate' => 'required',
            'images' => 'required'
        ]);
        $requestData = $request->all();

        Session::create($requestData);

        return redirect('admin/session')->with('flash_message', 'Session added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id) {
        $session = Session::findOrFail($id);
        $createdBy = User::where('id', $session->created_by)->value('name');
//dd($createdBy);

        return view('admin.session.show', compact('session','createdBy'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id) {
        $session = Session::findOrFail($id);

        return view('admin.session.edit', compact('session'));
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
            'description' => 'required',
            'business_hour' => 'required',
            'date' => 'required',
            'hourly_rate' => 'required',
            'images' => 'required'
        ]);
        $requestData = $request->all();

        $session = Session::findOrFail($id);
        $session->update($requestData);

        return redirect('admin/session')->with('flash_message', 'Session updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id) {
        Session::destroy($id);

        return redirect('admin/session')->with('flash_message', 'Session deleted!');
    }

    public function changeStatus(Request $request) {
        $session = Session::findOrFail($request->id);
        $session->state = $request->status == 'Block' ? '0' : '1';
        $session->save();
        return response()->json(["success" => true, 'message' => 'Session updated!']);
    }

}
