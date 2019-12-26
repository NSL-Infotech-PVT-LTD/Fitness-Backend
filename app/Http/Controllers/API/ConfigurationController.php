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

    public function getConfigurationColumn(Request $request, $column) {
        $user = User::findOrFail(\Auth::id());
        $rules = [];
        $validateAttributes = parent::validateAttributes($request, 'GET', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {

            if (!in_array($column, ['about_us', 'terms_and_conditions']))
                return parent::error('Please use valid column');
//dd($column);
            $key = '';
            if ($column == 'terms_and_conditions'):
                if ($user->hasRole('organizer') === true)
                    $key = '_organiser';
                if ($user->hasRole('coach') === true)
                    $key = '_coach';
                if ($user->hasRole('athlete') === true)
                    $key = '_athlete';
            endif;
            $model = new MyModel();
            $model = $model->first();
            $var = $column . $key;
//            dd($var);
            return parent::success($model->$var, 200, 'data');
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getaboutus(Request $request) {


        $rules = [];
        $validateAttributes = parent::validateAttributes($request, 'GET', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
            $model = new MyModel();
            $model = $model->select('about_us')->first();

            return parent::success($model);
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
