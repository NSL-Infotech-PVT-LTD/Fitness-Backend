<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController {

    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;

//    public function success($data = [], $code = 200) {
//        return response()->json(['data' => $data, 'success' => true], $code);
//    }
//
//    public function error($message = 'Internal Server Error', $code = 200) {
//        return response()->json(['message' => $message, 'success' => false], $code);
//    }

}
