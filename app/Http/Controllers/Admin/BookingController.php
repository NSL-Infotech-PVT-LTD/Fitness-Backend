<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Booking;
use App\User;
use App\Event;
use App\Session;
use App\Space;
use Illuminate\Http\Request;
use DataTables;

class BookingController extends AdminCommonController {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    protected $__rulesforindex = ['type' => 'required', 'target_id' => 'required', 'user_id' => 'required', 'owner_id' => 'required'];

    public function index(Request $request) {
        if ($request->ajax()) {
            $booking = Booking::all();
            return Datatables::of($booking)
                            ->addIndexColumn()
//                            ->editColumn('target_id', function($item) {
//
//                                $event = \App\Event::select('name')->where('id', $item->target_id)->first();
//                                $session = \App\Session::select('name')->where('id', $item->target_id)->first();
//                                $space = \App\Space::select('name')->where('id', $item->target_id)->first();
//                                if ('type' == 'event')
//                                {
//                                    return $event->name;
//                                }
//                                elseif ('type' == 'session'){
//                                    return $session->name;
//                                }
//                                else
//                                {
//                                    return $space->name;
//                                }
//                            })
                            ->editColumn('user_id', function($item) {
                                $return = \App\User::select('name')->where('id', $item->user_id)->first();
                                return $return->name;
                            })
                            ->editColumn('owner_id', function($item) {
                                $return = \App\User::select('name')->where('id', $item->owner_id)->first();
                                return $return->name;
                            })
                            ->addColumn('action', function($item) {
                                $return = '';
                                $return .= " <a href=" . url('/admin/bookings/' . $item->id) . " title='View Booking'><button class='btn btn-info btn-sm'><i class='fa fa-eye' aria-hidden='true'></i></button></a>";
                                return $return;
                            })
                            ->rawColumns(['action', 'user_id', 'owner_id'])
                            ->make(true);
        }
        return view('admin.bookings.index', ['rules' => array_keys($this->__rulesforindex)]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create() {
        return view('admin.bookings.create');
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
        ]);
        $requestData = $request->all();

        Booking::create($requestData);

        return redirect('admin/bookings')->with('flash_message', 'Booking added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id) {
        $booking = Booking::findOrFail($id);
        $targetId = Event::where('id', $booking->target_id)->value('name');
        $targetId = Session::where('id', $booking->target_id)->value('name');
        $targetId = Space::where('id', $booking->target_id)->value('name');
        $userId = User::where('id', $booking->user_id)->value('name');
        $ownerId = User::where('id', $booking->owner_id)->value('name');



//dd($createdBy);
        return view('admin.bookings.show', compact('booking', 'targetId', 'userId', 'ownerId'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id) {
        $booking = Booking::findOrFail($id);

        return view('admin.bookings.edit', compact('booking'));
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
        ]);
        $requestData = $request->all();

        $booking = Booking::findOrFail($id);
        $booking->update($requestData);

        return redirect('admin/bookings')->with('flash_message', 'Booking updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id) {
        Booking::destroy($id);

        return redirect('admin/bookings')->with('flash_message', 'Booking deleted!');
    }

    public function changeStatus(Request $request) {
        $booking = Booking::findOrFail($request->id);
        $booking->state = $request->status == 'Block' ? '0' : '1';
        $booking->save();
        return response()->json(["success" => true, 'message' => 'Booking updated!']);
    }

}
