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
            $targetModeldata = $targetModel->whereId($request->target_id)->get();
            if($targetModeldata->isEmpty())
                return parent::error('Please use valid target id');
            if($request->type!='space')
                if($targetModeldata->first()->guest_allowed_left ==0)
                    return parent::error('Tickets are sold out, Better luck next time');
            if($request->type!='space')
                if($targetModeldata->first()->guest_allowed_left < $request->tickets)
                    return parent::error('Tickets are greater than left tickets');
//
//            dd($targetModelupdate);
            $booking = \App\Booking::create($input);

            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            \Stripe\Charge::create([
                "amount" => $booking->price * 100,
                "currency" => config('app.stripe_default_currency'),
                "source" => $request->token, // obtained with Stripe.js
                "description" => "Charge for the booking booked through utrain app"
            ]);
            /*****target model update start****/
            $targetModelupdate = $targetModel->findOrFail($request->target_id);
            $targetModelupdate->guest_allowed_left = $targetModeldata->first()->guest_allowed_left-$request->tickets;
            $targetModelupdate->save();
            /*****target model update end****/
        // Push notification start
            parent::pushNotifications(['title' => $this->_MSGCreate['title'], 'body' => $this->_MSGCreate['body'], 'data' => ['target_id' => $booking->id]], $targetModeldata->first()->created_by);
            // Push notification end

            return parent::successCreated(['message' => 'Created Successfully', 'booking' => $booking]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getBookingsAthleteAll(Request $request) {
        $rules = ['limit' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), true);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $model = MyModel::where('user_id', \Auth::id())->Select('id', 'type', 'target_id', 'user_id', 'tickets', 'price');
            $model = $model->with(['userDetails','event','space','session']);
            $perPage = isset($request->limit) ? $request->limit : 20;
//            $model = $model->whereHas('event', function ($query) {
//                dd($query);
//                $query->Where('name', 'LIKE', "%$query%");
//            });
            return parent::success($model->paginate($perPage));
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }
    public function getBookingsAthlete(Request $request) {
          $rules = ['search' => '','target_id'=>'','type'=>'required|in:event,session,space','order_by'=>'required_if:type,event|required_if:type,session','limit' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $model = MyModel::where('user_id', \Auth::id())->where('type',$request->type)->Select('id', 'type', 'target_id', 'user_id', 'tickets', 'price');
            $model = $model->with('userDetails')->with($request->type);
            if($request->type != 'space'):
                $model= $model->whereHas($request->type, function ($query)use($request) {
                    if($request->type=='event'):
                        $targetOrderByKey='end_date';
                    elseif ($request->type=='session'):
                         $targetOrderByKey='date';
                    endif;
                    if ($request->order_by == 'upcoming'):
                        $query->whereDate($targetOrderByKey,'>=',\Carbon\Carbon::now());
                    elseif ($request->order_by == 'completed'):
                        $query->whereDate($targetOrderByKey,'<',\Carbon\Carbon::now());
                    endif;
                });
            endif;

            $perPage = isset($request->limit) ? $request->limit : 20;
            if (isset($request->search)):
//                dd($request->search);
                $model = $model->whereHas('userDetails', function ($query)use($request) {
                    $query->Where('name', 'LIKE', "%$request->search%")->orWhere('email', 'LIKE', "%$request->search%");
                });
            endif;
            return parent::success($model->paginate($perPage));
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getBookingsOrganiser(Request $request) {
        $rules = ['search' => '','target_id'=>'required','type'=>'required|in:event,session,space','order_by'=>'required_if:type,event|required_if:type,session','limit' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;

        try {


            $model = new \App\Booking();
            $user = \App\User::find(Auth::user()->id);
            if ($user->hasRole('organizer') === false)
                return parent::error('Please use valid auth token');
//            $target = Event::where('created_by',\Auth::id())->pluck('id');
            if(Event::where('created_by',\Auth::id())->where('id',$request->target_id)->get()->isEmpty())
                return parent::error('Not found');
            $model = MyModel::where('target_id',$request->target_id)->where('type',$request->type)->Select('id', 'type', 'target_id', 'user_id', 'tickets', 'price');
//            dd($model);

            $model = $model->with('userDetails')->with($request->type);
            if($request->type != 'space'):
                $model= $model->whereHas($request->type, function ($query)use($request) {
                    if($request->type=='event'):
                        $targetOrderByKey='end_date';
                    elseif ($request->type=='session'):
                        $targetOrderByKey='date';
                    endif;
                    if ($request->order_by == 'upcoming'):
                        $query->whereDate($targetOrderByKey,'>=',\Carbon\Carbon::now());
                    elseif ($request->order_by == 'completed'):
                        $query->whereDate($targetOrderByKey,'<',\Carbon\Carbon::now());
                    endif;
                });
            endif;
            if (isset($request->search)):
//                dd($request->search);
                $model = $model->whereHas('userDetails', function ($query)use($request) {
                    $query->Where('name', 'LIKE', "%$request->search%")->orWhere('email', 'LIKE', "%$request->search%");
                });
            $perPage = isset($request->limit) ? $request->limit : 20;
            return parent::success($model->paginate($perPage));
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getBookingsCoach(Request $request) {
        $rules = ['search' => '','target_id'=>'','type'=>'required|in:event,session,space','order_by'=>'required_if:type,event|required_if:type,session','limit' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;

        try {


            $model = new \App\Booking();
            $user = \App\User::find(Auth::user()->id);
            if ($user->hasRole('coach') === false)
                return parent::error('Please use valid auth token');
//            $target = Event::where('created_by',\Auth::id())->pluck('id');
            if(Event::where('created_by',\Auth::id())->where('id',$request->target_id)->get()->isEmpty())
                return parent::error('Event not found');
            $model = MyModel::where('target_id', $request->target_id)->where('type',$request->type)->Select('id', 'type', 'target_id', 'user_id', 'tickets', 'price');
            $model = $model->with('userDetails')->with($request->type);
            if($request->type != 'space'):
                $model= $model->whereHas($request->type, function ($query)use($request) {
                    if($request->type=='event'):
                        $targetOrderByKey='end_date';
                    elseif ($request->type=='session'):
                        $targetOrderByKey='date';
                    endif;
                    if ($request->order_by == 'upcoming'):
                        $query->whereDate($targetOrderByKey,'>=',\Carbon\Carbon::now());
                    elseif ($request->order_by == 'completed'):
                        $query->whereDate($targetOrderByKey,'<',\Carbon\Carbon::now());
                    endif;
                });
            endif;

            if (isset($request->search)):
//                dd($request->search);
                $model = $model->whereHas('userDetails', function ($query)use($request) {
                    $query->Where('name', 'LIKE', "%$request->search%")->orWhere('email', 'LIKE', "%$request->search%");
                });
            $perPage = isset($request->limit) ? $request->limit : 20;
            return parent::success($model->paginate($perPage));
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
