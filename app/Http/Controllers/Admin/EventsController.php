<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Event;
use Illuminate\Http\Request;
use DataTables;

class EventsController extends AdminCommonController {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    protected $__rulesforindex = ['name' => 'required', 'location' => 'required', 'guest_allowed' => 'required', 'price' => 'required'];

    public function index(Request $request) {
        if ($request->ajax()) {
            $events = Event::all();
            return Datatables::of($events)
                            ->addIndexColumn()
                            ->editColumn('price', function($item) {
                                return ' <i class="fa fa-' . config('app.stripe_default_currency') . '" aria-hidden="true"></i> ' . $item->price;
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
                                $return .= " <a href=" . url('/admin/events/' . $item->id) . " title='View Event'><button class='btn btn-info btn-sm'><i class='fa fa-eye' aria-hidden='true'></i></button></a>";
                                return $return;
                            })
                            ->rawColumns(['status', 'action','price'])
                            ->make(true);
        }
        return view('admin.events.index', ['rules' => array_keys($this->__rulesforindex)]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create() {
        return view('admin.events.create');
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
            'start_at' => 'required',
            'end_at' => 'required',
            'location' => 'required',
            'service_id' => 'required',
            'organizer_id' => 'required',
            'guest_allowed' => 'required'
        ]);
        $requestData = $request->all();

        Event::create($requestData);

        return redirect('admin/events')->with('flash_message', 'Event added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id) {
        $event = Event::findOrFail($id);

        return view('admin.events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id) {
        $event = Event::findOrFail($id);

        return view('admin.events.edit', compact('event'));
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
            'start_at' => 'required',
            'end_at' => 'required',
            'location' => 'required',
            'service_id' => 'required',
            'organizer_id' => 'required',
            'guest_allowed' => 'required'
        ]);
        $requestData = $request->all();

        $event = Event::findOrFail($id);
        $event->update($requestData);

        return redirect('admin/events')->with('flash_message', 'Event updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id) {
        Event::destroy($id);

        return redirect('admin/events')->with('flash_message', 'Event deleted!');
    }

    public function changeStatus(Request $request) {
        $appointment = Event::findOrFail($request->id);
        $appointment->state = $request->status == 'Block' ? '0' : '1';
        $appointment->save();
        return response()->json(["success" => true, 'message' => 'Event updated!']);
    }

}
