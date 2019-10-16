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
    public function store(Request $request) {

        $rules = ['type' => 'required', 'target_id' => 'required', 'user_id' => '', 'tickets' => '', 'price' => 'required',
            'payment_details' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
            $input['user_id'] = \Auth::id();

            $booking = \App\Booking::create($input);
            return parent::successCreated(['message' => 'Created Successfully', 'booking' => $booking]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }

        public function getOrganisers(Request $request) {
            $rules = [];
            $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
            if ($validateAttributes):
                return $validateAttributes;
            endif;

            try {
                $model = new MyModel();
                $model = \App\Booking::where('created_by', \Auth::id())->Select('type', 'target_id', 'user_id', 'tickets', 'price');
                $perPage = isset($request->limit) ? $request->limit : 20;
                return parent::success($model->first());
                return parent::success($model->paginate($perPage));
            } catch (\Exception $ex) {
                return parent::error($ex->getMessage());
            }
        }
    }
}
