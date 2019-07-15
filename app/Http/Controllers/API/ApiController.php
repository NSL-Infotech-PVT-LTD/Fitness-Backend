<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Validator;
use App\User;
use Illuminate\Support\Facades\Auth;
use Twilio\Rest\Client;
//fcm
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class ApiController extends \App\Http\Controllers\Controller {

    /**
     * Create admin controller instance.
     *
     * @return void
     */
    public function __construct() {
//        $this->middleware('auth');
//        $roles = implode('|', Role::all()->pluck('name')->toArray());
//        $this->middleware(['role:' . $roles, 'auth:admin']);
//        dd($roles);
    }

    private function _headers() {
        return getallheaders();
    }

    protected function __allowedUsers() {
        $userRole = \App\User::find($this->_headers()['user_id'])->getRoleNames()['0'];
        return \App\User::role($userRole)->get()->pluck('id')->toArray();
    }

    public $successStatus = 200;
    public static $locale = '';
//    public $requiredParams = ['device_id' => 'required', 'device_token' => 'required', 'device_type' => 'in:ios,android|required', 'client_id' => 'required', 'client_secret' => 'required'];
    public $requiredParams = ['device_id' => 'required', 'device_type' => 'in:ios,android|required', 'client_id' => 'required', 'client_secret' => 'required'];
    protected static $_allowedURIwithoutAuth = ['api/login', 'api/customer/login', 'api/configuration/{type}', 'api/customer/verify-login', 'api/customer/registeration', 'api/customer/resend-otp'];

    public static function validateClientSecret() {
        $headers = getallheaders();
        if (!isset($headers['client_id']) || !isset($headers['client_secret'])):
            return self::error('Client Id and Secret not found.', 422);
        endif;
        $response = self::validateClient($headers['client_id'], $headers['client_secret']);
        if ($response === false) :
            return self::error('Client Id and Secret mismatched.', 409);
        endif;
//        dd(\Request::route()->uri());
        if (!in_array(\Request::route()->uri(), self::$_allowedURIwithoutAuth)):
            if (!isset($headers['user_id'])):
                return self::error('Loged in User Id is required', 422);
            else:
                $user = User::find($headers['user_id']);
                if ($user === null)
                    return self::error('Loged in User Not found', 401);
//                dd($user->hasAnyRole('super admin'));
                if ($user->hasPermissionTo(\Request::route()->uri()) === false):
                    return self::error("You're not authorized to do, Please contact administrator", 403);
                endif;
            endif;
        endif;
        if (isset($headers['locale'])):
            if (!in_array($headers['locale'], ['', 'kr', 'ar'])):
                return self::error('Please use valid language.', 422);
            endif;
            self::$locale = $headers['locale'];
        endif;
        return false;
    }

    protected static function validateClient($client_id, $client_secret) {
        $check = \App\Models\OauthClients::where(["id" => $client_id, "secret" => $client_secret]);
        if ($check->exists())
            return true;
        else
            return false;
    }

    protected static function validateHeadersOnly($request, $formType = 'GET', $attributeValidate = []) {
        $headers = getallheaders();
        if ($request->method() != $formType) {
            return self::error('This method is not allowed.', 409);
        }
        if (isset($headers['client_id']) && isset($headers['client_secret'])):
            $params['client_id'] = $headers['client_id'];
            $params['client_secret'] = $headers['client_secret'];
        endif;
//        if (isset($headers['device_id']) && isset($headers['device_token']) && isset($headers['device_type'])):
        if (isset($headers['device_id']) && isset($headers['device_type'])):
            $params['device_id'] = $headers['device_id'];
//            $params['device_token'] = $headers['device_token'];
            $params['device_type'] = $headers['device_type'];
        endif;
        $validator = Validator::make($params, $attributeValidate);
        if ($validator->fails()) {
            $errors = [];
            $messages = $validator->getMessageBag();
            foreach ($messages->keys() as $key) {
                $errors[] = $messages->get($key)['0'];
            }
            return self::error($errors, 422, false);
        }
        return false;
    }

    public static function validateAttributes($request, $formType = 'GET', $attributeValidate = [], $attributes = [], $checkVariableCount = true) {
        $headers = getallheaders();
        if ($request->method() != $formType) {
            return self::error('This method is not allowed.', 409);
        }
        if (isset($headers['client_id']) && isset($headers['client_secret'])):
            $params['client_id'] = $headers['client_id'];
            $params['client_secret'] = $headers['client_secret'];
        endif;
//        if (isset($headers['device_id']) && isset($headers['device_token']) && isset($headers['device_type'])):
        if (isset($headers['device_id']) && isset($headers['device_type'])):
            $params['device_id'] = $headers['device_id'];
//            $params['device_token'] = $headers['device_token'];
            $params['device_type'] = $headers['device_type'];
        endif;
        foreach ($attributes as $attribute):
            $params[$attribute] = $request->$attribute;
        endforeach;
        if ($checkVariableCount === true):
            if (count($attributes) != count($request->all())):
                return self::error('Please fill required parameters only.', 409);
            endif;
//        else:
//            if (count($request->all()) == 0):
//                return self::error('Please select one of the paramter.', 409);
//            endif;
        endif;
//        echo'<pre>';
//        print_r($params);
//        die;
        $validator = Validator::make($params, $attributeValidate);
        if ($validator->fails()) {
            $errors = [];
            $messages = $validator->getMessageBag();
            foreach ($messages->keys() as $key) {
                $errors[] = $messages->get($key)['0'];
            }
            return self::error($errors, 422, false);
        }
        return false;
    }

    public static function error($validatorMessage, $errorCode = 422, $messageIndex = true) {
        if ($messageIndex === true):
            $validatorMessage = ['message' => [$validatorMessage]];
        else:
            $validatorMessage = ['message' => $validatorMessage];
        endif;
        return response()->json(['status' => false, 'data' => (object) [], 'error' => ['code' => $errorCode, 'error_message' => $validatorMessage]], $errorCode);
    }

    public static function success($data, $code = 200) {
//        print_r($data);die;
        return response()->json(['status' => true, 'code' => $code, 'data' => (object) $data], $code);
    }

    public static function successCreated($data, $code = 201) {
        return response()->json(['status' => true, 'code' => $code, 'data' => (object) $data], $code);
    }

    protected static function sendOTP(User $user) {
        $otp = mt_rand(1000, 9999);
        $user->otp = $otp;
        $user->save();
        return self::sendTextMessage('Your Gas Application Verification code is ' . $otp, $user->mobile_number);
    }

    protected static function sendTextMessage($message, $to = '9646848501') {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $twilio = new Client($sid, $token);
//        $return = $twilio->messages->create("+964" . $to, ["body" => $message, "from" => env('TWILIO_FROM')]);
        $return = $twilio->messages->create("+91" . $to, ["body" => $message, "from" => env('TWILIO_FROM')]);
        return $return;
    }

    public static function pushNotofication($data = [], $deviceToken) {
        // FCM
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);
        $notificationBuilder = new PayloadNotificationBuilder($data['title']);
        $notificationBuilder->setBody($data['body'])->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['a_data' => 'my_data']);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

//        $deviceToken = "dRyHOgfdDMA:APA91bFr-dj3_sDe3z7R3d30X12k6n4NnFWuyvbsh4xGRr-s0j2RfpKplfrc0rms5ZZ0aZu6taho3ZbGn_xvtSPdq0QBTcXTRjo94g2L5X5snSuJUW4yt-TfH5WRbEqYoKAktSkLPN5X";

        $downstreamResponse = FCM::sendTo($deviceToken, $option, $notification, $data);
//        $downstreamResponse->numberFailure();
        return $downstreamResponse->numberSuccess() == '1' ? true : false;
    }

}
