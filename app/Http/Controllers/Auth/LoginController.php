<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller {
    /*
      |--------------------------------------------------------------------------
      | Login Controller
      |--------------------------------------------------------------------------
      |
      | This controller handles authenticating users for the application and
      | redirecting them to your home screen. The controller uses a trait
      | to conveniently provide its functionality to your applications.
      |
     */

use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
//        dd(\Auth::check());
//        
//        $this->middleware('guest')->except('logout');
    }

    public function salonAdminCheckAuth(Request $request) {
        $userdata = array(
            'email' => $request->email,
            'password' => $request->password
        );
        // attempt to do the login
        if (\Auth::attempt($userdata)) {
            return redirect()->route('salon-admin.dashboard');
        }
    }

}
