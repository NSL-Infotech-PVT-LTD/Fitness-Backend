<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Space as MyModel;
use Twilio\Rest\Client;
use Intervention\Image\ImageManagerStatic as Image;
use Validator;
use DB;
use Auth;

class SpacesController extends ApiController {

    public function store(Request $request) {

        $rules = ['name' => 'required', 'images_1' => 'required', 'images_2' => '', 'images_3' => '', 'images_4' => '', 'images_5' => '', 'description' => 'required','location'=>'required','latitude'=>'required','longitude'=>'required', 'price_hourly' => 'required', 'availability_week' => 'required', 'open_hours_from'=>'required|after_or_equal:\' . \Carbon\Carbon::now()','open_hours_to'=>'required|after_or_equal:\' . \Carbon\Carbon::now()','price_daily' => 'required'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
            $input['created_by'] = \Auth::id();
            $input['state']= '1';
            $images = [];

            for ($i = 1; $i <= 5; $i++):
                $var = 'images_' . $i;
                if (isset($request->$var))
                    $images[] = parent::__uploadImage($request->file($var), public_path('uploads/spaces'),true);
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
        $rules = ['name' => 'required', 'images_1' => '', 'images_2' => '', 'images_3' => '', 'images_4' => '', 'images_5' => '', 'location'=>'','latitude'=>'','longitude'=>'','description' => '', 'price_hourly' => '', 'availability_week' => '', 'open_hours_from'=>'','open_hours_to'=>'','price_daily' => ''];
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
            $input['state']= '1';
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
        $rules = ['search'=>'','limit'=>''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
//            $model = new MyModel();
            $model = MyModel::where('created_by', \Auth::id())->Select('id', 'name', 'images', 'description', 'price_hourly', 'location', 'latitude', 'longitude','availability_week','open_hours_from','open_hours_to', 'created_by', 'price_daily');
            if (isset($request->search))
                $model = $model->Where('name', 'LIKE', "%$request->search%")
                    ->orWhere('description', 'LIKE', "%$request->search%");
            $perPage = isset($request->limit) ? $request->limit : 20;
            return parent::success($model->paginate($perPage));
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getCoachSpaces(Request $request) {
        //Validating attributes
        $rules = ['search'=>'','limit'=>''];
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
            $model = MyModel::where('created_by', \Auth::id())->Select('id', 'name', 'images', 'description', 'price_hourly', 'location', 'latitude', 'longitude','availability_week', 'open_hours_from','open_hours_to','created_by', 'price_daily');


            if (isset($request->search))
                $model = $model->Where('name', 'LIKE', "%$request->search%")
                    ->orWhere('description', 'LIKE', "%$request->search%");
            $perPage = isset($request->limit) ? $request->limit : 20;
            return parent::success($model->paginate($perPage));
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getAthleteSpaces(Request $request) {
        //Validating attributes
        $rules = ['search' => '', 'order_by' => 'required|in:price_high,price_low,latest,distance', 'limit' => '','organiser_id'=>''];
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
            $model = $model->select('id', 'name', 'images', 'description', 'price_hourly', 'availability_week', 'open_hours_from','open_hours_to','created_by', 'price_daily', 'location', 'latitude', 'longitude', \DB::raw('( 3959 * acos( cos( radians(' . $user->latitude . ') ) * cos( radians( ' . $latKey . ' ) ) * cos( radians( ' . $lngKey . ' ) - radians(' . $user->longitude . ') ) + sin( radians(' . $user->latitude . ') ) * sin( radians(' . $latKey . ') ) ) ) AS distance'));

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

            if($request->organiser_id){
                $model = $model->where('created_by', $request->input('organiser_id'));

            }

            if ($user->hasRole('organizer') === true){
                $model = $model->where('created_by', '!=',\Auth::id());
            }

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
