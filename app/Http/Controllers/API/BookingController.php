<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Booking as MyModel;
use Twilio\Rest\Client;
use Validator;
use DB;
use Auth;

class BookingController extends ApiController

{
    private $_MSGCreate = ['title' => 'Hurray!', 'body' => 'You got new Booking'];

    public function store(Request $request)
    {

        $rules = ['type' => 'required', 'target_id' => 'required', 'user_id' => '', 'tickets' => '', 'price' => 'required',
            'payment_details' => '','token'=>'required'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            if (!isset($request->token))
                return parent::error('Please add token');
            $input = $request->all();
            $input['user_id'] = \Auth::id();
            $booking = \App\Booking::create($input);

            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            \Stripe\Charge::create([
                "amount" => $booking->price * 100,
                "currency" => config('app.stripe_default_currency'),
                "source" => $request->token, // obtained with Stripe.js
                "description" => "Charge for the booking booked through utrain app"
            ]);

            // Push notification start
            parent::pushNotifications(['title' => $this->_MSGCreate['title'], 'body' => $this->_MSGCreate['body'], 'data' => ['target_id' => $booking->id]], $request->user_id);
            // Push notification end

            return parent::successCreated(['message' => 'Created Successfully', 'booking' => $booking]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }
        public function getBookings(Request $request) {
            $rules = [];
            $validateAttributes = parent::validateAttributes($request, 'GET', $rules, array_keys($rules), false);
            if ($validateAttributes):
                return $validateAttributes;
            endif;

            try {
                $model = new \App\Booking();
                $model = MyModel::where('user_id', \Auth::id())->Select('id', 'type', 'target_id', 'user_id', 'tickets', 'price');

                return parent::success($model->get());
            } catch (\Exception $ex) {
                return parent::error($ex->getMessage());
            }
        }
    }
