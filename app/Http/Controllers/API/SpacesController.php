<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Space as MyModel;
use Twilio\Rest\Client;
use Validator;
use DB;
use Auth;

class SpacesController extends ApiController {

    public function store(Request $request) {

        $rules = ['name' => 'required', 'images_1' => 'required', 'images_2' => '', 'images_3' => '', 'images_4' => '', 'images_5' => '', 'description' => 'required', 'price_hourly' => 'required', 'availability_week' => 'required', 'price_daily' => 'required'];
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
                    $images[] = parent::__uploadImage($request->file($var), public_path('uploads/spaces'));
            endfor;

            if (count($images) > 0)
                $input['images'] = json_encode($images);
            $space = MyModel::create($input);
            return parent::successCreated(['message' => 'Created Successfully', 'space' => $space]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function Update(Request $request) {
        $rules = ['name' => 'required', 'images_1' => '', 'images_2' => '', 'images_3' => '', 'images_4' => '', 'images_5' => '', 'description' => '', 'price_hourly' => '', 'availability_week' => '', 'price_daily' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            if (MyModel::where('id', $request->id)->where('created_by', \Auth::id())->get()->isEmpty() === true)
                return parent::error('Please use valid created_by id');
            $input = $request->all();
            $input['organizer_id'] = \Auth::id();
            $images = [];

            for ($i = 1; $i <= 5; $i++):
                $var = 'images_' . $i;
                if (isset($request->$var))
                    $images[] = parent::__uploadImage($request->file($var), public_path('uploads/spaces'));
            endfor;

            if (count($images) > 0)
                $input['images'] = json_encode($images);
            $space = MyModel::findOrFail($request->id);
            $space->fill($input);
            $space->save();
            return parent::successCreated(['Message' => 'Updated Successfully', 'space' => $space]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function destroy(Request $request) {
        $rules = ['id' => 'required'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
//         dd($id);
        try {
            if (MyModel::where('id', $request->id)->where('created_by', \Auth::id())->get()->isEmpty() === true)
                return parent::error('Please use valid created_by id');
            MyModel::destroy($request->id);
            return parent::success(['message' => 'Deleted Successfully']);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getOrganiserSpaces(Request $request) {
        $rules = ['order_by'=>'required|in:upcoming,completed'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
//            $model = new MyModel();
            $model = MyModel::where('created_by', \Auth::id())->Select('id', 'name', 'images', 'description', 'price_hourly', 'availability_week', 'created_by', 'price_daily');
            if ($request->order_by == 'upcoming')
                $model = $model->where('created_at','>=',\Carbon\Carbon::now());
            if ($request->order_by == 'completed')
                $model = $model->where('created_at','<=',\Carbon\Carbon::now());
            return parent::success($model->get());
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getCoachSpaces(Request $request) {
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
            return parent::error('Please use valid token');
            $model = new MyModel();
            $model = MyModel::where('created_by', \Auth::id())->Select('id', 'name', 'images', 'description', 'price_hourly', 'availability_week', 'created_by', 'price_daily');
            if ($request->order_by == 'upcoming')
                $model = $model->where('created_at','>=',\Carbon\Carbon::now());
            if ($request->order_by == 'completed')
                $model = $model->where('created_at','<=',\Carbon\Carbon::now());
            return parent::success($model->get());
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getAthleteSpaces(Request $request) {
        //Validating attributes
        $rules = ['search' => '', 'order_by' => 'required|in:price_high,price_low,latest,distance', 'limit' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $user = \App\User::findOrFail(\Auth::id());
            if ($user->get()->isEmpty())
                return parent::error('User Not found');
//            if ($user->hasRole('athlete') === false)
//                return parent::error('Please use valid token');

            $model = new MyModel();
            $perPage = isset($request->limit) ? $request->limit : 20;

            $latKey = 'latitude';
            $lngKey = 'longitude';
            $model = $model->select('id', 'name', 'images', 'description', 'price_hourly', 'availability_week', 'created_by', 'price_daily', 'location', 'latitude', 'longitude', \DB::raw('( 3959 * acos( cos( radians(' . $user->latitude . ') ) * cos( radians( ' . $latKey . ' ) ) * cos( radians( ' . $lngKey . ' ) - radians(' . $user->longitude . ') ) + sin( radians(' . $user->latitude . ') ) * sin( radians(' . $latKey . ') ) ) ) AS distance'));

//            $model = $model->havingRaw('distance < ' . $request->radius . '');
            if (isset($request->search))
                $model = $model->Where('name', 'LIKE', "%$request->search%");
            switch ($request->order_by):
                case 'price_high':
                    $model = $model->orderBy('price_hourly', 'desc');
                    break;
                case 'price_low':
                    $model = $model->orderBy('price_hourly', 'asc');
                    break;
                case 'latest':
                    $model = $model->orderBy('created_at', 'desc');
                case 'distance':
                    $model = $model->orderBy('distance');
                    break;
                default :
                    $model = $model->orderBy('distance');
                    break;
            endswitch;

            return parent::success($model->paginate($perPage));
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

}
