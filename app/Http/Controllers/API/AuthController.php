<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Twilio\Rest\Client;
use Validator;
use DB;
use Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends ApiController {

    public function Salonregister(Request $request) {
//        dd(implode(',',\App\Currency::get()->pluck('id')->toArray()));
        $rules = ['name' => 'required', 'email' => 'required|email|unique:users', 'password' => 'required', 'phone' => 'required|unique:users', 'country' => 'required', 'address' => 'required', 'profile_image' => 'required'];
        $rules = array_merge($this->requiredParams, $rules);
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
            $input['password'] = Hash::make($request->password);
            $input['profile_image'] = parent::__uploadImage($request->profile_image, public_path('uploads/salon/profile_image'));
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

    public function Salonupdate(Request $request) {
        $user = \App\User::findOrFail(\Auth::id());
        if ($user->get()->isEmpty())
            return parent::error('User Not found');
        if ($user->hasRole('salon-admin') === false)
            return parent::error('Please use valid token');
        $rules = ['name' => '', 'password' => '', 'country' => '', 'address' => '', 'profile_image' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
            $input['password'] = Hash::make($request->password);
            if (isset($request->profile_image))
                $input['profile_image'] = parent::__uploadImage($request->profile_pic, public_path('uploads/salon/profile_image'));

            $user->fill($input);
            $user->save();
            return parent::successCreated(['Message' => 'Updated Successfully', 'user' => $user]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function CustomerRegister(Request $request) {
        $rules = ['name' => 'required', 'email' => 'required|email|unique:users', 'password' => 'required', 'phone' => 'required|unique:users'];

        $rules = array_merge($this->requiredParams, $rules);
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
            $input['password'] = Hash::make($request->password);
            $user = \App\User::create($input);
            $user->assignRole(\App\Role::where('id', 3)->first()->name);
            $token = $user->createToken('netscape')->accessToken;
            // Add user device details for firbase
            parent::addUserDeviceData($user, $request);
            return parent::successCreated(['Message' => 'Created Successfully', 'token' => $token, 'user' => $user]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function CustomerUpdate(Request $request) {
        $user = \App\User::findOrFail(\Auth::id());
        if ($user->get()->isEmpty())
            return parent::error('User Not found');
        if ($user->hasRole('client') === false)
            return parent::error('Please use valid token');
        $rules = ['name' => '', 'email' => 'required|email|unique:users,email,' . \Auth::id(), 'password' => '', 'phone' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
            $input['password'] = Hash::make($request->password);
//            var_dump(json_decode($input['category_id']));    
//            dd('s');
            $user->fill($input);
            $user->save();
            return parent::successCreated(['Message' => 'Updated Successfully', 'user' => $user]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function Login(Request $request) {
        try {
            $rules = ['email' => 'required|email|exists:users,email', 'password' => 'required'];
            $rules = array_merge($this->requiredParams, $rules);
            $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), true);
            if ($validateAttributes):
                return $validateAttributes;
            endif;

            //parent::addUserDeviceData($user, $request);
            if (Auth::attempt(['email' => request('email'), 'password' => request('password')])):
                $user = \App\User::find(Auth::user()->id);
                $token = $user->createToken('netscape')->accessToken;
//                $user = $user->with('roles');
                // Add user device details for firbase
                return parent::successCreated(['message' => 'Login Successfully', 'token' => $token, 'user' => $user]);
            else:
                return parent::error("User credentials doesn't matched");
            endif;
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

}
