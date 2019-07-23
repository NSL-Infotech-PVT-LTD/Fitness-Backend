<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Twilio\Rest\Client;
use Validator;
use DB;

class RegisterController extends ApiController {

    public function frelancerRegister(Request $request) {
        $rules = ['firstname' => 'required', 'phone' => '', 'email' => 'required|email|unique:users', 'profile_pic' => 'required', 'bio' => 'required', 'category_id' => 'required', 'experience' => 'required', 'hourly_rate' => 'required', 'latitude' => '', 'longitude' => '', 'portfolio_image_1' => 'required', 'portfolio_image_2' => '', 'portfolio_image_3' => '', 'portfolio_image_4' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', array_merge($this->requiredParams, $rules), array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            if (isset($request->phone)):
                if (!(\App\User::where('phone', $request->phone)->get()->isEmpty()))
                    return parent::error('The phone has already been taken');
            endif;
            $input = $request->all();
            $input['profile_pic'] = parent::__uploadImage($request->profile_pic, public_path('uploads/freelancer/profile_pic'));
            $portfolioimageName = [];
            for ($i = 1; $i <= 4; $i++):
                $portfolio = 'portfolio_image_' . $i;
                $portfolioimageName[] = parent::__uploadImage($request->$portfolio, public_path('uploads/freelancer/portfolio'));
            endfor;
            $input['portfolio_image'] = json_encode($portfolioimageName);
            $input['category_id'] = $request->category_id;
            $user = \App\User::create($input);
            $user->assignRole(\App\Role::where('id', 2)->first()->name);
            $token = $user->createToken('netscape')->accessToken;
            return parent::successCreated(['Message' => 'Created Successfully', 'token' => $token]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function ClientRegister(Request $request) {
        $rules = ['phone' => '', 'email' => 'required|email|unique:users', 'firstname' => 'required', 'lastname' => 'required', 'profile_pic' => 'required', 'category_id' => 'required', 'latitude' => '', 'longitude' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', array_merge($this->requiredParams, $rules), array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            if (isset($request->phone)):
                if (!(\App\User::where('phone', $request->phone)->get()->isEmpty()))
                    return parent::error('The phone has already been taken');
            endif;
            $input = $request->all();
//            var_dump(json_decode($input['category_id']));    
//            dd('s');
            $input['profile_pic'] = parent::__uploadImage($request->profile_pic, public_path('uploads/client/profile_pic'));
            $input['category_id'] = $input['category_id'];
            $user = \App\User::create($input);
            $user->assignRole(\App\Role::where('id', 3)->first()->name);
            $token = $user->createToken('netscape')->accessToken;
            return parent::successCreated(['Message' => 'Created Successfully', 'token' => $token]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function sendsms(Request $request) {
        try {
            $rules = ['phone' => 'required|unique:users,phone'];
            $validateAttributes = parent::validateAttributes($request, 'POST', array_merge($this->requiredParams, $rules), array_keys($rules), true);
            if ($validateAttributes):
                return $validateAttributes;
            endif;
            $otp = parent::sendOTP($request->phone);
            return parent::successCreated(['Message' => 'Otp Send Successfully', 'otp' => $otp]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function checkEmail(Request $request) {
        try {
            $rules = ['email' => 'required|email|unique:users,email'];
            $validateAttributes = parent::validateAttributes($request, 'POST', array_merge($this->requiredParams, $rules), array_keys($rules), true);
            if ($validateAttributes):
                return $validateAttributes;
            endif;
            return parent::successCreated(['Message' => 'Please proceed with registeration process']);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

}
