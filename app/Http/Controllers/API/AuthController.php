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

    private function addservices($service_ids, $userId) {
//        dd($service_ids);
        foreach (json_decode($service_ids) as $service):
            $userservice = \App\UserService::where('service_id', $service->id)->where('user_id', $userId)->get();
            if ($userservice->isEmpty())
                $userservice = new \App\UserService();
            else
                $userservice = \App\UserService::findOrFail($userservice->first()->id);

//            dd($userservice);
            $userservice->service_id = $service->id;
            $userservice->price = $service->price;
            $userservice->user_id = $userId;
            $userservice->save();
        endforeach;
    }

    public function CoachRegister(Request $request) {
//        dd(implode(',',\App\Currency::get()->pluck('id')->toArray()));
        $rules = ['name' => 'required', 'email' => 'required|email|unique:users', 'password' => 'required', 'phone' => 'required|unique:users', 'location' => 'required', 'latitude' => 'required', 'longitude' => 'required', 'profile_image' => 'required', 'business_hour_starts' => 'required', 'business_hour_ends' => 'required', 'bio' => 'required', 'sport_id'=>'required','service_ids' => 'required', 'expertise_years' => 'required', 'hourly_rate' => 'required','profession'=>'required','experience_detail'=>'required','training_service_detail'=>'required'];
        $rules = array_merge($this->requiredParams, $rules);
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
//            dd(json_decode($request->service_ids));
            $input['password'] = Hash::make($request->password);
            $input['sport_id']= json_encode($request->sport_id);
            $input['profile_image'] = parent::__uploadImage($request->file('profile_image'), public_path('uploads/coach/profile_image'),true);

            $user = \App\User::create($input);
            //Assign role to created user[1=>10,2=>20,]

            self::addservices($request->service_ids, $user->id);

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
        $rules = ['name' => '', 'phone' => '', 'location' => '', 'latitude' => '', 'longitude' => '', 'profile_image' => '', 'business_hour_starts' => '', 'business_hour_ends' => '', 'bio' => '', 'service_ids' => '', 'expertise_years' => '', 'hourly_rate' => '','profession'=>'','experience_detail'=>'','training_service_detail'=>''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
            $input['sport_id']= json_encode($request->sport_id);
            if (isset($request->profile_image))
            $input['profile_image'] = parent::__uploadImage($request->file('profile_image'), public_path('uploads/coach/profile_image'),true);


            if (isset($request->service_ids))
                self::addservices($request->service_ids, $user->id);
//            add service module end
            $user->fill($input);
            $user->save();
            parent::addUserDeviceData($user, $request);
            $user = \App\User::whereId($user->id)->select('id','name','email','phone','location','latitude','longitude','business_hour_starts','business_hour_ends','bio','service_ids','expertise_years','hourly_rate','profile_image','sport_id','profession','experience_detail','training_service_detail')->first();
            return parent::successCreated(['Message' => 'Updated Successfully', 'user' => $user]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function AtheleteRegister(Request $request) {
        $rules = ['name' => 'required', 'email' => 'required|email|unique:users', 'password' => 'required', 'phone' => 'required|unique:users', 'address' => 'required', 'latitude' => 'required', 'longitude' => 'required', 'profile_image' => 'required','sport_id'=>'','achievements'=>'required','experience_detail'=>'required'];

        $rules = array_merge($this->requiredParams, $rules);
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
            $input['sport_id']= json_encode($request->sport_id);
            $input['password'] = Hash::make($request->password);
            $input['profile_image'] = parent::__uploadImage($request->file('profile_image'), public_path('uploads/athlete/profile_image'),true);
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
        $rules = ['name' => '', 'phone' => '', 'address' => '', 'latitude' => '', 'longitude' => '', 'profile_image' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
            $input['sport_id']= json_encode($request->sport_id);
            if (isset($request->profile_image))
            $input['profile_image'] = parent::__uploadImage($request->file('profile_image'), public_path('uploads/athlete/profile_image'),true);
//            var_dump(json_decode($input['category_id']));
//            dd('s');
            $user->fill($input);
            $user->save();
            $user = \App\User::whereId($user->id)->select('id','name','email','phone','address','latitude','longitude','profile_image','sport_id','achievements','experience_detail')->first();
            return parent::successCreated(['Message' => 'Updated Successfully', 'user' => $user]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function OrganiserRegister(Request $request) {
        $rules = ['name' => 'required', 'email' => 'required|email|unique:users', 'password' => 'required', 'phone' => 'required|unique:users', 'location' => 'required', 'latitude' => 'required', 'longitude' => 'required', 'profile_image' => 'required', 'business_hour_starts' => 'required', 'business_hour_ends' => 'required', 'bio' => 'required', 'service_ids' => 'required', 'expertise_years' => 'required', 'hourly_rate' => 'required', 'portfolio_image_1' => 'required', 'portfolio_image_2' => '', 'portfolio_image_3' => '', 'portfolio_image_4' => '','experience_detail'=>'required','training_service_detail'=>'required'];

        $rules = array_merge($this->requiredParams, $rules);
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
            $input['password'] = Hash::make($request->password);
            $input['profile_image'] = parent::__uploadImage($request->file('profile_image'), public_path('uploads/organiser/profile_image'),true);
            $portfolio_image = [];

            for ($i = 1; $i <= 4; $i++):
                $var = 'portfolio_image_' . $i;
                if (isset($request->$var))
                    $portfolio_image[] = parent::__uploadImage($request->file($var), public_path('uploads/organiser/portfolio_image'),true);
            endfor;

            if (count($portfolio_image) > 0)
                $input['portfolio_image'] = json_encode($portfolio_image);
            $user = \App\User::create($input);
            //add service module start
            self::addservices($request->service_ids, $user->id);
            //add service module end
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
        $rules = ['name' => '','phone' => '', 'location' => '', 'latitude' => '', 'longitude' => '', 'profile_image' => '', 'business_hour_starts' => '', 'business_hour_ends' => '', 'bio' => 'required', 'service_ids' => '', 'expertise_years' => '', 'hourly_rate' => '', 'portfolio_image_1' => '', 'portfolio_image_2' => '', 'portfolio_image_3' => '', 'portfolio_image_4' => '','experience_detail'=>'','training_service_detail'=>''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
            if (isset($request->profile_image))
                $input['profile_image'] = parent::__uploadImage($request->file('profile_image'), public_path('uploads/organiser/profile_image'),true);
//            var_dump(json_decode($input['category_id']));
//            dd('s');

            $portfolio_image = [];

            for ($i = 1; $i <= 4; $i++):
                $var = 'portfolio_image_' . $i;
                if (isset($request->$var))
                    $portfolio_image[] = parent::__uploadImage($request->file($var), public_path('uploads/organiser/portfolio_image'),true);
            endfor;

            if (count($portfolio_image) > 0)
                $input['portfolio_image'] = json_encode($portfolio_image);

//
            $user->fill($input);
            $user->save();
            //add service module start
            if (isset($request->service_ids))
                self::addservices($request->service_ids, $user->id);
            //add service module end
            $user = \App\User::whereId($user->id)->select('id','name','email','phone','location','latitude','longitude','bio','service_ids','expertise_years','hourly_rate','business_hour_starts','business_hour_ends','portfolio_image','profile_image','experience_detail','training_service_detail')->first();
            return parent::successCreated(['Message' => 'Updated Successfully', 'user' =>$user]);
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
                parent::addUserDeviceData($user, $request);
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

    public function getOrganisers(Request $request) {
        $rules = ['search' => '', 'limit' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
            $user = \App\User::findOrFail(\Auth::id());
            if ($user->hasRole('athlete') === false)
                return parent::error('Please use valid token');
            $model = new \App\User();
            $roleusersSA = \DB::table('role_user')->where('role_id', \App\Role::where('name', 'organizer')->first()->id)->pluck('user_id');
            $model = $model->wherein('users.id', $roleusersSA)
                    ->Select('id', 'name', 'email', 'phone', 'location', 'latitude', 'longitude', 'profile_image', 'business_hour_starts', 'business_hour_ends', 'bio', 'expertise_years', 'hourly_rate', 'portfolio_image', 'service_ids','sport_id','experience_detail','training_service_detail');

            $perPage = isset($request->limit) ? $request->limit : 20;
            if (isset($request->search))
                $model = $model->Where('name', 'LIKE', "%$request->search%");
            return parent::success($model->paginate($perPage));
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getCoaches(Request $request) {
        $rules = ['search' => '', 'limit' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
            $user = \App\User::findOrFail(\Auth::id());
            if ($user->hasRole('athlete') === false)
                return parent::error('Please use valid token');
            $model = new \App\User();
            $roleusersSA = \DB::table('role_user')->where('role_id', \App\Role::where('name', 'coach')->first()->id)->pluck('user_id');
            $model = $model->wherein('users.id', $roleusersSA)
                    ->Select('id', 'name', 'email', 'phone', 'location', 'location', 'latitude', 'longitude', 'profile_image', 'business_hour_starts', 'business_hour_ends', 'bio', 'expertise_years', 'sport_id','hourly_rate', 'service_ids','profession','experience_detail','training_service_detail');
            $perPage = isset($request->limit) ? $request->limit : 20;
            if (isset($request->search))
                $model = $model->Where('name', 'LIKE', "%$request->search%")
                    ->orWhere('email', 'LIKE', "%$request->search%")
                    ->orWhere('sport_id', 'LIKE', "%$request->search%");
            return parent::success($model->paginate($perPage));
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }


    public function getcoach(Request $request)
    {

        $rules = ['id' => 'required','search'=>'','limit'=>''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), true);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
            $user = \App\User::findOrFail(\Auth::id());
            if ($user->hasRole('athlete') === false)
                return parent::error('Please use valid token');

            $model = new \App\User();
            $roleusersSA = \DB::table('role_user')->where('role_id', \App\Role::where('name', 'coach')->first()->id)->pluck('user_id');
            $model = $model->wherein('users.id', $roleusersSA)
                ->Select('id', 'name', 'email', 'phone', 'location', 'location', 'latitude', 'longitude', 'profile_image', 'business_hour_starts', 'business_hour_ends', 'bio', 'expertise_years','sport_id', 'hourly_rate', 'portfolio_image', 'service_ids','profession','experience_detail','training_service_detail');
            $model = $model->where('id', $request->id);
            if (isset($request->search))
                $model = $model->Where('name', 'LIKE', "%$request->search%")
                    ->orWhere('email', 'LIKE', "%$request->search%")
                    ->orWhere('sport_id', 'LIKE', "%$request->search%");

            $perPage = isset($request->limit) ? $request->limit : 20;
            return parent::success($model->paginate($perPage)->first());

        } catch (\Exception $ex) {

            return parent::error($ex->getMessage());
        }

    }


    public function getorganiser(Request $request)
    {

        $rules = ['id' => 'required','search'=>'','limit'=>''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), true);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
            $user = \App\User::findOrFail(\Auth::id());
            if ($user->hasRole('athlete') === false)
                return parent::error('Please use valid token');
            $model = new \App\User();
            $model = new \App\User();
            $roleusersSA = \DB::table('role_user')->where('role_id', \App\Role::where('name', 'organizer')->first()->id)->pluck('user_id');
            $model = $model->wherein('users.id', $roleusersSA)
                ->Select('id', 'name', 'email', 'phone', 'location', 'location', 'latitude', 'longitude', 'profile_image', 'business_hour_starts', 'business_hour_ends', 'bio', 'expertise_years', 'hourly_rate', 'portfolio_image', 'service_ids','experience_detail','training_service_detail');
            $model = $model->where('id', $request->id);
            if (isset($request->search))
                $model = $model->Where('name', 'LIKE', "%$request->search%")
                    ->orWhere('email', 'LIKE', "%$request->search%");
            $perPage = isset($request->limit) ? $request->limit : 20;
            return parent::success($model->paginate($perPage)->first());
        } catch (\Exception $ex) {

            return parent::error($ex->getMessage());
        }

    }

}
