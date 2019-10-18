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

        $rules = ['name' => 'required', 'description' => 'required', 'start_date' => 'required', 'end_date' => 'required', 'start_time' => 'required', 'end_time' => 'required', 'price' => 'required', 'images_1' => 'required', 'images_2' => '', 'images_3' => '', 'images_4' => '', 'images_5' => '', 'location' => 'required', 'latitude' => 'required', 'longitude' => 'required', 'service_id' => 'required', 'guest_allowed' => 'required', 'equipment_required' => 'required'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
            $input['created_by'] = \Auth::id();
            $images = [];

            for ($i = 1; $i <= 5; $i++):
                $var = 'images_' . $i;
                if (isset($request->$var))
                    $images[] = parent::__uploadImage($request->file($var), public_path('uploads/events'));
            endfor;

            if (count($images) > 0)
                $input['images'] = json_encode($images);
            $event = MyModel::create($input);
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
                if (isset($request->$var))
                    $images[] = parent::__uploadImage($request->file($var), public_path('uploads/events'));
            endfor;

            if (count($images) > 0)
                $input['images'] = json_encode($images);
            $address = MyModel::findOrFail($request->id);
            $address->fill($input);
            $address->save();
            return parent::successCreated(['Message' => 'Updated Successfully', 'address' => $address]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getOrganiserEvents(Request $request) {

        $rules = ['order_by'=>'required|in:upcoming,completed'];

        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
//        dd($rules);
        // dd($category_id);
        try {


            $model = new MyModel();
            $model = MyModel::where('created_by', \Auth::id())->Select('id', 'name', 'description', 'start_date', 'end_date', 'start_time', 'end_time', 'price', 'images', 'location', 'latitude', 'longitude', 'service_id', 'created_by', 'guest_allowed', 'equipment_required');
            if ($request->order_by == 'upcoming')
$model = $model->where('created_at','>=',\Carbon\Carbon::now());
            if ($request->order_by == 'completed')
                $model = $model->where('created_at','<=',\Carbon\Carbon::now());
            return parent::success($model->get());
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getCoachEvents(Request $request) {
        //Validating attributes
        $rules = ['order_by'=>'required|in:upcoming,completed'];
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
            if ($request->order_by == 'upcoming')
                $model = $model->where('created_at','>=',\Carbon\Carbon::now());
            if ($request->order_by == 'completed')
                $model = $model->where('created_at','<=',\Carbon\Carbon::now());
            return parent::success($model->get());
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getAthleteEvents(Request $request) {
        $rules = ['search' => '', 'radius' => 'required', 'order_by' => 'required|in:distance,price_high,price_low,latest', 'limit' => ''];
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
            $perPage = isset($request->limit) ? $request->limit : 20;

            if (isset($request->search))
                $model = $model->Where('name', 'LIKE', "%$request->search%");
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
            $model = $model->select('id', 'name', 'description', 'start_date', 'end_date', 'start_time', 'end_time', 'price', 'images', 'location', 'latitude', 'longitude', 'service_id',
                    'created_by', 'guest_allowed', 'equipment_required', \DB::raw('( 3959 * acos( cos( radians(' . $user->latitude . ') ) * cos( radians( ' . $latKey . ' ) ) * cos( radians( ' . $lngKey . ' ) - radians(' . $user->longitude . ') ) + sin( radians(' . $user->latitude . ') ) * sin( radians(' . $latKey . ') ) ) ) AS distance'));

//            $model = $model->havingRaw('distance < ' . $request->radius . '');
            $model = $model->orderBy('distance');
//
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
