<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Twilio\Rest\Client;
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
        $rules = array_merge($this->requiredParams, $rules);
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
            $input['sport_id']= json_encode($request->sport_id);
            $input['organisation_id']= \Auth::id();
            $input['profile_image'] = parent::__uploadImage($request->file('profile_image'), public_path('uploads/organiserCoach/profile_image'),true);

            $organiserCoach = \App\OrganiserCoach::create($input);
            return parent::successCreated(['message' => 'Created Successfully', 'token' => $token, 'organiserCoach' => $organiserCoach]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }
}
