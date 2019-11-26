<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Configuration as MyModel;

use Validator;
use DB;

class ConfigurationController extends ApiController {

    public function getaboutus(Request $request) {


        $rules = [];
        $validateAttributes = parent::validateAttributes($request, 'GET', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
            $model = new MyModel();
            $model = $model->select('about_us');
            
            return parent::success($model->get());

        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }
    
    
}



