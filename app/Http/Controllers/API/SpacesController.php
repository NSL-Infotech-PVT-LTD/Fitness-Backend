<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Space as MyModel;
use Twilio\Rest\Client;
use Validator;
use DB;
use Auth;

class SpacesController extends ApiController {

    public function store(Request $request) {

        $rules = ['name' => 'required', 'images_1' => 'required', 'images_2' => '', 'images_3' => '', 'images_4' => '', 'images_5' => '', 'description' => 'required', 'price_hourly' => 'required', 'availability_week' => 'required', 'price_daily' => 'required'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
            $input['organizer_id'] = \Auth::id();
            $images = [];

            for ($i = 1; $i <= 5; $i++):
                $var = 'images_' . $i;
                if (isset($var))
                    $images[] = parent::__uploadImage($request->file($var), public_path('uploads/spaces'));
            endfor;

            if (count($images) > 0)
                $input['images'] = json_encode($images);
            $space = MyModel::create($input);
            return parent::successCreated(['message' => 'Created Successfully', 'space' => $space]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function read(Request $request) {
        $rules = [];
        $validateAttributes = parent::validateAttributes($request, 'GET', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
//            $model = new MyModel();
            $model = MyModel::where('organizer_id', \Auth::id())->Select('id', 'name', 'images', 'description', 'price_hourly', 'availability_week', 'organizer_id', 'price_daily');

            return parent::success($model->get());
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function Update(Request $request) {
        $rules = ['name' => 'required', 'images' => '', 'description' => '', 'price_hourly' => '', 'availability_week' => '', 'price_daily' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            if (MyModel::where('id', $request->id)->where('organizer_id', \Auth::id())->get()->isEmpty() === true)
                return parent::error('Please use valid organizer id');
            $input = $request->all();
            $input['organizer_id'] = \Auth::id();
            $images = [];

            for ($i = 1; $i <= 5; $i++):
                $var = 'images_' . $i;
                if (isset($var))
                    $images[] = parent::__uploadImage($request->file($var), public_path('uploads/spaces'));
            endfor;

            if (count($images) > 0)
                $input['images'] = json_encode($images);
            $space = MyModel::findOrFail($request->id);
            $space->fill($input);
            $space->save();
            return parent::successCreated(['Message' => 'Updated Successfully', 'space' => $space]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function destroy(Request $request) {
        $rules = ['id' => 'required'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
//         dd($id);
        try {
            if (MyModel::where('id', $request->id)->where('organizer_id', \Auth::id())->get()->isEmpty() === true)
                return parent::error('Please use valid organizer id');
            MyModel::destroy($request->id);
            return parent::success(['message' => 'Deleted Successfully']);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getItems(Request $request) {
        //Validating attributes
        $user = \App\User::findOrFail(\Auth::id());
        if ($user->get()->isEmpty())
            return parent::error('User Not found');
        if ($user->hasRole('athlete') === false)
            return parent::error('Please use valid token');
        $rules = [];
        $validateAttributes = parent::validateAttributes($request, 'GET', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $model = new MyModel();
            $model = $model->where('athlete_id', \Auth::id())->select('id', 'name', 'images', 'description', 'price_hourly', 'availability_week', 'organizer_id', 'price_daily');
            return parent::success($model->get());
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

}
