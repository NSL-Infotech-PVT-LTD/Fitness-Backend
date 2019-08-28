<?php

namespace App\Http\Controllers\SalonAdmin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Appointment;
use Illuminate\Http\Request;

class AppointmentsController extends SalonAdminCommonController {

    public function __construct() {
        $this->middleware('guest');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request) {
        $keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $appointments = Appointment::where('service_id', 'LIKE', "%$keyword%")
                    ->orWhere('date', 'LIKE', "%$keyword%")
                    ->orWhere('start_time', 'LIKE', "%$keyword%")
                    ->orWhere('end_time', 'LIKE', "%$keyword%")
                    ->orWhere('comments', 'LIKE', "%$keyword%")
                    ->latest();
        } else {
            $appointments = Appointment::latest();
        }
        $appointments = $appointments->where('salon_user_id', \Auth::user()->id);
        $appointments = $appointments->paginate($perPage);
//        $appointments = Appointment::whereDate('created_at', Carbon::today())->get();
        return view('salon-admin.appointments.index', compact('appointments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create() {
        return view('salon-admin.appointments.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request) {

        $requestData = $request->all();
        $requestData['salon_user_id'] = \Auth::user()->id;
        Appointment::create($requestData);

        return redirect('salon-admin/appointments')->with('flash_message', 'Appointment added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id) {
        $appointment = Appointment::findOrFail($id);

        return view('salon-admin.appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id) {
        $appointment = Appointment::findOrFail($id);

        return view('salon-admin.appointments.edit', compact('appointment'));
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

        $requestData = $request->all();

        $appointment = Appointment::findOrFail($id);
        $appointment->update($requestData);

        return redirect('salon-admin/appointments')->with('flash_message', 'Appointment updated!');
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
        $appointment = Appointment::findOrFail($request->id);
        $appointment->status = $request->status;
        $appointment->save();
        return response()->json(["success" => true, 'message' => 'Appointment updated!']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id) {
        Appointment::destroy($id);

        return redirect('salon-admin/appointments')->with('flash_message', 'Appointment deleted!');
    }

}
