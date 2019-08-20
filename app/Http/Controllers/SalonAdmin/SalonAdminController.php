<?php

namespace App\Http\Controllers\SalonAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Carbon\Carbon;
use DB;

class SalonAdminController extends SalonAdminCommonController {

    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index() {
        $orders = "0";
        return view('salon-admin.dashboard', compact('salonadmin', 'customers', 'orders'));
    }

}
