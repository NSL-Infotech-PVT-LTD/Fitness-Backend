<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\portfolio;
use Illuminate\Http\Request;

class portfoliosController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request) {
        $keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $portfolios = portfolio::where('name', 'LIKE', "%$keyword%")
                            ->latest()->paginate($perPage);
        } else {
            $portfolios = portfolio::latest()->paginate($perPage);
        }

        return view('admin.portfolios.index', compact('portfolios'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create() {
        return view('admin.portfolios.create');
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
            'lastname' => 'required',
            'image' => 'required',
        ]);
        $images = array();
        if ($request->hasfile('image')) {
            foreach ($request->image as $file) {
                $fileName = $file->getClientOriginalName();
                $destinationPath = public_path('images');
                $file->move($destinationPath, $fileName);
                $images[] = $fileName;
            }
        }
        $requestData = $request->all();
        $requestData['image'] = implode($images);
        portfolio::create($requestData);

        return redirect('admin/portfolios')->with('flash_message', 'portfolio added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id) {
        $portfolio = portfolio::findOrFail($id);

        return view('admin.portfolios.show', compact('portfolio'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id) {
        $portfolio = portfolio::findOrFail($id);

        return view('admin.portfolios.edit', compact('portfolio'));
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
            'name' => 'required'
        ]);
        $requestData = $request->all();

        $portfolio = portfolio::findOrFail($id);
        $portfolio->update($requestData);

        return redirect('admin/portfolios')->with('flash_message', 'portfolio updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id) {
        portfolio::destroy($id);

        return redirect('admin/portfolios')->with('flash_message', 'portfolio deleted!');
    }

}
