<?php

namespace App\Http\Controllers\API;

use App\Booking;
use App\Event;
use App\Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Booking as MyModel;
use App\BookingSpace;
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
            parent::pushNotifications(['title' => $this->_MSGCreate['title'], 'body' => $this->_MSGCreate['body'], 'data' => ['target_id' => $booking->id, 'target_model' => 'booking', 'target_type' => $booking->type]], $targetModeldata->first()->created_by);
            parent::pushNotifications(['title' => $this->_MSGAthCreate['title'], 'body' => $this->_MSGAthCreate['body'], 'data' => ['target_id' => $booking->id, 'target_model' => 'booking', 'target_type' => $booking->type]], $booking->user_id);
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
            parent::pushNotifications(['title' => $this->_MSGCreate['title'], 'body' => $this->_MSGCreate['body'], 'data' => ['target_id' => $booking->id, 'target_model' => 'booking', 'target_type' => $booking->type]], $targetModeldata->first()->created_by);
            parent::pushNotifications(['title' => $this->_MSGAthCreate['title'], 'body' => $this->_MSGAthCreate['body'], 'data' => ['target_id' => $booking->id, 'target_model' => 'booking', 'target_type' => $booking->type]], $booking->user_id);
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
            $model = MyModel::where('user_id', \Auth::id())->Select('id', 'type', 'target_id', 'user_id', 'tickets', 'price', 'payment_id', 'status', 'rating');
            $model = $model->with(['userDetails']);
//            $model = $model->where('target_data','!=','');
//            $perPage = isset($request->limit) ? $request->limit : 20;
            $dataSend = [];
            $requestDate = \Carbon\Carbon::createFromFormat('Y-m', $request->filter_by);
//            dd($model->get());
            $bookingSpaceIds = BookingSpace::whereYear('booking_date', $requestDate->year)->whereMonth('booking_date', $requestDate->month)->get()->pluck('booking_id')->toarray();
            foreach ($model->get() as $data):
                if (empty($data['target_data']))
                    continue;               
                if ($data->type == 'space'):
                    if (!in_array($data->id, $bookingSpaceIds))
                        continue;
                endif;
                $dataSend[] = $data;
            endforeach;
//            dd($dataSend);
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
            $model = MyModel::where('user_id', \Auth::id())->Select('id', 'type', 'target_id', 'user_id', 'tickets', 'price', 'payment_id', 'status', 'rating');
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

    public function getbothbookings(Request $request) {
        $rules = ['limit' => '', 'filter_by' => 'required|date_format:Y-m'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $user = \App\User::find(Auth::user()->id);
            $owner = MyModel::where('owner_id', \Auth::id())->Select('id', 'type', 'target_id', 'user_id', 'tickets', 'price', 'payment_id', 'status', 'rating');
            $owner = $owner->with(['userDetails']);

            $dataSend = [];
            $requestDate = \Carbon\Carbon::createFromFormat('Y-m', $request->filter_by);
            if (isset($requestDate)):
                $bookingSpaceIds = BookingSpace::whereYear('booking_date', $requestDate->year)->whereMonth('booking_date', $requestDate->month)->get()->pluck('booking_id')->toarray();
//                    dd($bookingSpaceIds);
//                $owner = $owner->whereIn('id', $bookingSpaceIds);
            endif;
//            dd($owner->get()->toarray());
            foreach ($owner->get() as $k => $data):
                if (empty($data['target_data']))
                    continue;
                if ($data->type == 'space'):
                    if (!in_array($data->id, $bookingSpaceIds))
                        continue;
                endif;
                $dataSend[$k] = $data;
                $dataSend[$k]['is_booking_my'] = true;
            endforeach;
//dd($dataSend);
            $booked = MyModel::where('user_id', \Auth::id())->Select('id', 'type', 'target_id', 'user_id', 'tickets', 'price', 'payment_id', 'status', 'rating');
            $booked = $booked->with(['userDetails']);
            $bookSend = [];
//            $requestDate = \Carbon\Carbon::createFromFormat('Y-m', $request->filter_by);
//            dd($booked->get()->toarray());
            foreach ($booked->get() as $h => $book):
                if (empty($book['target_data']))
                    continue;
                if ($book->type == 'space'):
                    if (!in_array($book->id, $bookingSpaceIds))
                        continue;
                endif;
                $bookSend[$h] = $book;
                $bookSend[$h]['is_booking_my'] = false;
            endforeach;
//            dd($dataSend);
            $bookings = array_merge($dataSend, $bookSend);
//            return parent::success(['bookings_as_owner' => $dataSend, 'bookings_as_user' => $bookSend]);
            return parent::success(['data' => $bookings]);
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
            $model = $model->where('id', $request->id)->with('userDetails')->with('booking_details');
            $model = $model->with($model->first()->type);
//            dd($model);
            return parent::success($model->first());
        } catch (\Exception $ex) {

            return parent::error($ex->getMessage());
        }
    }

    public function getspace(Request $request) {

        $rules = ['id' => 'required|exists:booking_spaces,id'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), true);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
            $model = new \App\BookingSpace();
            $model = $model->where('id', $request->id)->with('userDetails');
            $model = $model->with($model->first());
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

    private static function splitTimeWithBookedhours($StartTime, $EndTime, $Duration = "60", $bookedSlots) {
        $ReturnArray = array(); // Define output
        $StartTime = strtotime($StartTime); //Get Timestamp
        $EndTime = strtotime($EndTime); //Get Timestamp

        $AddMins = $Duration * 60;
//        dd($bookedSlots);
        while ($StartTime <= $EndTime) { //Run loop
            $ReturnArray[] = date("H:i", $StartTime);
            $StartTime += $AddMins; //Endtime check
        }
//        dd($ReturnArray);
//        dd($bookedSlots);
        $uniqueR = [];
        foreach ($bookedSlots as $slots):
//            dd((strtotime($slots[count($slots) - 1]) - strtotime($slots[0])) / 60);
            if (((strtotime($slots[count($slots) - 1]) - strtotime($slots[0])) / 60) == 60):
//                    dd($slots[0]);
//                echo array_search(date('H:i', strtotime($slots[0])), $ReturnArray);
                unset($ReturnArray[array_search(date('H:i', strtotime($slots[0])), $ReturnArray)]);
            else:
                foreach ($ReturnArray as $rrrrrr):
                    if ((strtotime($rrrrrr) > strtotime($slots[0] . ' + 1 minute')) && (strtotime($rrrrrr) < strtotime($slots[count($slots) - 1] . ' - 1 minute'))):
//                    dd(array_search($rrrrrr, $ReturnArray));
//                        $uniqueR[] = array_search($rrrrrr, $ReturnArray);
                        unset($ReturnArray[array_search($rrrrrr, $ReturnArray)]);
                    endif;
                endforeach;
            endif;
        endforeach;
//        dd($ReturnArray);
        $rr = [];
        $k = 0;
        foreach (array_keys($ReturnArray) as $ret):
            try {
//                if ($k == 0)
//                    $rr[$k][] = $ReturnArray[$ret];
                if (isset($ReturnArray[$ret + 1])):
                    $rr[$k][] = $ReturnArray[$ret];
                else:
//                    $rr[$k] = array_values(array_unique($rr[$k]));
//                dd(array_values($rr[$k]));
                    $k++;
                endif;
            } catch (\Exception $ex) {
                continue;
            }
        endforeach;
//        dd($rr);
        return $rr;
    }

    public function getavailability(Request $request) {
//        $rules = ['target_id' => 'required|exists:spaces,id', 'date' => 'required', 'from_time' => 'required', 'to_time' => 'required', 'hours' => 'required'];
        $rules = ['target_id' => 'required|exists:spaces,id', 'date' => 'required'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), true);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $user = \App\User::find(Auth::user()->id);

            $model = new \App\Space();
            $model = $model->where('id', $request->target_id);
            $requestDay = date('N', strtotime($request->date));
//            dd(json_decode($model->first()->availability_week));
//            if (!in_array($requestDay, json_decode($model->first()->availability_week)))
            if (!in_array($requestDay, $model->first()->availability_week))
                return parent::error('availabilty week does not matches');
            $booking = new \App\Booking();
            $booking = $booking->where('target_id', $request->target_id);
            $bookingIds = $booking->get()->pluck('id')->toarray();
//             dd($bookingIds);
            $bookingspaces = \App\BookingSpace::whereIn('booking_id', $bookingIds)->whereDate('booking_date', $request->date);
            $bookingspaces = $bookingspaces->get();
//            dd($bookingspace->pluck('from_time')->toarray());
            $bookedslotss = [];


            foreach ($bookingspaces as $bookingspace):
                $bookedslotss[] = [$bookingspace->from_time, $bookingspace->to_time];
            endforeach;
            $slots = self::splitTimeWithBookedhours($model->first()->open_hours_from, $model->first()->open_hours_to, 1 * 60, $bookedslotss);
//            dd($slots);
            $available = [];
            foreach ($slots as $slot):
                $available[] = [$slot[0], date('H:i', strtotime($slot[count($slot) - 1] . '+ 1 hour'))];
            endforeach;
//            dd($available);
            return parent::success(['available_slot' => $available]);
        } catch (\Exception $ex) {

            return parent::error($ex->getMessage());
        }
    }

}
