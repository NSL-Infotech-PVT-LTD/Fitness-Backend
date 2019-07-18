<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends ApiController
{
    public function index() {
        $category =\App\Category::select('id','name')->with('sub_category')->get();
         return parent::success($category);
    }
}
