<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;

class ProductsController extends AdminCommonController {

    protected $__rules = ['name' => 'required', 'image' => 'required', 'images' => '', 'short_description' => 'required', 'description' => 'required', 'directions_for_use' => '', 'quantity' => 'required|integer', 'price' => 'required|integer'];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request) {
        $keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $products = Product::where('name', 'LIKE', "%$keyword%")
                            ->latest()->paginate($perPage);
        } else {
            $products = Product::latest()->paginate($perPage);
        }
        $keys = ['name', 'quantity', 'price'];
        return view('admin.products.index', compact('products', 'keys'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create() {
//        dd('admin.products.create');
        return view('admin.products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request) {
        $this->validate($request, $this->__rules);
        $requestData = $request->all();

        $imageName = uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
        $request->file('image')->move(base_path() . '/public/uploads/products/', $imageName);
        $requestData['image'] = $imageName;
//        dd($requestData);
        Product::create($requestData);

        return redirect('admin/products')->with('flash_message', 'Product added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id) {
        $product = Product::findOrFail($id);
        $keys = array_keys($this->__rules);
        return view('admin.products.show', compact('product', 'keys'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id) {
        $product = Product::findOrFail($id);

        return view('admin.products.edit', compact('product'));
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
        $this->validate($request, $this->__rules);
        $requestData = $request->all();

        $product = Product::findOrFail($id);

        $imageName = uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
        $request->file('image')->move(base_path() . '/public/uploads/products/', $imageName);
        $requestData['image'] = $imageName;
        $product->update($requestData);

        return redirect('admin/products')->with('flash_message', 'Product updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id) {
        Product::destroy($id);

        return redirect('admin/products')->with('flash_message', 'Product deleted!');
    }

}
