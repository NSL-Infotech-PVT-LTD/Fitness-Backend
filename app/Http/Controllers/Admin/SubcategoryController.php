<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Subcategory;
use DB;

class SubcategoryController extends Controller {

    public function index($id) {
        $subcategory = \App\Subcategory::where('categories_id', $id)->get();
        return view('admin.subcategories.index', compact('subcategory'));
    }

    public function create($id) {
        return view('admin.subcategories.create', ['id' => $id]);
    }

    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'categories_id' => ''
        ]);
        $requestData = $request->all();
        $Subcategory = Subcategory::create($requestData);
        return redirect('admin/subcategories/' . $request->categories_id)->with('flash_message', 'Category added!');
    }

    public function show($id) {
        $category = Subcategory::findOrFail($id);
        return view('admin.subcategories.show', compact('category'));
    }

    public function edit($id) {
        $category = Subcategory::findOrFail($id);
        return view('admin.subcategories.edit', compact('category'));
    }

    public function update(Request $request, $id) {
        $this->validate($request, [
            'name' => 'required',
            'categories_id' => ''
        ]);
        $requestData = $request->all();
       //dd($requestData);
        $category = Subcategory::findOrFail($id);
        $category->update($requestData);
        return redirect('/admin/subcategories/'.$category->categories_id)->with('flash_message', 'Sub Category updated!');
    }
    
    
    public function destroy($id) {
        Subcategory::destroy($id);
        return redirect(url()->previous()
)->with('flash_message', 'Category deleted!');
    }

}
