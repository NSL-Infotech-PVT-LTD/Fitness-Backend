<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Event as MyModel;
use Twilio\Rest\Client;
use Validator;
use DB;
use Auth;

class EventsController extends ApiController {

    public function store(Request $request) {

        $rules = ['name' => 'required', 'description' => 'required', 'start_at' => 'required', 'end_at' => 'required', 'location' => 'required', 'latitude' => 'required', 'longitude' => 'required', 'service_id' => 'required', 'guest_allowed' => 'required', 'equipment_required' => 'required'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
            $input['organizer_id'] = \Auth::id();
            $event = MyModel::create($input);
            return parent::successCreated(['message' => 'Created Successfully', 'event' => $event]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function Update(Request $request) {
        $rules = ['id' => 'required', 'name' => '', 'description' => '', 'start_at' => '', 'end_at' => '', 'location' => '', 'latitude' => '', 'longitude' => '', 'guest_allowed' => '', 'equipment_required' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            if (MyModel::where('id', $request->id)->where('organizer_id', \Auth::id())->get()->isEmpty() === true)
                return parent::error('Please use valid Address id');
            $input = $request->all();
            $address = MyModel::findOrFail($request->id);
            $address->fill($input);
            $address->save();
            return parent::successCreated(['Message' => 'Updated Successfully', 'address' => $address]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getOrganiserEvents(Request $request) {
        $rules = [];
        $validateAttributes = parent::validateAttributes($request, 'GET', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {


            $model = new MyModel();
            $model = MyModel::where('organizer_id', \Auth::id())->Select('id', 'name', 'description', 'start_at', 'end_at', 'location', 'latitude', 'longitude', 'service_id', 'organizer_id', 'guest_allowed', 'equipment_required');

            return parent::success($model->get());
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

   

    public function getCoachEvents(Request $request) {
        //Validating attributes
        $user = \App\User::findOrFail(\Auth::id());
        if ($user->get()->isEmpty())
            return parent::error('User Not found');
        if ($user->hasRole('coach') === false)
            return parent::error('Please use valid token');
        $rules = [];
        $validateAttributes = parent::validateAttributes($request, 'GET', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
           
            $model = new MyModel();
            $model = MyModel::where('organizer_id', \Auth::id())->Select('id', 'name', 'description', 'start_at', 'end_at', 'location', 'latitude', 'longitude', 'service_id', 'organizer_id', 'guest_allowed', 'equipment_required');
            return parent::success($model->get());
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }
    
    
     public function getAthleteEvents(Request $request) {
        $rules = ['radius' => 'required', 'order_by' => 'required|in:distance'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            //Validating attributes
            $user = \App\User::findOrFail(\Auth::id());
            if ($user->get()->isEmpty())
                return parent::error('User Not found');
            if ($user->hasRole('athlete') === false)
                return parent::error('Please use valid token');

            $model = new MyModel();
            if (isset($request->search))
                $model = $model->Where('name', 'LIKE', "%$request->search%");

            $latKey = 'latitude';
            $lngKey = 'longitude';
            $model = $model->select('id', 'name', 'description', 'start_at', 'end_at', 'location', 'latitude', 'longitude', 'service_id',
                    'organizer_id', 'guest_allowed', 'equipment_required', \DB::raw('( 3959 * acos( cos( radians(' . $user->latitude . ') ) * cos( radians( ' . $latKey . ' ) ) * cos( radians( ' . $lngKey . ' ) - radians(' . $user->longitude . ') ) + sin( radians(' . $user->latitude . ') ) * sin( radians(' . $latKey . ') ) ) ) AS distance'));
           
            $model = $model->havingRaw('distance < ' . $request->radius . '');
            $model = $model->orderBy('distance');
//           
            return parent::success($model->get());
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

}
