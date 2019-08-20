<?php

namespace App\Http\Controllers\SalonAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Carbon\Carbon;
use DB;

class SalonAdminCommonController extends Controller {

    public function __construct() {
        $this->middleware('guest');
    }

}
