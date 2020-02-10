<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Contact;
use App\User;
use App\Role;
use Illuminate\Http\Request;
use DataTables;

class ContactController extends AdminCommonController {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    protected $__rulesforindex = ['message' => 'required', 'media' => 'required', 'sender_name' => 'required'];

    public function index(Request $request) {
        if ($request->ajax()) {
            $contact = Contact::all();
            return Datatables::of($contact)
                            ->addIndexColumn()
                            ->editColumn('message', function($item) {
//                               $return = $item->message;
                                $return = strlen($item->message) > 10 ? substr($item->message, 0, 10) . "..." : $item->message;
                                return $return;
                            })
                            ->editColumn('sender_name', function($item) {
                                $return = \App\User::select('name')->where('id', $item->sender_name)->first();
                                return $return->name;
                            })
                            ->addColumn('sender_email', function($item) {
                                $return = \App\User::select('email')->where('id', $item->sender_name)->first();
                                return $return->email;
                            })
                            ->addColumn('sender_phone', function($item) {
                                $return = \App\User::select('phone')->where('id', $item->sender_name)->first();
                                return $return->phone;
                            })
                            ->editColumn('media', function($item) {
                                return "<img width='50' src=" . url('uploads/contact/' . $item->media) . ">";
                            })
                            ->addColumn('action', function($item) {
                                $return = '';


                                $return .= " <a href=" . url('/admin/contact/' . $item->id) . " title='View Query'><button class='btn btn-info btn-sm'><i class='fa fa-eye' aria-hidden='true'></i></button></a>";
                                return $return;
                            })
                            ->rawColumns(['message', 'action', 'media', 'sender_email', 'sender_phone'])
                            ->make(true);
        }
        return view('admin.contact.index', ['rules' => array_keys($this->__rulesforindex)]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create() {
        return view('admin.contact.create');
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
            'message' => 'required',
            'media' => 'required'
        ]);
        $requestData = $request->all();

        Contact::create($requestData);

        return redirect('admin/contact')->with('flash_message', 'Contact added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id) {
        $contact = Contact::findOrFail($id);
        $createdBy = User::where('id', $contact->sender_name)->value('name');
        $createdEmail = User::where('id', $contact->sender_name)->value('email');
        $createdPhone = User::where('id', $contact->sender_name)->value('phone');
        $roleusersSA = \DB::table('role_user')->where('user_id', User::where('id', $contact->sender_name)->first()->id)->pluck('role_id');
        $createdType = Role::where('id', $roleusersSA)->value('name');
//dd($createdType);




        return view('admin.contact.show', compact('contact', 'createdBy', 'createdEmail', 'createdPhone', 'createdType'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id) {
        $contact = Contact::findOrFail($id);

        return view('admin.contact.edit', compact('contact'));
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
            'message' => 'required',
            'media' => 'required'
        ]);
        $requestData = $request->all();

        $contact = Contact::findOrFail($id);
        $contact->update($requestData);

        return redirect('admin/contact')->with('flash_message', 'Contact updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id) {
        Contact::destroy($id);

        return redirect('admin/contact')->with('flash_message', 'Contact deleted!');
    }

}
