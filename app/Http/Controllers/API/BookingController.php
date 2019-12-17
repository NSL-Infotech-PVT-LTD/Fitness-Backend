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
use App\Http\Controllers\API\DatePeriod;
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
            parent::pushNotifications(['title' => $this->_MSGCreate['title'], 'body' => $this->_MSGCreate['body'], 'data' => ['target_id' => $booking->id, 'target_model' => 'booking']], $targetModeldata->first()->created_by);
            parent::pushNotifications(['title' => $this->_MSGAthCreate['title'], 'body' => $this->_MSGAthCreate['body'], 'data' => ['target_id' => $booking->id, 'target_model' => 'booking']], $booking->user_id);
            // Push notification end

            return parent::successCreated(['message' => 'Created Successfully', 'booking' => $booking]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function spacestore(Request $request) {

        $rules = ['type' => 'required|in:space', 'target_id' => 'required', 'user_id' => '', 'price' => 'required',
            'payment_details' => '', 'token' => 'required', 'status' => 'required', 'booking' => 'required'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $params = json_decode($request->booking);
            if (!isset($request->token))
                return parent::error('Please add token');
            $input = $request->all();
            $input['user_id'] = \Auth::id();
            $targetModel = new \App\Space();
            $targetModeldata = $targetModel->whereId($request->target_id)->get();
            if ($targetModeldata->isEmpty())
                return parent::error('Please use valid target id');
//            $checkData = MyModel::where('target_id', $request->target_id)->where('type', $request->type)->get();
//            if ($checkData->isEmpty() === false):
//                return parent::error(['message' => $request->target_id . ' already booked']);
//            endif;
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
            foreach ($params as $param):
                \App\BookingSpace::create(['booking_id' => $booking->id, 'booking_date' => $param->booking_date, 'from_time' => $param->from_time, 'to_time' => $param->to_time]);
            endforeach;
//            $booking->payment_id = $stripe->id;
            /*             * ***target model update start*** */
            $targetModelupdate = $targetModel->findOrFail($request->target_id);
            $targetModelupdate->save();
            /*             * ***target model update end*** */
            // Push notification start
            parent::pushNotifications(['title' => $this->_MSGCreate['title'], 'body' => $this->_MSGCreate['body'], 'data' => ['target_id' => $booking->id, 'target_model' => 'booking']], $targetModeldata->first()->created_by);
            parent::pushNotifications(['title' => $this->_MSGAthCreate['title'], 'body' => $this->_MSGAthCreate['body'], 'data' => ['target_id' => $booking->id, 'target_model' => 'booking']], $booking->user_id);
            // Push notification end

            return parent::successCreated(['message' => 'Created Successfully', 'booking' => $booking]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getBookingsAll(Request $request) {
        $rules = ['limit' => '', 'filter_by' => 'required|date_format:Y-m'];
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
                $dataSend[] = $data;
            endforeach;
            return parent::success(['data' => $dataSend]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getSpaceBookings(Request $request) {
        $rules = ['limit' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $perPage = isset($request->limit) ? $request->limit : 20;
            $model = MyModel::where('user_id', \Auth::id())->Select('id', 'type', 'target_id', 'user_id', 'tickets', 'price', 'space_date_start', 'space_date_end', 'payment_id', 'status', 'rating');
            $model = $model->with(['userDetails']);
            return parent::success($model->paginate($perPage));
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
                        $targetOrderByKey = 'start_date';
                    elseif ($request->type == 'session'):
                        $targetOrderByKey = 'start_date';
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
                        $targetOrderByKey = 'start_date';
                    elseif ($request->type == 'session'):
                        $targetOrderByKey = 'start_date';
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

    public function getBookingscoachOrg(Request $request) {
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
                        $targetOrderByKey = 'start_date';
                    elseif ($request->type == 'session'):
                        $targetOrderByKey = 'start_date';
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

    public function getAlllBookingsCoach(Request $request) {
        $rules = ['limit' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $user = \App\User::find(Auth::user()->id);
            if ($user->hasRole('coach') === false)
                return parent::error('Please use valid auth token');
            $model = MyModel::where('owner_id', \Auth::id())->Select('id', 'type', 'target_id', 'user_id', 'tickets', 'price', 'payment_id', 'status', 'rating');
            $model = $model->with(['userDetails']);
            $perPage = isset($request->limit) ? $request->limit : 20;
            return parent::success($model->paginate($perPage));
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getAlllBookingsOrganiser(Request $request) {
        $rules = ['limit' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $user = \App\User::find(Auth::user()->id);
            if ($user->hasRole('organizer') === false)
                return parent::error('Please use valid auth token');
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

    private static function splitTime($StartTime, $EndTime, $Duration = "60") {
        $ReturnArray = array(); // Define output
        $StartTime = strtotime($StartTime); //Get Timestamp
        $EndTime = strtotime($EndTime); //Get Timestamp

        $AddMins = $Duration * 60;

        while ($StartTime <= $EndTime) { //Run loop
            $ReturnArray[] = date("G:i:s", $StartTime);
            $StartTime += $AddMins; //Endtime check
        }
        return $ReturnArray;
    }

    public function getavailability(Request $request) {
//        $rules = ['target_id' => 'required|exists:spaces,id', 'date' => 'required', 'from_time' => 'required', 'to_time' => 'required', 'hours' => 'required'];
        $rules = ['target_id' => 'required|exists:spaces,id', 'date' => 'required', 'hours' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $request->hours = '1';
            $model = new \App\Space();
            $model = $model->where('id', $request->target_id);

//            if ($request->from_time < $model->first()->open_hours_from):
//                return parent::error('Time is less than the space open_hours_from timing');
//            endif;
//            if ($request->to_time > $model->first()->open_hours_to):
//                return parent::error('Time is more than the space open_hours_to timing');
//            endif;

            $requestDay = date('N', strtotime($request->date));
            if (!in_array($requestDay, json_decode($model->first()->availability_week)))
                return parent::error('availabilty week does not matches');

//            $slot = self::SplitTime($request->from_time, $request->to_time, $request->hours * 60);

            $booking = new \App\Booking();
            $booking = $booking->where('target_id', $request->target_id);
            $bookingIds = $booking->get()->pluck('id')->toarray();
//             dd($bookingIds);
            $bookingspaces = \App\BookingSpace::whereIn('booking_id', $bookingIds)->whereDate('booking_date', $request->date);
            $bookingspaces = $bookingspaces->get();
//            dd($bookingspace->pluck('from_time')->toarray());
            $slots = self::splitTime($model->first()->open_hours_from, $model->first()->open_hours_to, $request->hours * 60);
//                 dd($slots);
//            $a = '12:30';
////            dd($a);
//            $b = '15:30';
//            $slotss = self::splitTime($a, $b, 1 * 60);
//            dd($slotss);
//       


            
            $bookedslotss = [];
            foreach ($bookingspaces as $bookingspace):

                $bookedslots = self::splitTime($bookingspace->from_time, $bookingspace->to_time, 1 * 60);
                unset($bookedslots[count($bookedslots) - 1]);
                $bookedslotss=array_merge($bookedslotss,$bookedslots);
//            dd($bookedslotss);
            endforeach;

//            dd($bookedslotss);
            $available = [];
            foreach ($slots as $slot):
                if (in_array($slot, $bookedslotss))
                    continue;
             $slots = date('H:i:s',strtotime($slot.'+ 1 hour'));
                $available[] =[$slot,$slots];
            endforeach;
                unset($available[count($available) - 1]);
            dd($available);
//            if (in_array($requestDay, json_decode($booking->first()->date)))
//                return parent::error('Sorry,requested date is not available');

            return parent::success(['available_slot' => $available]);
        } catch (\Exception $ex) {

            return parent::error($ex->getMessage());
        }
    }

}
