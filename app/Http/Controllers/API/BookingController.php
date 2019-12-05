<?php

namespace App\Http\Controllers\API;

use App\Booking;
use App\Event;
use App\Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Booking as MyModel;
use App\UserNotification;
use Twilio\Rest\Client;
use Validator;
use DB;
use Auth;

class BookingController extends ApiController {

    private $_MSGCreate = ['title' => 'Hurray!', 'body' => 'You got new Booking'];
    private $_MSGAthCreate = ['title' => 'Hurray!', 'body' => 'Your Booking is confirmed'];

    public function store(Request $request) {

        $rules = ['type' => 'required|in:event,session', 'target_id' => 'required', 'user_id' => '', 'tickets' => '', 'price' => 'required',
            'payment_details' => '', 'token' => 'required', 'status' => 'required'];
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
                    $targetModel = new \App\Event();
                    break;
                case 'space':
                    $targetModel = new \App\Space();
                    break;
                case 'session':
                    $targetModel = new \App\Session();
                    break;
            endswitch;
            $targetModeldata = $targetModel->whereId($request->target_id)->get();
//            dd($targetModeldata);
            if ($targetModeldata->isEmpty())
                return parent::error('Please use valid target id');
            if ($request->type != 'space')
                if ($targetModeldata->first()->guest_allowed_left == 0)
                    return parent::error('Tickets are sold out, Better luck next time');
            if ($request->type != 'space')
                if ($targetModeldata->first()->guest_allowed_left < $request->tickets)
                    return parent::error('Tickets are greater than left tickets');
//
//            dd($targetModelupdate);
            $input['owner_id'] = $targetModeldata->first()->created_by;
            $booking = \App\Booking::create($input);

            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            $stripe = \Stripe\Charge::create([
                        "amount" => $booking->price * 100,
                        "currency" => config('app.stripe_default_currency'),
                        "source" => $request->token, // obtained with Stripe.js
                        "description" => "Charge for the booking booked through utrain app"
            ]);
            /*             * ***target model update start*** */
//            Booking::findorfail($booking->id);
            $booking->payment_details = json_encode($stripe);
            $booking->payment_id = $stripe->id;
            $booking->save();
            $targetModelupdate = $targetModel->findOrFail($request->target_id);
            $targetModelupdate->guest_allowed_left = $targetModeldata->first()->guest_allowed_left - $request->tickets;
            $targetModelupdate->save();
            /*             * ***target model update end*** */
            // Push notification start
            parent::pushNotifications(['title' => $this->_MSGCreate['title'], 'body' => $this->_MSGCreate['body'], 'data' => ['target_id' => $booking->id]], $targetModeldata->first()->created_by);
            parent::pushNotifications(['title' => $this->_MSGAthCreate['title'], 'body' => $this->_MSGAthCreate['body'], 'data' => ['target_id' => $booking->id]], $booking->user_id);
            // Push notification end

            return parent::successCreated(['message' => 'Created Successfully', 'booking' => $booking]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function spacestore(Request $request) {

        $rules = ['type' => 'required|in:space', 'target_id' => 'required', 'user_id' => '', 'price' => 'required',
            'payment_details' => '', 'token' => 'required', 'status' => 'required', 'space_date_start' => 'required|date_format:"Y-m-d H:i|after_or_equal:\' . \Carbon\Carbon::now()', 'space_date_end' => 'required|date_format:"Y-m-d H:i|after_or_equal:\' . \Carbon\Carbon::now()'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            if (!isset($request->token))
                return parent::error('Please add token');
            $input = $request->all();
            $input['user_id'] = \Auth::id();
            $targetModel = new \App\Space();
            $targetModeldata = $targetModel->whereId($request->target_id)->get();
            if ($targetModeldata->isEmpty())
                return parent::error('Please use valid target id');
            $checkData = MyModel::where('target_id', $request->target_id)->where('type', $request->type)->get();
            if ($checkData->isEmpty() === false):
                return parent::error(['message' => $request->target_id . ' already booked']);
            endif;
//
//            dd($targetModelupdate);
            $input['owner_id'] = $targetModeldata->first()->created_by;
            $booking = \App\Booking::create($input);

            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            $stripe = \Stripe\Charge::create([
                        "amount" => $booking->price * 100,
                        "currency" => config('app.stripe_default_currency'),
                        "source" => $request->token, // obtained with Stripe.js
                        "description" => "Charge for the booking booked through utrain app"
            ]);
            /*             * ***target model update start*** */
//            Booking::findorfail($booking->id);
            $booking->payment_details = json_encode($stripe);
            $booking->payment_id = $stripe->id;
            $booking->save();
//            $booking->payment_id = $stripe->id;
            /*             * ***target model update start*** */
            $targetModelupdate = $targetModel->findOrFail($request->target_id);
            $targetModelupdate->save();
            /*             * ***target model update end*** */
            // Push notification start
            parent::pushNotifications(['title' => $this->_MSGCreate['title'], 'body' => $this->_MSGCreate['body'], 'data' => ['target_id' => $booking->id]], $targetModeldata->first()->created_by);
            parent::pushNotifications(['title' => $this->_MSGAthCreate['title'], 'body' => $this->_MSGAthCreate['body'], 'data' => ['target_id' => $booking->id]], $booking->user_id);
            // Push notification end

            return parent::successCreated(['message' => 'Created Successfully', 'booking' => $booking]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getBookingsAthleteAll(Request $request) {
        $rules = ['limit' => '', 'filter_by' => 'required'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
//            dd($request->filter_by);
            $model = MyModel::where('user_id', \Auth::id())->Select('id', 'type', 'target_id', 'user_id', 'tickets', 'price', 'space_date_start', 'space_date_end', 'payment_id', 'status', 'rating');
            $model = $model->with(['userDetails']);
//            $model = $model->where('target_data','!=','');
//            $perPage = isset($request->limit) ? $request->limit : 20;
            $dataSend = [];
            $requestDate = \Carbon\Carbon::createFromFormat('Y-m', $request->filter_by);
            foreach ($model->get() as $data):
                if (empty($data['target_data']))
                    continue;
                if ($data->type == 'space'):

                    $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $data->space_date_start);
                    if ($date->month !== $requestDate->month)
                        continue;
                endif;
//                if ($data->type == 'space'):
//                    $targetModel->whereYear('space_date_start', \Carbon\Carbon::now()->year)->whereMonth('space_date_start', \Carbon\Carbon::now()->month);
//                endif;
                $dataSend[] = $data;
            endforeach;
            return parent::success(['data' => $dataSend]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getBookingsAthlete(Request $request) {
        $rules = ['search' => '', 'target_id' => '', 'type' => 'required|in:event,session,space', 'order_by' => 'required_if:type,event|required_if:type,session', 'limit' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $model = MyModel::where('user_id', \Auth::id())->where('type', $request->type)->Select('id', 'type', 'target_id', 'user_id', 'tickets', 'price', 'payment_id', 'status', 'rating');
            $model = $model->with('userDetails')->with($request->type);
            if ($request->type != 'space'):
                $model = $model->whereHas($request->type, function ($query)use($request) {
                    if ($request->type == 'event'):
                        $targetOrderByKey = 'end_date';
                    elseif ($request->type == 'session'):
                        $targetOrderByKey = 'end_date';
                    endif;
                    if ($request->order_by == 'upcoming'):
                        $query->whereDate($targetOrderByKey, '>=', \Carbon\Carbon::now());
                    elseif ($request->order_by == 'completed'):
                        $query->whereDate($targetOrderByKey, '<', \Carbon\Carbon::now());
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
        $rules = ['search' => '', 'target_id' => 'required', 'type' => 'required|in:event,session,space', 'order_by' => 'required_if:type,event|required_if:type,session', 'limit' => ''];
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
            switch ($request->type):
                case 'event':
                    $targetModel = new \App\Event();
                    break;
                case 'space':
                    $targetModel = new \App\Space();
                    break;
                case 'session':
                    $targetModel = new \App\Session();
                    break;
            endswitch;
            if ($targetModel::where('created_by', \Auth::id())->where('id', $request->target_id)->get()->isEmpty())
                return parent::error('Not found');
            $model = MyModel::where('target_id', $request->target_id)->where('type', $request->type)->Select('id', 'type', 'target_id', 'user_id', 'tickets', 'price', 'payment_id', 'status', 'rating');
//            dd($model);

            $model = $model->with('userDetails')->with($request->type);
            if ($request->type != 'space'):
                $model = $model->whereHas($request->type, function ($query)use($request) {
                    if ($request->type == 'event'):
                        $targetOrderByKey = 'end_date';
                    elseif ($request->type == 'session'):
                        $targetOrderByKey = 'end_date';
                    endif;
                    if ($request->order_by == 'upcoming'):
                        $query->whereDate($targetOrderByKey, '>=', \Carbon\Carbon::now());
                    elseif ($request->order_by == 'completed'):
                        $query->whereDate($targetOrderByKey, '<', \Carbon\Carbon::now());
                    endif;
                });
            endif;
            if (isset($request->search)):
//                dd($request->search);
                $model = $model->whereHas('userDetails', function ($query)use($request) {
                    $query->Where('name', 'LIKE', "%$request->search%")->orWhere('email', 'LIKE', "%$request->search%");
                });
            endif;
            $perPage = isset($request->limit) ? $request->limit : 20;
            return parent::success($model->paginate($perPage));
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getBookingsCoach(Request $request) {
        $rules = ['search' => '', 'target_id' => '', 'type' => 'required|in:event,session,space', 'order_by' => 'required_if:type,event|required_if:type,session', 'limit' => ''];
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
            switch ($request->type):
                case 'event':
                    $targetModel = new \App\Event();
                    break;
                case 'space':
                    $targetModel = new \App\Space();
                    break;
                case 'session':
                    $targetModel = new \App\Session();
                    break;
            endswitch;
            if ($targetModel->where('created_by', \Auth::id())->where('id', $request->target_id)->get()->isEmpty())
                return parent::error('Not found');
            $model = MyModel::where('target_id', $request->target_id)->where('type', $request->type)->Select('id', 'type', 'target_id', 'user_id', 'tickets', 'price', 'payment_id', 'status', 'rating');
            $model = $model->with('userDetails')->with($request->type);
            if ($request->type != 'space'):
                $model = $model->whereHas($request->type, function ($query)use($request) {
                    if ($request->type == 'event'):
                        $targetOrderByKey = 'end_date';
                    elseif ($request->type == 'session'):
                        $targetOrderByKey = 'end_date';
                    endif;
                    if ($request->order_by == 'upcoming'):
                        $query->whereDate($targetOrderByKey, '>=', \Carbon\Carbon::now());
                    elseif ($request->order_by == 'completed'):
                        $query->whereDate($targetOrderByKey, '<', \Carbon\Carbon::now());
                    endif;
                });
            endif;

            if (isset($request->search)):
//                dd($request->search);
                $model = $model->whereHas('userDetails', function ($query)use($request) {
                    $query->Where('name', 'LIKE', "%$request->search%")->orWhere('email', 'LIKE', "%$request->search%");
                });
            endif;
            $perPage = isset($request->limit) ? $request->limit : 20;
            return parent::success($model->paginate($perPage));
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getAllBookingsCoach(Request $request) {
        $rules = ['limit' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), true);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
//        dd(\Auth::id());
            $model = MyModel::where('owner_id', \Auth::id())->Select('id', 'type', 'target_id', 'user_id', 'tickets', 'price', 'payment_id', 'status', 'rating');
            $model = $model->with(['userDetails']);
            $perPage = isset($request->limit) ? $request->limit : 20;

            return parent::success($model->paginate($perPage));
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getAllBookingsOrganiser(Request $request) {
        $rules = ['limit' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), true);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
//        dd(\Auth::id());
            $model = MyModel::where('owner_id', \Auth::id())->Select('id', 'type', 'target_id', 'user_id', 'tickets', 'price', 'payment_id', 'status', 'rating');
            $model = $model->with(['userDetails']);
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

    public function rating(Request $request) {
        $rules = ['booking_id' => 'required', 'rating' => 'required|in:1,2,3,4,5'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;

        try {
            if (\App\Booking::whereId($request->booking_id)->where('user_id', \Auth::id())->get()->isEmpty())
                return parent::error('Please use valid booking');
            $rating = MyModel::where('id', $request->booking_id)->get();
            if ($rating->isEmpty() === false):
                /*                 * ***target model update start*** */
                $ratingupdate = \App\Booking::findOrFail($request->booking_id);
                $ratingupdate->rating = $request->rating;
                $ratingupdate->status = 'completed_rated';
                $ratingupdate->save();
                /*                 * ***target model update end*** */
                return parent::successCreated(['message' => $request->booking_id . ' Updated Successfully']);
            else:
                return parent::error('Booking is not rated yet');
            endif;
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getTransactDetails(Request $request) {

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

    public function getnotifications(Request $request) {


        $rules = ['search' => '', 'limit' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
            $user = \App\User::find(Auth::user()->id);
            $model = new \App\UserNotification();
            $model = $model->where('user_id', \Auth::id())->select('id', 'title', 'body', 'data', 'user_id', 'created_at');
            if (isset($request->search))
                $model = $model->Where('title', 'LIKE', "%$request->search%")
                        ->orWhere('body', 'LIKE', "%$request->search%")
                        ->orWhere('data', 'LIKE', "%$request->search%");
            $perPage = isset($request->limit) ? $request->limit : 20;
            return parent::success($model->paginate($perPage));
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

}
