<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Configuration as MyModel;
use Twilio\Rest\Client;
use Validator;
use App\User;
use DB;
use Auth;

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

    public function getTerms(Request $request) {
        $user = User::findOrFail(\Auth::id());

        $rules = [];
        $validateAttributes = parent::validateAttributes($request, 'GET', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {

            if ($user->get()->isEmpty())
                return parent::error('User Not found');
            $model = new MyModel();
            if ($user->hasRole('organizer') === true):
                $key = 'terms_and_conditions_organiser';
            endif;
            if ($user->hasRole('coach') === true):
                $key = 'terms_and_conditions_coach';
            endif;
            if ($user->hasRole('athlete') === true):
                $key = 'terms_and_conditions_athlete';
            endif;
            return parent::success($model->get()->pluck($key));
        } catch (\Exception $ex) {

            return parent::error($ex->getMessage());
        }
    }

}
