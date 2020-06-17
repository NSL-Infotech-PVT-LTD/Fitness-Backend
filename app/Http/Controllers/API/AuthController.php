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
        $rules = ['name' => 'required', 'email' => 'required|email|unique:users', 'password' => 'required', 'phone' => 'required|unique:users', 'location' => 'required', 'latitude' => 'required', 'longitude' => 'required', 'profile_image' => 'required', 'business_hour_starts' => 'required', 'business_hour_ends' => 'required', 'bio' => 'required', 'sport_id' => 'required', 'service_ids' => 'required', 'expertise_years' => 'required', 'hourly_rate' => 'required', 'profession' => 'required', 'experience_detail' => 'required', 'training_service_detail' => 'required', 'police_doc' => 'required'];
        $rules = array_merge($this->requiredParams, $rules);
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
//           $input['service_ids']= json_encode($request->service_ids);
            $input['password'] = Hash::make($request->password);
            $input['is_notify'] = '1';
            $input['is_login'] = '1';
//            $input['sport_id']= json_encode($request->sport_id);
            $input['profile_image'] = parent::__uploadImage($request->file('profile_image'), public_path('uploads/coach/profile_image'), true);
            $input['police_doc'] = parent::__uploadImage($request->file('police_doc'), public_path('uploads/coach/police_doc'), false);


            $user = \App\User::create($input);
            //Assign role to created user[1=>10,2=>20,]

            self::addservices($request->service_ids, $user->id);



            $user->assignRole(\App\Role::where('id', 2)->first()->name);
            // create user token for authorization
            $token = $user->createToken('netscape')->accessToken;

//            testing comment
            // Add user device details for firbase
            parent::addUserDeviceData($user, $request);
            return parent::successCreated(['message' => 'Please wait while Admin will approve your account']);
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
        $rules = ['name' => '', 'phone' => 'unique:users,phone,' . $user->id, 'location' => '', 'latitude' => '', 'longitude' => '', 'profile_image' => '', 'business_hour_starts' => '', 'business_hour_ends' => '', 'bio' => '', 'service_ids' => '', 'expertise_years' => '', 'hourly_rate' => '', 'profession' => '', 'experience_detail' => '', 'training_service_detail' => '', 'police_doc' => '', 'sport_id' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
//            $input['sport_id']= json_encode($request->sport_id);
            if (isset($request->profile_image))
                $input['profile_image'] = parent::__uploadImage($request->file('profile_image'), public_path('uploads/coach/profile_image'), true);

            if (isset($request->police_doc))
                $input['police_doc'] = parent::__uploadImage($request->file('police_doc'), public_path('uploads/coach/police_doc'), false);


            if (isset($request->service_ids))
                self::addservices($request->service_ids, $user->id);
//            add service module end
            $user->fill($input);
            $user->save();

            $user = \App\User::whereId($user->id)->select('id', 'name', 'email', 'phone', 'location', 'latitude', 'longitude', 'business_hour_starts', 'business_hour_ends', 'bio', 'service_ids', 'expertise_years', 'hourly_rate', 'profile_image', 'sport_id', 'profession', 'experience_detail', 'training_service_detail', 'police_doc')->first();
            return parent::successCreated(['message' => 'Updated Successfully', 'user' => $user]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function AtheleteRegister(Request $request) {
        $rules = ['name' => 'required', 'email' => 'required|email|unique:users', 'password' => 'required', 'phone' => 'required|unique:users', 'address' => 'required', 'latitude' => 'required', 'longitude' => 'required', 'profile_image' => 'required', 'sport_id' => '', 'achievements' => 'required', 'experience_detail' => 'required'];

        $rules = array_merge($this->requiredParams, $rules);
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
//            $input['sport_id']= json_encode($request->sport_id);
            $input['password'] = Hash::make($request->password);
            $input['profile_image'] = parent::__uploadImage($request->file('profile_image'), public_path('uploads/athlete/profile_image'), true);
            $input['is_notify'] = '1';
            $input['state'] = '1';
            $user = \App\User::create($input);
            $user->assignRole(\App\Role::where('id', 3)->first()->name);
            $token = $user->createToken('netscape')->accessToken;
            // Add user device details for firbase
            parent::addUserDeviceData($user, $request);
            return parent::successCreated(['message' => 'Created Successfully', 'token' => $token, 'user' => $user]);
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
        $rules = ['name' => '', 'phone' => 'unique:users,phone,' . $user->id, 'address' => '', 'latitude' => '', 'longitude' => '', 'profile_image' => '','sport_id'=>''];
      
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
//            $input['sport_id']= json_encode($request->sport_id);
            if (isset($request->profile_image))
                $input['profile_image'] = parent::__uploadImage($request->file('profile_image'), public_path('uploads/athlete/profile_image'), true);
//            var_dump(json_decode($input['category_id']));
//            dd('s');
            $user->fill($input);
            $user->save();
            $user = \App\User::whereId($user->id)->select('id', 'name', 'email', 'phone', 'address', 'latitude', 'longitude', 'profile_image', 'sport_id', 'achievements', 'experience_detail')->first();
            return parent::successCreated(['message' => 'Updated Successfully', 'user' => $user]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function OrganiserRegister(Request $request) {
        $rules = ['name' => 'required', 'email' => 'required|email|unique:users', 'password' => 'required', 'phone' => 'required|unique:users', 'location' => 'required', 'latitude' => 'required', 'longitude' => 'required', 'profile_image' => 'required', 'business_hour_starts' => 'required', 'business_hour_ends' => 'required', 'bio' => 'required', 'service_ids' => 'required', 'expertise_years' => 'required', 'hourly_rate' => 'required', 'portfolio_image_1' => '', 'portfolio_image_2' => '', 'portfolio_image_3' => '', 'portfolio_image_4' => '', 'experience_detail' => 'required', 'training_service_detail' => 'required', 'police_doc' => 'required', 'sport_id' => 'required'];

        $rules = array_merge($this->requiredParams, $rules);
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
            $input['password'] = Hash::make($request->password);
            $input['profile_image'] = parent::__uploadImage($request->file('profile_image'), public_path('uploads/organiser/profile_image'), true);
            $input['police_doc'] = parent::__uploadImage($request->file('police_doc'), public_path('uploads/organiser/police_doc'), false);
            $input['is_notify'] = '1';
            $input['is_login'] = '1';
            $portfolio_image = [];
            if (!isset($request->portfolio_image_1) && !isset($request->portfolio_image_2) && !isset($request->portfolio_image_3) && !isset($request->portfolio_image_4)):
                return parent::error('Please upload any one portfolio image ');
            endif;
            for ($i = 1; $i <= 4; $i++):
                $var = 'portfolio_image_' . $i;
                if (isset($request->$var))
                    $input[$var] = parent::__uploadImage($request->file($var), public_path('uploads/organiser/portfolio_image'));
            endfor;

//            if (count($portfolio_image) > 0)
//                $input['portfolio_image'] = json_encode($portfolio_image);
            $user = \App\User::create($input);
            //add service module start
            self::addservices($request->service_ids, $user->id);
            //add service module end
            $user->assignRole(\App\Role::where('id', 4)->first()->name);
            $token = $user->createToken('netscape')->accessToken;
            // Add user device details for firbase
            parent::addUserDeviceData($user, $request);
            return parent::successCreated(['message' => 'Please wait while Admin will approve your account']);
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
        $rules = ['name' => '', 'phone' => 'unique:users,phone,' . $user->id, 'location' => '', 'latitude' => '', 'longitude' => '', 'profile_image' => '', 'business_hour_starts' => '', 'business_hour_ends' => '', 'bio' => '', 'service_ids' => '', 'expertise_years' => '', 'hourly_rate' => '', 'portfolio_image_1' => '', 'portfolio_image_2' => '', 'portfolio_image_3' => '', 'portfolio_image_4' => '', 'experience_detail' => '', 'training_service_detail' => '', 'police_doc' => '', 'sport_id' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
            if (isset($request->profile_image))
                $input['profile_image'] = parent::__uploadImage($request->file('profile_image'), public_path('uploads/organiser/profile_image'), false);

            if (isset($request->police_doc))
                $input['police_doc'] = parent::__uploadImage($request->file('police_doc'), public_path('uploads/organiser/police_doc'));
//            var_dump(json_decode($input['category_id']));
//            dd('s');
            if (isset($request->portfolio_image))
                $portfolio_image = [];

            for ($i = 1; $i <= 4; $i++):

                $var = 'portfolio_image_' . $i;
                if (isset($request->$var)):
                    $input[$var] = parent::__uploadImage($request->file($var), public_path('uploads/organiser/portfolio_image'), true);
                endif;


            endfor;

//            if (count($portfolio_image) > 0)
//                $input['portfolio_image'] = json_encode($portfolio_image);
//
            $user->fill($input);
            $user->save();
            //add service module start
            if (isset($request->service_ids))
                self::addservices($request->service_ids, $user->id);
            //add service module end
            $user = \App\User::whereId($user->id)->select('id', 'name', 'email', 'phone', 'location', 'latitude', 'longitude', 'bio', 'service_ids', 'expertise_years', 'hourly_rate', 'business_hour_starts', 'business_hour_ends', 'portfolio_image_1', 'portfolio_image_2', 'portfolio_image_3', 'portfolio_image_4', 'profile_image', 'experience_detail', 'training_service_detail', 'police_doc', 'sport_id')->first();
            return parent::successCreated(['message' => 'Updated Successfully', 'user' => $user]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function Login(Request $request) {
        try {
            $rules = ['email' => 'required', 'password' => 'required'];
            $rules = array_merge($this->requiredParams, $rules);
            $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), true);
            if ($validateAttributes):
                return $validateAttributes;
            endif;

            //parent::addUserDeviceData($user, $request);
            if (Auth::attempt(['email' => request('email'), 'password' => request('password')])):
                if (\App\User::whereId(Auth::user()->id)->where('state', '1')->get()->isEmpty()):
                    return parent::error("Your account has not activated yet");
                endif;
                $user = \App\User::find(Auth::user()->id);
                $user->is_login = '1';
                $user->save();
                $token = $user->createToken('netscape')->accessToken;


                parent::addUserDeviceData($user, $request);


//                $user = $user->with('roles');
                // Add user device details for firbase
                
                 
                $getUserStripe= \App\Stripe::where('user_id',$user->id)->first();
              
                return parent::successCreated(['message' => 'Login Successfully', 'token' => $token, 'user' => $user,'stripeDetails'=>$getUserStripe]);
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
                    $message->subject(trans('admin/users/index'));
                });
//        dd($response);
        switch ($response) {
            case Password::RESET_LINK_SENT:
                return parent::successCreated('Password reset link sent please check inbox');
            case Password::INVALID_USER:
                return parent::error(trans($response));
            default :
                return parent::error(trans($response));
                break;
        }
        return parent::error('Something Went');
    }

    public function getOrganisers(Request $request) {
        $rules = ['search' => '', 'limit' => '', 'order_by' => ''];
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
                    ->leftJoin('bookings', 'bookings.owner_id', '=', 'users.id')
                    ->select('users.id', 'users.name', 'users.email', 'users.phone', 'users.created_at', 'users.location', 'users.latitude', 'users.longitude', 'users.profile_image', 'users.business_hour_starts', 'users.business_hour_ends', 'users.bio', 'users.expertise_years', 'users.hourly_rate', 'users.portfolio_image_1', 'users.portfolio_image_2', 'users.portfolio_image_3', 'users.portfolio_image_4', 'users.service_ids', 'users.sport_id', 'users.experience_detail', 'users.training_service_detail', 'users.police_doc', 'users.state', \DB::raw('AVG(bookings.rating) as rating'));
            $model = $model->groupBy('users.id');
            $perPage = isset($request->limit) ? $request->limit : 20;
            if (isset($request->search)) {
                $model = $model->where(function($query) use ($request) {
                    $query->where('name', 'LIKE', "%$request->search%")
                            ->orWhere('sport_id', 'LIKE', "%$request->search%");
                });
            }
            switch ($request->order_by):
                case 'latest':
                    $model = $model->orderBy('created_at', 'desc');
                    break;
                case 'rating':
                    $model = $model->orderBy('rating', 'desc');
                default :
                    $model = $model->orderBy('created_at', 'desc');
                    break;
            endswitch;

//            if ($request->order_by == 'rating')
//                $model = $model->orderBy('rating', 'desc');
            $model = $model->where('users.state', '1');

            return parent::success($model->paginate($perPage));
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getCoaches(Request $request) {
        $rules = ['search' => '', 'limit' => '', 'order_by' => ''];
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
                    ->leftJoin('bookings', 'bookings.owner_id', '=', 'users.id')
                    ->Select('users.id', 'users.name', 'users.email', 'users.phone', 'users.location', 'users.latitude', 'users.longitude', 'users.profile_image', 'users.business_hour_starts', 'users.business_hour_ends', 'users.bio', 'users.expertise_years', 'users.sport_id', 'users.hourly_rate', 'users.service_ids', 'users.profession', 'users.experience_detail', 'users.training_service_detail', 'users.police_doc', 'users.state', \DB::raw('AVG(bookings.rating) as rating'));
            $model = $model->groupBy('users.id');

            $perPage = isset($request->limit) ? $request->limit : 20;
            if (isset($request->search)) {
                $model = $model->where(function($query) use ($request) {
                    $query->where('name', 'LIKE', "%$request->search%")
                            ->orWhere('sport_id', 'LIKE', "%$request->search%");
                });
            }
//            dd($model);

            switch ($request->order_by):
                case 'latest':
                    $model = $model->orderBy('users.created_at', 'desc');
                    break;
                case 'rating':
                    $model = $model->orderBy('rating', 'desc');
                default :
                    $model = $model->orderBy('users.created_at', 'desc');
                    break;
            endswitch;

//            if ($request->order_by == 'rating')
//                $model = $model->orderBy('rating', 'desc');
            $model = $model->where('users.state', '1');
//
            return parent::success($model->paginate($perPage));
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getcoach(Request $request) {

        $rules = [];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
           
            $user = \App\User::findOrFail(\Auth::id());
            if ($user->hasRole('coach') === false)
                return parent::error('Please use valid auth token');

            $model = new \App\User();
            $roleusersSA = \DB::table('role_user')->where('role_id', \App\Role::where('name', 'coach')->first()->id)->pluck('user_id');
            $model = $model->wherein('users.id', $roleusersSA)
                    ->leftJoin('bookings', 'bookings.owner_id', '=', 'users.id')
                    ->Select('users.id', 'users.name', 'users.email', 'users.phone', 'users.location', 'users.latitude', 'users.longitude', 'users.profile_image', 'users.business_hour_starts', 'users.business_hour_ends', 'users.bio', 'users.expertise_years', 'users.sport_id', 'users.hourly_rate', 'users.service_ids', 'users.profession', 'users.experience_detail', 'users.training_service_detail', 'users.police_doc', \DB::raw('AVG(bookings.rating) as rating'));
            $model = $model->groupBy('users.id');
            $model = $model->where('users.id', \Auth::id());
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
    
     public function getcoachdetail(Request $request) {
         
  
        $rules = ['id'=>'required'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
           
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            
            $model = new \App\User();
            $roleusersSA = \DB::table('role_user')->where('role_id', \App\Role::where('name', 'coach')->first()->id)->pluck('user_id');
            $model = $model->wherein('users.id', $roleusersSA)
                    ->leftJoin('bookings', 'bookings.owner_id', '=', 'users.id')
                    ->Select('users.id', 'users.name', 'users.email', 'users.phone', 'users.location', 'users.latitude', 'users.longitude', 'users.profile_image', 'users.business_hour_starts', 'users.business_hour_ends', 'users.bio', 'users.expertise_years', 'users.sport_id', 'users.hourly_rate', 'users.service_ids', 'users.profession', 'users.experience_detail', 'users.training_service_detail', 'users.police_doc', \DB::raw('AVG(bookings.rating) as rating'));
            $model = $model->groupBy('users.id');
             $model = $model->where('users.id', $request->id);
          
          
            return parent::success($model->first());
        } catch (\Exception $ex) {

            return parent::error($ex->getMessage());
        }
    }


    public function getorganiser(Request $request) {

        $rules = ['id' => 'required'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), true);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
            $user = \App\User::findOrFail(\Auth::id());
            if ($user->hasRole('organizer') === false)
                return parent::error('Please use valid auth token');
            $model = new \App\User();
          
            $roleusersSA = \DB::table('role_user')->where('role_id', \App\Role::where('name', 'organizer')->first()->id)->pluck('user_id');
            $model = $model->wherein('users.id', $roleusersSA)
                    ->leftJoin('bookings', 'bookings.owner_id', '=', 'users.id')
                    ->select('users.id', 'users.name', 'users.email', 'users.phone', 'users.created_at', 'users.location', 'users.latitude', 'users.longitude', 'users.profile_image', 'users.business_hour_starts', 'users.business_hour_ends', 'users.bio', 'users.expertise_years', 'users.hourly_rate', 'users.portfolio_image_1', 'users.portfolio_image_2', 'users.portfolio_image_3', 'users.portfolio_image_4', 'users.service_ids', 'users.sport_id', 'users.experience_detail', 'users.training_service_detail', 'users.police_doc', \DB::raw('AVG(bookings.rating) as rating'));
            $model = $model->groupBy('users.id');
            $model = $model->where('users.id', $request->id);
            if (isset($request->search))
                $model = $model->Where('name', 'LIKE', "%$request->search%")
                        ->orWhere('email', 'LIKE', "%$request->search%");
            $perPage = isset($request->limit) ? $request->limit : 20;
            return parent::success($model->paginate($perPage)->first());
        } catch (\Exception $ex) {

            return parent::error($ex->getMessage());
        }
    }

    /**
     * Reset the given user's password.
     * 
     * @param  ResetPasswordRequest  $request
     * @return Response
     */
    public function changePassword(Request $request) {
        $rules = ['old_password' => 'required', 'password' => 'required', 'password_confirmation' => 'required|same:password'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            if (\Hash::check($request->old_password, \Auth::User()->password)):
                $model = \App\User::find(\Auth::id());
                $model->password = \Hash::make($request->password);
                $model->save();
                return parent::success('Password Changed Successfully');
            else:
                return parent::error('Please use valid old password');
            endif;
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function logout(Request $request) {
        $rules = [];
//        $rules = array_merge($this->requiredParams, $rules);
        $validateAttributes = parent::validateAttributes($request, 'GET', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $user = \App\User::findOrFail(\Auth::id());
            $user->is_login = '0';
            $user->save();
            $device= \App\UserDevice::where('user_id', \Auth::id())->get();
//            dd($device);
             if ($device->isEmpty() === false)
            \App\UserDevice::destroy($device->first()->id);
            return parent::success('Logout Successfully');
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function toggleNotifyUser(Request $request) {
        $rules = [];
        $validateAttributes = parent::validateAttributes($request, 'GET', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $user = \App\User::findOrFail(\Auth::id());
            $user->is_notify = ((\App\User::whereId(\Auth::id())->first()->is_notify === '1') ? '0' : '1');
            $user->save();
            return parent::success('Notify Status Updated Successfully');
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function AuthCheck(Request $request) {
        $rules = ['type' => 'required|in:email,phone'];

        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $rules = ['data' => 'unique:users,' . $request->type];
            $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
            if ($validateAttributes):
                return $validateAttributes;
            endif;
            try {
                
            } catch (Exception $ex) {
                
            }
            return parent::success('It is not available in database');
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

}
