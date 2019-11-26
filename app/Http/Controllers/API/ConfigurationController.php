<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Parent_;
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
    
     public function getTerms(Request $request) {


        $rules = ['type'=>'required|in:organizer,coach,athlete'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
            
              
            $model = new MyModel();
             switch ($request->type):
                 
                case 'organiser':
                    $model = $model->select('terms_and_conditions_organiser');
                    break;
                case 'coach':
                 $model = $model->select('terms_and_conditions_coach');
                    break;
                case 'athlete':
                   $model = $model->select('terms_and_conditions_athlete');
                    break;
            endswitch;
            
            
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
            $user = \App\User::findOrFail(\Auth::id());
             if ($user->get()->isEmpty())
                return parent::error('User Not found');
            if ($user->hasRole('coach') === false)
                return parent::error('Please use valid auth token');
            $model = new MyModel();
            $model = $model->select('terms_and_conditions_coach');
            
            return parent::success($model->get());

        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }
    
    
}



