<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Carbon\Carbon;
use DB;

class AdminCommonController extends Controller {

    public function __construct() {
        $this->middleware('guest');
    }

}
