<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Twilio\Rest\Client;
use Validator;
use DB;
use App\UserFavourite as MyModel;

class UserFavouriteController extends ApiController {

    public function store(Request $request) {
        $rules = ['freelancer_id' => 'required|exists:users,id'];
        $validateAttributes = parent::validateAttributes($request, 'POST', array_merge($this->requiredParams, $rules), array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        $clientID = \Auth::id();
//        dd($clientID);
        try {
            $checkFavourite = MyModel::where('client_id', $clientID)->where('freelancer_id', $request->freelancer_id)->get();
            if ($checkFavourite->isEmpty()):
                $favourite = new MyModel();
                $favourite->client_id = $clientID;
                $favourite->freelancer_id = $request->freelancer_id;
                $favourite->save();
                return parent::successCreated('Freelancer Marked as Favourite');
            else:
                MyModel::destroy($checkFavourite->first()->id);
                return parent::successCreated('Freelancer UnMarked as Favourite');
            endif;
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getItems(Request $request) {
//        $rules = ['client_id' => 'required|exists:users,id'];
        $validateAttributes = parent::validateAttributes($request, 'GET', array_merge($this->requiredParams), [], false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        $clientID = \Auth::id();
        try {
            $client_ids = MyModel::where('client_id', $clientID)->get();
            if ($client_ids->isEmpty() === true)
                return parent::successCreated('No Favourite Freelancer Found', '203');

            $model = \App\User::wherein('id', $client_ids->pluck('freelancer_id')->toArray());
            if ($model->get()->isEmpty() !== true)
                return parent::success($model->get());
            else
                return parent::error('No Data Found');
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

}
