<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Service as MyModel;
use Twilio\Rest\Client;
use Intervention\Image\ImageManagerStatic as Image;
use Validator;
use DB;

class ServicesController extends ApiController {

    public function getitems(Request $request) {


        $rules = ['search'=>'','limit'=>''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
            $model = new MyModel();
            $model = $model->select('id','name');
            if (isset($request->search))
                $model = $model->Where('name', 'LIKE', "%$request->search%");
            $perPage = isset($request->limit) ? $request->limit : 20;
            return parent::success($model->paginate($perPage));

        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }
}

