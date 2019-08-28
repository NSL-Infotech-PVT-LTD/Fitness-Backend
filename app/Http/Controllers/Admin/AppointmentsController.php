<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Appointment;
use Illuminate\Http\Request;

class AppointmentsController extends AdminCommonController {

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
        $appointments = $appointments->paginate($perPage);
        return view('admin.appointments.index', compact('appointments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create() {
        return view('admin.appointments.create');
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
        Appointment::create($requestData);

        return redirect('admin/appointments')->with('flash_message', 'Appointment added!');
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

        return view('admin.appointments.show', compact('appointment'));
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

        return view('admin.appointments.edit', compact('appointment'));
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

        return redirect('admin/appointments')->with('flash_message', 'Appointment updated!');
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

        return redirect('admin/appointments')->with('flash_message', 'Appointment deleted!');
    }

    public function getServicebySalon(Request $request) {
        $html = '';
        foreach (\App\Service::where('salon_user_id', $request->id)->get() as $service):
            $html .= '<option value="' . $service->id . '">' . $service->name . '</option>';
        endforeach;
        return response()->json(["success" => true, 'html' => $html]);
    }

}
