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

    private $_MSGCreate = ['title' => 'Hey!', 'body' => 'New event has created'];

    public function store(Request $request) {
//        parent::pushNotificationsUserRoles(['title' => $this->_MSGCreate['title'], 'body' => $this->_MSGCreate['body'], 'data' => ['target_id' => '1', 'target_model' => 'event']], '3', true);
//        dd('s');
        $rules = ['name' => 'required', 'description' => 'required', 'start_date' => 'required|date_format:"Y-m-d"|after_or_equal:\' . \Carbon\Carbon::now()', 'end_date' => 'required|date_format:"Y-m-d"|after_or_equal:\' . \Carbon\Carbon::now()', 'start_time' => 'required|after_or_equal:\' . \Carbon\Carbon::now()', 'end_time' => 'required|after_or_equal:\' . \Carbon\Carbon::now()', 'price' => 'required', 'images_1' => 'required', 'images_2' => '', 'images_3' => '', 'images_4' => '', 'images_5' => '', 'location' => 'required', 'latitude' => 'required', 'longitude' => 'required', 'service_id' => 'required', 'guest_allowed' => 'required', 'equipment_required' => 'required'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {

            $input = $request->all();

            $input['created_by'] = \Auth::id();
            $input['state'] = '1';

            $images = [];

            for ($i = 1; $i <= 5; $i++):
                $var = 'images_' . $i;
                if (isset($request->$var))
                    $input[$var] = parent::__uploadImage($request->file($var), public_path('uploads/events'), true);
            endfor;

//            if (count($images) > 0)
//                $input['images'] = json_encode($images);
            $input['guest_allowed_left'] = $request->guest_allowed;
            $event = MyModel::create($input);
            parent::pushNotificationsUserRoles(['title' => $this->_MSGCreate['title'], 'body' => $this->_MSGCreate['body'], 'data' => ['target_id' => $event->id, 'target_model' => 'event']], '3', true);
            return parent::successCreated(['message' => 'Created Successfully', 'event' => $event]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function Update(Request $request) {
        $rules = ['id' => 'required', 'name' => '', 'description' => '', 'start_date' => '', 'end_date' => '', 'start_time' => '', 'end_time' => '', 'price' => '', 'images_1' => '', 'images_2' => '', 'images_3' => '', 'images_4' => '', 'images_5' => '', 'location' => '', 'latitude' => '', 'longitude' => '', 'guest_allowed' => '', 'equipment_required' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            if (MyModel::where('id', $request->id)->where('created_by', \Auth::id())->get()->isEmpty() === true)
                return parent::error('Please use valid id');
            $input = $request->all();
            $images = [];

            for ($i = 1; $i <= 5; $i++):
                $var = 'images_' . $i;
                if (isset($request->$var)):
                    $input[$var] = parent::__uploadImage($request->file($var), public_path('uploads/events'));
                endif;
            endfor;

//            if (count($images) > 0)
//                $input['images'] = json_encode($images);
            $event = MyModel::findOrFail($request->id);
            if (isset($request->guest_allowed)):
                if ($request->guest_allowed < $event->guest_allowed)
                    return parent::error('You are not allowed to reduce guest allowed');
                $input['guest_allowed_left'] = $event->guest_allowed_left + ($request->guest_allowed - $event->guest_allowed);
            endif;
            $event->fill($input);
            $event->save();
            return parent::successCreated(['Message' => 'Updated Successfully', 'event' => $event]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getOrganiserEvents(Request $request) {

        $rules = ['order_by' => '', 'search' => '', 'limit' => ''];

        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
//        dd($rules);
        // dd($category_id);
        try {


            $model = new MyModel();
            $model = MyModel::where('created_by', \Auth::id())->Select('id', 'name', 'description', 'start_date', 'end_date', 'start_time', 'end_time', 'price', 'images_1', 'images_2', 'images_3', 'images_4', 'images_5', 'location', 'latitude', 'longitude', 'service_id', 'created_by', 'guest_allowed', 'guest_allowed_left', 'equipment_required');

//            dd(\Carbon\Carbon::now()->toDate());
            if ($request->order_by == 'upcoming')
                $model = $model->whereDate('start_date', '>=', \Carbon\Carbon::now());
            if ($request->order_by == 'completed')
                $model = $model->whereDate('start_date', '<', \Carbon\Carbon::now());
            if (isset($request->search))
                $model = $model->Where('name', 'LIKE', "%$request->search%")
                        ->orWhere('description', 'LIKE', "%$request->search%");
            $perPage = isset($request->limit) ? $request->limit : 20;
            return parent::success($model->paginate($perPage));
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getCoachEvents(Request $request) {
        //Validating attributes
        $rules = ['order_by' => '', 'search' => '', 'limit' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $user = \App\User::findOrFail(\Auth::id());
            if ($user->get()->isEmpty())
                return parent::error('User Not found');
            if ($user->hasRole('coach') === false)
                return parent::error('Please use valid auth token');



            $model = new MyModel();
            $model = MyModel::where('created_by', \Auth::id())->Select('id', 'name', 'description', 'start_date', 'end_date', 'start_time', 'end_time', 'price', 'images_1', 'images_2', 'images_3', 'images_4', 'images_5', 'location', 'latitude', 'longitude', 'service_id', 'created_by', 'guest_allowed', 'guest_allowed_left', 'equipment_required');
            if ($request->order_by == 'upcoming')
                $model = $model->whereDate('start_date', '>=', \Carbon\Carbon::now());
            if ($request->order_by == 'completed')
                $model = $model->whereDate('start_date', '<', \Carbon\Carbon::now());
            if (isset($request->search))
                $model = $model->Where('name', 'LIKE', "%$request->search%")
                        ->orWhere('description', 'LIKE', "%$request->search%");
            $perPage = isset($request->limit) ? $request->limit : 20;
            return parent::success($model->paginate($perPage));
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getAthleteEvents(Request $request) {
        $rules = ['search' => '', 'radius' => 'required', 'order_by' => 'required|in:distance,price_high,price_low,latest', 'limit' => '', 'coach_id' => ''];
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
                return parent::error('Please use valid auth token');

            $model = new MyModel();
            $perPage = isset($request->limit) ? $request->limit : 20;


            if (isset($request->search))
                $model = $model->Where('name', 'LIKE', "%$request->search%")
                        ->orWhere('description', 'LIKE', "%$request->search%");
            switch ($request->order_by):
                case 'price_high':
                    $model = $model->orderBy('price', 'desc');
                    break;
                case 'price_low':
                    $model = $model->orderBy('price', 'asc');
                    break;
                case 'latest':
                    $model = $model->orderBy('created_at', 'desc');
                    break;
                default :
                    $model = $model->orderBy('created_at', 'desc');
                    break;
            endswitch;
            $latKey = 'latitude';
            $lngKey = 'longitude';
            $model = $model->select('id', 'name', 'description', 'start_date', 'end_date', 'start_time', 'end_time', 'price', 'images_1', 'images_2', 'images_3', 'images_4', 'images_5', 'location', 'latitude', 'longitude', 'service_id',
                    'created_by', 'guest_allowed', 'guest_allowed_left', 'equipment_required', \DB::raw('( 3959 * acos( cos( radians(' . $user->latitude . ') ) * cos( radians( ' . $latKey . ' ) ) * cos( radians( ' . $lngKey . ' ) - radians(' . $user->longitude . ') ) + sin( radians(' . $user->latitude . ') ) * sin( radians(' . $latKey . ') ) ) ) AS distance'));

            if ($request->coach_id) {
                $model = $model->where('created_by', $request->input('coach_id'));
            }

//            $model = $model->havingRaw('distance < ' . $request->radius . '');
            $model = $model->orderBy('distance', 'desc');
            $model = $model->whereDate('start_date', '>=', \Carbon\Carbon::now());
            return parent::success($model->paginate($perPage));
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getitem(Request $request) {

        $rules = ['id' => 'required'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), true);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
            $model = new MyModel();
            $model = $model->where('id', $request->id);
            return parent::success($model->first());
        } catch (\Exception $ex) {

            return parent::error($ex->getMessage());
        }
    }

}
