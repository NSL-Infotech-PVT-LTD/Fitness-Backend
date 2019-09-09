<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Service as MyModel;
use Twilio\Rest\Client;
use Validator;
use DB;

class ServicesController extends ApiController {

    public function getitems(Request $request) {

        
        $rules = [];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
            $model = new MyModel();
            $model = $model->select('id', 'name');
            
            return parent::success($model->where('state', '1')->get());
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

}

