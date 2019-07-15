<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Role;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\portfolio;
use Twilio\Rest\Client;
use DB;

class UserController extends Controller {

    public function FreelancerRegister(Request $request) {
        $validator = Validator::make($request->all(), [
                    'firstname' => 'required',
                    'email' => 'required|email|unique:users',
                    'phone' => '',
                    'profile_pic' => 'required',
                    'category' => 'required',
                    'subcategory' => 'required',
                    'latitude' => 'required',
                    'longitude' => 'required',
                    'portfolio_image' => 'required',
                    'bio' => 'required',
                    'type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $roles = \App\Role::where('name', $request->type)->first();
       // dd($roles);
        if (!$roles) {
            return parent::error("type Not Found", 401);
        }
        $fileName = 'null';
        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $fileName = $file->getClientOriginalName();
            $destinationPath = public_path('images');
            $file->move($destinationPath, $fileName);
        }
        $portfolio = 'null';
        if (Input::hasFile('portfolio_image')) {
            $portfolio = uniqid() . '.' . Input::file('portfolio_image')->getClientOriginalExtension();
            Input::file('portfolio_image')->move(public_path('images'), $portfolio);
        }
        $input = $request->all();
        $input['profile_pic'] = $fileName;
        $input['portfolio_image'] = $portfolio;
        $user = \App\User::create($input);
        $token = $user->createToken('netscape')->accessToken;
       $UserRole = DB::table('role_user')->insert(['user_id' => $user->id, 'role_id' => $roles->id]);
        return parent::success($token, 200);
    }
    
    
    public function ClientRegister(Request $request) {
        $validator = Validator::make($request->all(), [
                    'firstname' => 'required',
                    'lastname' => 'required',
                    'email' => 'required|email|unique:users',
                    'phone' => '',
                    'profile_pic' => 'required',
                    'latitude' => 'required',
                    'longitude' => 'required',
                    'type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $roles = \App\Role::where('name', $request->type)->first();
        if (!$roles) {
            return parent::error("type Not Found", 401);
        }
        $fileName = 'null';
        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $fileName = $file->getClientOriginalName();
            $destinationPath = public_path('images');
            $file->move($destinationPath, $fileName);
        }
        $input = $request->all();
        $input['profile_pic'] = $fileName;
        $user = \App\User::create($input);
        $token = $user->createToken('netscape')->accessToken;
        $UserRole = DB::table('role_user')->insert(['user_id' => $user->id, 'role_id' => $roles->id]);
        return parent::success($token, 200);
    }
    
    
    
    
    

    /**
     * Handles Login Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request) {
        $credentials = [
            'email' => $request->email,
            'phone' => $request->phone
        ];
      
        if (auth()->attempt($credentials)) {
            $token = auth()->user()->createToken('netscape')->accessToken;
            return parent::success('login succesful', 200);
        } else {
            return parent::error("Wrong Username or phone", 401);
        }
    }

    public function FreelancerList(Request $request) {
        $role = \App\Role::where('name', $request->type)->first();
        $result = DB::table('users')
                ->join('role_user', 'users.id', '=', 'role_user.user_id')
                ->where('role_id', $role->id)
                ->get();
        if ($result) {
            return parent::success($result, 200);
        } else {

            return parent::error("Freelancer Not Found", 401);
        }
    }

    public function Portfolio(Request $request) {

        $validator = Validator::make($request->all(), [
                    'image' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $images = array();
        if ($request->hasFile('image')) {
            foreach ($request->file('image')as $file) {
                $fileName = $file->getClientOriginalName();
                $destinationPath = public_path('images');
                $file->move($destinationPath, $fileName);
                $images[] = $fileName;
                $requestData['image'] = $fileName;
                $requestData['user_id'] = Auth::id();
                $portfolio = portfolio::create($requestData);
            }

            if ($portfolio) {
                return parent::success('portfolio Created successfully', 200);
            } else {
                return parent::error(" Not Created", 401);
            }
        }
    }

    public function PhoneVerification() {

        $sid = "ACf724838aaac3765baf8c311311966ebb";
        $token = "21acece4743dd2741ad86161cf36afda";
        $twilio = new Client($sid, $token);
        $message = $twilio->messages
                ->create("9816642246",
                array(
                    "from" => "+17653798059",
                    "messagingServiceSid" => "SM87f64f49970a4aebb6f9c1dbeaec0332",
                    "body" => "message send in 2 minute"
                )
        );

        print($message->sid);
    }

    public function connectLinkedIn() {
        
    }

}
