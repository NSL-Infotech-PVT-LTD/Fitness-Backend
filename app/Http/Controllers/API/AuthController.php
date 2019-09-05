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

class AuthController extends ApiController {

    public function CoachRegister(Request $request) {
//        dd(implode(',',\App\Currency::get()->pluck('id')->toArray()));
        $rules = ['name' => 'required', 'email' => 'required|email|unique:users', 'password' => 'required', 'phone' => 'required|unique:users', 'location' => 'required','latitude'=>'required','longitude'=>'required' ,'profile_image' => 'required', 'business_hour' => 'required', 'bio' => 'required', 'service_ids' => 'required', 'expertise_years' => 'required', 'hourly_rate' => 'required'];
        $rules = array_merge($this->requiredParams, $rules);
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
            $input['password'] = Hash::make($request->password);
            $input['profile_image'] = parent::__uploadImage($request->file('profile_image'), public_path('uploads/coach/profile_image'));
               
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

    public function CoachUpdate(Request $request) {
        $user = \App\User::findOrFail(\Auth::id());
        if ($user->get()->isEmpty())
            return parent::error('User Not found');
        if ($user->hasRole('coach') === false)
            return parent::error('Please use valid token');
        $rules = ['name' => '', 'password' => '', 'phone' => '', 'location' => '','latitude' => '', 'longitude' => '','profile_image' => '', 'business_hour' => '', 'bio' => '', 'service_ids' => 'required', 'expertise_years' => '', 'hourly_rate' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
            $input['password'] = Hash::make($request->password);
            
            $input['profile_image'] = parent::__uploadImage($request->file('profile_image'), public_path('uploads/coach/profile_image'));

            $user->fill($input);
            $user->save();
            return parent::successCreated(['Message' => 'Updated Successfully', 'user' => $user]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function AtheleteRegister(Request $request) {
        $rules = ['name' => 'required', 'email' => 'required|email|unique:users', 'password' => 'required', 'phone' => 'required|unique:users', 'address' => 'required','latitude'=>'required','longitude'=>'required'];

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

    public function AtheleteUpdate(Request $request) {
        $user = \App\User::findOrFail(\Auth::id());
        if ($user->get()->isEmpty())
            return parent::error('User Not found');
        if ($user->hasRole('athlete') === false)
            return parent::error('Please use valid token');
        $rules = ['name' => '', 'password' => '', 'phone' => '', 'address' => '','latitude'=>'','longitude'=>''];
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

    public function OrganiserRegister(Request $request) {
        $rules = ['name' => 'required', 'email' => 'required|email|unique:users', 'password' => 'required', 'phone' => 'required|unique:users', 'location' => 'required','latitude'=>'required','longitude'=>'required' ,'profile_image' => 'required', 'business_hour' => 'required','bio' => 'required', 'service_ids' => 'required', 'expertise_years' => 'required', 'hourly_rate' => 'required','portfolio_image_1' => 'required', 'portfolio_image_2' => '', 'portfolio_image_3' => '', 'portfolio_image_4' => ''];

        $rules = array_merge($this->requiredParams, $rules);
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
            $input['password'] = Hash::make($request->password);
            $input['profile_image'] = parent::__uploadImage($request->file('profile_image'), public_path('uploads/organiser/profile_image'));
             $portfolio_image =[];
                
                for($i=1;$i<=4;$i++):
                    $var = 'portfolio_image_'.$i;
                if(isset($var))
                    $portfolio_image[] = parent::__uploadImage($request->file($var), public_path('uploads/organiser/portfolio_image'));
                endfor;
                
                if(count($portfolio_image)>0)
                    $input['portfolio_image'] = json_encode($portfolio_image);
            $user = \App\User::create($input);
            $user->assignRole(\App\Role::where('id', 4)->first()->name);
            $token = $user->createToken('netscape')->accessToken;
            // Add user device details for firbase
            parent::addUserDeviceData($user, $request);
            return parent::successCreated(['Message' => 'Created Successfully', 'token' => $token, 'user' => $user]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function OrganiserUpdate(Request $request) {
        $user = \App\User::findOrFail(\Auth::id());
        if ($user->get()->isEmpty())
            return parent::error('User Not found');
        if ($user->hasRole('organizer') === false)
            return parent::error('Please use valid token');
        $rules = ['name' => '', 'password' => '', 'phone' => '', 'location' => '', 'latitude'=>'','longitude'=>'','profile_image' => '', 'business_hour' => '','bio' => 'required', 'service_ids' => '', 'expertise_years' => '', 'hourly_rate' => '','portfolio_image_1' => '', 'portfolio_image_2' => '', 'portfolio_image_3' => '', 'portfolio_image_4' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
            $input['password'] = Hash::make($request->password);
            if (isset($request->profile_image))
                $input['profile_image'] = parent::__uploadImage($request->file('profile_image'), public_path('uploads/organiser/profile_image'));
//            var_dump(json_decode($input['category_id']));    
//            dd('s');
            if (isset($request->profile_image))
                $input['profile_image'] = parent::__uploadImage($request->file('profile_image'), public_path('uploads/organiser/profile_image'));
            $portfolio_image =[];
                
                for($i=1;$i<=4;$i++):
                    $var = 'portfolio_image_'.$i;
                if(isset($var))
                    $portfolio_image[] = parent::__uploadImage($request->file($var), public_path('uploads/organiser/portfolio_image'));
                endfor;
                
                if(count($portfolio_image)>0)
                    $input['portfolio_image'] = json_encode($portfolio_image);
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

    public function resetPassword(Request $request, Factory $view) {
        //Validating attributes
        $rules = ['email' => 'required'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), true);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        $view->composer('emails.auth.password', function($view) {
            $view->with([
                'title' => trans('front/password.email-title'),
                'intro' => trans('front/password.email-intro'),
                'link' => trans('front/password.email-link'),
                'expire' => trans('front/password.email-expire'),
                'minutes' => trans('front/password.minutes'),
            ]);
        });
//        dd($request->only('email'));
        $response = Password::sendResetLink($request->only('email'), function (Message $message) {
                    $message->subject(trans('front/password.reset'));
                });
//        dd($response);
        switch ($response) {
            case Password::RESET_LINK_SENT:
                return parent::successCreated('Password sent please check inbox');
            case Password::INVALID_USER:
                return parent::error(trans($response));
            default :
                return parent::error(trans($response));
                break;
        }
        return parent::error('Something Went');
    }

}
