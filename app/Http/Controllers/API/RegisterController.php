<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Twilio\Rest\Client;
use Intervention\Image\ImageManagerStatic as Image;
use Validator;
use DB;

class RegisterController extends ApiController {

    public function customerRegister(Request $request) {
//        dd(implode(',',\App\Currency::get()->pluck('id')->toArray()));
        $rules = ['firstname' => 'required', 'phone' => '', 'email' => 'required|email|unique:users', 'profile_pic' => 'required', 'bio' => 'required', 'category_id' => 'required', 'experience' => 'required', 'hourly_rate' => 'required', 'latitude' => '', 'longitude' => '', 'portfolio_image_1' => '', 'portfolio_image_2' => '', 'portfolio_image_3' => '', 'portfolio_image_4' => '', 'currency' => 'in:' . implode(',', \App\Currency::get()->pluck('id')->toArray())];
        $rules = array_merge($this->requiredParams, $rules);
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
            $input['profile_pic'] = parent::__uploadImage($request->profile_pic, public_path('uploads/customer/profile_pic'));
            $user = \App\User::create($input);
            //Assign role to created user
            $user->assignRole(\App\Role::where('id', 2)->first()->name);
            // create user token for authorization
            $token = $user->createToken('netscape')->accessToken;
            // Add user device details for firbase
            parent::addUserDeviceData($user, $request);
            return parent::successCreated(['message' => 'Created Successfully', 'token' => $token, 'user' => $user]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function customerUpdate(Request $request) {
        $user = \App\User::findOrFail(\Auth::id());
        if ($user->get()->isEmpty())
            return parent::error('User Not found');
        if ($user->hasRole('freelancer') === false)
            return parent::error('Please use valid token');
        $rules = ['firstname' => '', 'profile_pic' => '', 'bio' => '', 'category_id' => '', 'experience' => '', 'hourly_rate' => '', 'latitude' => '', 'longitude' => '', 'portfolio_image_1' => '', 'portfolio_image_2' => '', 'portfolio_image_3' => '', 'portfolio_image_4' => '', 'currency' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            if (isset($request->phone)):
                if (!(\App\User::where('phone', $request->phone)->where('id', '!=', \Auth::id())->get()->isEmpty()))
                    return parent::error('The phone has already been taken');
            endif;
            $input = $request->all();
            if (isset($request->profile_pic))
                $input['profile_pic'] = parent::__uploadImage($request->profile_pic, public_path('uploads/freelancer/profile_pic'));
            $portfolioimageName = [];
            for ($i = 1; $i <= 4; $i++):
                $portfolio = 'portfolio_image_' . $i;
                if (isset($request->$portfolio))
                    $portfolioimageName[] = parent::__uploadImage($request->$portfolio, public_path('uploads/freelancer/portfolio'));
            endfor;
            if (count($portfolioimageName) > 0)
                $input['portfolio_image'] = json_encode($portfolioimageName);
//            else
//                $input['portfolio_image'] = null;
            $input['category_id'] = $request->category_id;
            $user->fill($input);
            $user->save();
            return parent::successCreated(['Message' => 'Updated Successfully', 'user' => $user]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function ClientRegister(Request $request) {
        $rules = ['phone' => '', 'email' => 'required|email|unique:users', 'firstname' => 'required', 'lastname' => 'required', 'profile_pic' => 'required', 'category_id' => 'required', 'latitude' => '', 'longitude' => ''];

        $rules = array_merge($this->requiredParams, $rules);
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
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
            $quickBlox = parent::registerUserQuickBlox($user->email, $user->firstname . ' ' . $user->lastname);
            $user->quick_blox_id = $quickBlox->user->id;
            $user->save();
            // Add user device details for firbase
            parent::addUserDeviceData($user, $request);
            return parent::successCreated(['Message' => 'Created Successfully', 'token' => $token, 'user' => $user]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function ClientUpdate(Request $request) {
        $user = \App\User::findOrFail(\Auth::id());
        if ($user->get()->isEmpty())
            return parent::error('User Not found');
        if ($user->hasRole('client') === false)
            return parent::error('Please use valid token');
        $rules = ['phone' => '', 'email' => 'required|email|unique:users,email,' . \Auth::id(), 'firstname' => 'required', 'lastname' => 'required', 'profile_pic' => 'required', 'category_id' => 'required', 'latitude' => '', 'longitude' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            if (isset($request->phone)):
                if (!(\App\User::where('phone', $request->phone)->where('id', '!=', \Auth::id())->get()->isEmpty()))
                    return parent::error('The phone has already been taken');
            endif;
            $input = $request->all();
//            var_dump(json_decode($input['category_id']));
//            dd('s');
            $input['profile_pic'] = parent::__uploadImage($request->profile_pic, public_path('uploads/client/profile_pic'));
            $input['category_id'] = $input['category_id'];
            $user->fill($input);
            $user->save();
            return parent::successCreated(['Message' => 'Updated Successfully', 'user' => $user]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function sendsms(Request $request) {
        try {
            $rules = ['phone' => 'required|unique:users,phone'];
            $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), true);
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
            $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), true);
            if ($validateAttributes):
                return $validateAttributes;
            endif;
            return parent::successCreated(['Message' => 'Please proceed with registeration process']);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function loginMobile(Request $request) {
        try {
            $rules = ['phone' => 'required|exists:users,phone'];
            $rules = array_merge($this->requiredParams, $rules);
            $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), true);
            if ($validateAttributes):
                return $validateAttributes;
            endif;

            $otp = parent::sendOTP($request->phone);
            // Add user device details for firbase
            $user = \App\User::where('phone', $request->phone)->first();
            parent::addUserDeviceData($user, $request);
            return parent::success(['message' => 'Login Successfully', 'otp' => $otp]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function loginEmail(Request $request) {
        try {
            $rules = ['email' => 'required|email|exists:users,email'];
            $rules = array_merge($this->requiredParams, $rules);
            $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), true);
            if ($validateAttributes):
                return $validateAttributes;
            endif;
            // Add user device details for firbase
            $user = \App\User::where('email', $request->email)->first();
            parent::addUserDeviceData($user, $request);
            return parent::success(['message' => 'Login Successfully']);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

}
