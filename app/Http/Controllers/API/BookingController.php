<?php

namespace App\Http\Controllers\API;

use App\Event;
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

        $rules = ['type' => 'required|in:event,session,space', 'target_id' => 'required', 'user_id' => '', 'tickets' => '', 'price' => 'required',
            'payment_details' => '','token'=>'required','status'=>'required'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            if (!isset($request->token))
                return parent::error('Please add token');
            $input = $request->all();
            $input['user_id'] = \Auth::id();
            switch ($request->type):
                case 'event':
                    $targetModel= new \App\Event();
                    break;
                case 'space':
                    $targetModel= new \App\Space();
                    break;
                case 'session':
                    $targetModel= new \App\Session();
                    break;
                    endswitch;
            $targetModel = $targetModel->whereId($request->target_id)->get();
            if($targetModel->isEmpty())
                return parent::error('Please use valid target id');
            $booking = \App\Booking::create($input);

            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            \Stripe\Charge::create([
                "amount" => $booking->price * 100,
                "currency" => config('app.stripe_default_currency'),
                "source" => $request->token, // obtained with Stripe.js
                "description" => "Charge for the booking booked through utrain app"
            ]);
            // Push notification start
            parent::pushNotifications(['title' => $this->_MSGCreate['title'], 'body' => $this->_MSGCreate['body'], 'data' => ['target_id' => $booking->id]], $targetModel->first()->created_by);
            // Push notification end

            return parent::successCreated(['message' => 'Created Successfully', 'booking' => $booking]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getBookingsAthlete(Request $request) {
          $rules = ['target_id'=>'','type'=>'required|in:event,session,space'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;

        try {
            $model = new \App\Booking();
            $model = MyModel::where('user_id', \Auth::id())->where('type',$request->type)->Select('id', 'type', 'target_id', 'user_id', 'tickets', 'price');
            $model = $model->with('userDetails');
            $model = $model->with($request->type);
            return parent::success($model->get());
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getBookingsOrganiser(Request $request) {
        $rules = ['target_id'=>'','type'=>'required|in:event,session,space'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;

        try {
            $model = new \App\Booking();
            $user = \App\User::find(Auth::user()->id);
            if ($user->hasRole('organizer') === false)
                return parent::error('Please use valid auth token');
            $target = Event::where('created_by',\Auth::id())->pluck('id');
            $model = MyModel::whereIN('target_id', $target)->where('type',$request->type)->Select('id', 'type', 'target_id', 'user_id', 'tickets', 'price');
            $model = $model->with('userDetails');
            $model = $model->with($request->type);
            return parent::success($model->get());
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getBookingsCoach(Request $request) {
        $rules = ['target_id'=>'','type'=>'required|in:event,session,space'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;

        try {
            $model = new \App\Booking();
            $user = \App\User::find(Auth::user()->id);
            if ($user->hasRole('coach') === false)
                return parent::error('Please use valid auth token');
            $target = Event::where('created_by',\Auth::id())->pluck('id');
            $model = MyModel::whereIN('target_id', $target)->where('type',$request->type)->Select('id', 'type', 'target_id', 'user_id', 'tickets', 'price');
            $model = $model->with('userDetails');
            $model = $model->with($request->type);
            return parent::success($model->get());
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getitem(Request $request) {

        $rules = ['id' => 'required|exists:bookings,id'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), true);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
            $model = new \App\Booking();
            $model = $model->where('id', $request->id)->with('userDetails');
            $model = $model->with($model->first()->type);
//            dd($model);
            return parent::success($model->first());
        } catch (\Exception $ex) {

            return parent::error($ex->getMessage());
        }
    }

}
