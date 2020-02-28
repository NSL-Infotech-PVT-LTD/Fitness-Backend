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

class CoachBookingController extends ApiController {

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

    public function getavailable(Request $request) {
//        $rules = ['target_id' => 'required|exists:spaces,id', 'date' => 'required', 'from_time' => 'required', 'to_time' => 'required', 'hours' => 'required'];
        $rules = ['coach_id' => 'required|exists:users,id', 'date' => 'required'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), true);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $user = \App\User::find(Auth::user()->id);



            $model = new \App\User();
            $model = $model->where('id', $request->coach_id);
            $requestDay = date('N', strtotime($request->date));
//            dd(json_decode($model->first()->availability_week));
//            if (!in_array($requestDay, json_decode($model->first()->availability_week)))

            $booking = new \App\CoachBooking();
            $booking = $booking->where('coach_id', $request->coach_id);
            $bookingIds = $booking->get()->pluck('id')->toarray();
//             dd($bookingIds);
            $bookingspaces = \App\CoachBookingDetail::whereIn('booking_id', $bookingIds)->whereDate('booking_date', $request->date);
            $bookingspaces = $bookingspaces->get();
//            dd($bookingspace->pluck('from_time')->toarray());
            $bookedslotss = [];


            foreach ($bookingspaces as $bookingspace):
                $bookedslotss[] = [$bookingspace->from_time, $bookingspace->to_time];
            endforeach;
//            dd(+$bookedslotss+
//            );
//            $slots = self::splitTimeWithBookedhours($model->first()->business_hour_starts, $model->first()->business_hour_ends, 1 * 60, $bookedslotss);
////            dd($slots);
//            $available = [];
//            foreach ($slots as $slot):
//                $available[] = [$slot[0], date('H:i', strtotime($slot[count($slot) - 1] . '+ 1 hour'))];
//            endforeach;
            //event slots start//
//            $eventbooking = new \App\Booking();
//            $eventbooking = $eventbooking->where('owner_id', $request->coach_id);
//            $eventbookingIds = $eventbooking->get()->pluck('target_id')->toarray();
//             dd($eventbookingIds);
            $events = new \App\Event();
//            $events = \App\Event::whereIn('id', $eventbookingIds)->whereDate('start_date', $request->date);
            $events = \App\Event::where('created_by', $request->coach_id)
                            ->whereRaw('"' . $request->date . '" between `start_date` and `end_date`')->get();

            $slots = self::splitTimeWithBookedhours($model->first()->business_hour_starts, $model->first()->business_hour_ends, 1 * 60, $bookedslotss);

//            dd($slots);
            $available = [];
            foreach ($slots as $slot):
                $available[] = [$slot[0], date('H:i', strtotime($slot[count($slot) - 1] . '+ 1 hour'))];

            endforeach;

//ok
            $bookedeventslotss = [];

            if ($events->isEmpty())
                return parent::success(['available_slot' => $available, 'event_slot' => []]);

//          
            foreach ($events as $event):

                $abc = [$event->images_1, $event->images_2, $event->images_3, $event->images_4, $event->images_5];
//            dd($abc);
                $ab = array_filter($abc, 'strlen');
                $abcd = json_encode($ab);
//            dd($ab);  
                $bookedeventslotss[] = ['id' => $event->id, 'name' => $event->name, 'description' => $event->description, 'start_date' => $event->start_date, 'end_date' => $event->end_date, 'start_time' => $event->start_time, 'end_time' => $event->end_time, 'price' => $event->price, 'images_1' => $event->images_1, 'images_2' => $event->images_2, 'images_3' => $event->images_3, 'images_4' => $event->images_4, 'images_5' => $event->images_5, 'location' => $event->location, 'latitude' => $event->latitude, 'longitude' => $event->longitude, 'service_id' => $event->service_id, 'created_by' => $event->created_by, 'guest_allowed' => $event->guest_allowed, 'equipment_required' => $event->equipment_required, 'guest_allowed_left' => $event->guest_allowed_left, 'sport_id' => $event->sport_id, 'images' => $abcd];
            endforeach;


            return parent::success(['available_slot' => $available, 'event_slot' => $bookedeventslotss]);
        } catch (\Exception $ex) {

            return parent::error($ex->getMessage());
        }
    }

    public function store(Request $request) {

        $rules = ['coach_id' => 'required', 'service_id' => 'required', 'price' => '', 'token' => 'required', 'booking' => 'required'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {

            $params = json_decode($request->booking);
            if (!isset($request->token))
                return parent::error('Please add token');
            $input = $request->all();
            $input['athlete_id'] = \Auth::id();
            $targetModel = new \App\User();
            $targetModeldata = $targetModel->whereId($request->coach_id)->get();
            if ($targetModeldata->isEmpty())
                return parent::error('Please use valid coach id');
//            $checkData = MyModel::where('target_id', $request->target_id)->where('type', $request->type)->get();
//            if ($checkData->isEmpty() === false):
//                return parent::error(['message' => $request->target_id . ' already booked']);
//            endif;
//
//            dd($targetModelupdate);



            $booking = \App\CoachBooking::create($input);

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
                \App\CoachBookingDetail::create(['booking_id' => $booking->id, 'booking_date' => $param->booking_date, 'from_time' => $param->from_time, 'to_time' => $param->to_time]);
            endforeach;
//            $booking->payment_id = $stripe->id;
            /*             * ***target model update start*** */
            $targetModelupdate = $targetModel->findOrFail($request->coach_id);
            $targetModelupdate->save();
            /*             * ***target model update end*** */


            return parent::successCreated(['message' => 'Booked Successfully', 'booking' => $booking]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

}
