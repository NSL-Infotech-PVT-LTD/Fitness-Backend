<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Configuration as MyModel;
use Auth;
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
    
     public function getTermsOrganiser(Request $request) {


        $rules = [];
        $validateAttributes = parent::validateAttributes($request, 'GET', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
           
            $model = new MyModel();
            $model = $model->select('terms_and_conditions_organiser');
            
            return parent::success($model->get());

        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }
     public function getTermsCoach(Request $request) {


        $rules = [];
        $validateAttributes = parent::validateAttributes($request, 'GET', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
           
            $model = new MyModel();
            $model = $model->select('terms_and_conditions_coach');
            
            return parent::success($model->get());

        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }
     public function getTermsAthlete(Request $request) {


        $rules = [];
        
        $validateAttributes = parent::validateAttributes($request, 'GET', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
           
            $model = new MyModel();
            $model = $model->select('terms_and_conditions_athlete');
            
            return parent::success($model->get());

        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }
    
}



