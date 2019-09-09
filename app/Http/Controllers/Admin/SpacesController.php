<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Space;
use Illuminate\Http\Request;

class SpacesController extends AdminCommonController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $spaces = Space::where('name', 'LIKE', "%$keyword%")
                ->orWhere('images', 'LIKE', "%$keyword%")
                ->orWhere('description', 'LIKE', "%$keyword%")
                ->orWhere('price_hourly', 'LIKE', "%$keyword%")
                ->orWhere('availability_week', 'LIKE', "%$keyword%")
                ->orWhere('organizer_id', 'LIKE', "%$keyword%")
                ->orWhere('price_weekly', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $spaces = Space::latest()->paginate($perPage);
        }

        return view('admin.spaces.index', compact('spaces'));
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
}
