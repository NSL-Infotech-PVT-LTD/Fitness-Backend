<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Twilio\Rest\Client;
use Validator;
use DB;
use App\User as MyModel;

class UserController extends ApiController {

    public function getFreelancers(Request $request) {
        $rules = ['category_id' => 'required', 'latitude' => '', 'longitude' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', array_merge($this->requiredParams, $rules), array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $roleusers = \DB::table('role_user')->where('role_id', '2')->pluck('user_id');
//            $distanceUsers = parent::getDistanceByTable($request->latitude, $request->longitude, '100', 'users');
//            $userAllowed = [];
//            foreach ($distanceUsers as $distanceUser):
//                $userAllowed[] = $distanceUser->id;
//            endforeach;
//            dd($userAllowed);
            $data = MyModel::wherein('id', $roleusers);
            //LatLong logic
//            $data = $data->whereIn('id', $userAllowed);
//            $data = $data->where('category_id', 'like', '%' . $request->category_id . '%');
            return parent::successCreated($data->get());
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getUser(Request $request) {
        $rules = ['user_id' => 'required'];
        $validateAttributes = parent::validateAttributes($request, 'POST', array_merge($this->requiredParams, $rules), array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
//            dd($request->user_id);
            $data = MyModel::where('id', $request->user_id);
            return parent::successCreated($data->first());
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

}
