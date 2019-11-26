<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Twilio\Rest\Client;
use App\OrganiserCoach as MyModel;
use Validator;
use DB;
use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\Factory;
use Illuminate\Support\Facades\Password;
use Illuminate\Mail\Message;

class OrganiserCoachController extends ApiController
{
    public function Store(Request $request) {
        $user = \App\User::findOrFail(\Auth::id());
        if ($user->get()->isEmpty())
            return parent::error('User Not found');
        if ($user->hasRole('organizer') === false)
            return parent::error('Please use valid token');
        $rules = ['name' => 'required','profile_image'=>'required','bio' => 'required', 'sport_id'=>'required','hourly_rate' => 'required', 'experience_detail'=>'required','expertise_years' => 'required','profession'=>'required','training_service_detail'=>'required'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
            $input['sport_id']= json_encode($request->sport_id);
            $input['organisation_id']= \Auth::id();
            $input['profile_image'] = parent::__uploadImage($request->file('profile_image'), public_path('uploads/organiserCoach/profile_image'),true);

            $organiserCoach = MyModel::create($input);
            return parent::successCreated(['message' => 'Created Successfully','organiserCoach' => $organiserCoach]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }
    public function update(Request $request) {
        $user = \App\User::findOrFail(\Auth::id());
        if ($user->get()->isEmpty())
            return parent::error('User Not found');
        if ($user->hasRole('organizer') === false)
            return parent::error('Please use valid token');
        $rules = ['id'=>'required|exists:organiser_coaches,id','name' => '', 'profile_image' => '', 'bio' => '', 'sport_id' => '', 'hourly_rate' => '', 'experience_detail' => '', 'expertise_years' => '','profession'=>'','training_service_detail'=>''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
            $input['sport_id']= json_encode($request->sport_id);
            $input['organisation_id']= \Auth::id();
            if (isset($request->profile_image))
                $input['profile_image'] = parent::__uploadImage($request->file('profile_image'), public_path('uploads/organiserCoach/profile_image'),true);
            $organiserCoach = MyModel::findOrfail($request->id);
            $organiserCoach->fill($input);
            $organiserCoach->save();

            return parent::successCreated(['Message' => 'Updated Successfully', 'organiserCoach' => $organiserCoach]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getitems(Request $request) {


        $rules = ['search'=>'','limit'=>''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
            $model = new MyModel();
            $model = $model->select('id','name','profile_image','bio','sport_id','organisation_id','hourly_rate','experience_detail','expertise_years','profession','training_service_detail');
            if (isset($request->search))
                $model = $model->Where('name', 'LIKE', "%$request->search%");
            $perPage = isset($request->limit) ? $request->limit : 20;
            return parent::success($model->paginate($perPage));

        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getOrganiseritems(Request $request) {


        $rules = ['search'=>'','limit'=>'','organiser_id'=>''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
            $model = new MyModel();
            $model = $model->where('organisation_id',$request->organiser_id)->select('id','name','profile_image','bio','sport_id','organisation_id','hourly_rate','experience_detail','expertise_years','profession','training_service_detail');
            if (isset($request->search))
                $model = $model->Where('name', 'LIKE', "%$request->search%");
            $perPage = isset($request->limit) ? $request->limit : 20;
            return parent::success($model->paginate($perPage));

        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }
}
