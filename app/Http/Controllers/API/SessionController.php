<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Session as MyModel;
use Twilio\Rest\Client;
use Intervention\Image\ImageManagerStatic as Image;
use Validator;
use DB;
use Auth;

class SessionController extends ApiController
{
 private $_MSGCreate = ['title' => 'Hey!', 'body' => 'New session has created'];
    public function store(Request $request)
    {

        $rules = ['name' => 'required', 'description' => 'required', 'start_date' => 'required|date_format:"Y-m-d"|after_or_equal:\' . \Carbon\Carbon::now()', 'end_date' => 'required|date_format:"Y-m-d"|after_or_equal:\' . \Carbon\Carbon::now()', 'start_time' => 'required|after_or_equal:\' . \Carbon\Carbon::now()', 'end_time' => 'required|after_or_equal:\' . \Carbon\Carbon::now()', 'hourly_rate' => 'required', 'images_1' => 'required', 'images_2' => '', 'images_3' => '', 'images_4' => '', 'images_5' => '', 'phone' => 'required','location'=>'required','latitude'=>'required','longitude'=>'required', 'guest_allowed' => 'required'];
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
                    $input[$var] = parent::__uploadImage($request->file($var), public_path('uploads/session'),true);
            endfor;

//            if (count($images) > 0)
//                $input['images'] = json_encode($images);
            $input['guest_allowed_left'] =$request->guest_allowed;
            $session = MyModel::create($input);
             parent::pushNotificationsUserRoles(['title' => $this->_MSGCreate['title'], 'body' => $this->_MSGCreate['body'], 'data' => ['target_id' => $session->id,'target_model'=>'session']], '3', true);
            return parent::successCreated(['message' => 'Created Successfully', 'session' => $session]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function Update(Request $request)
    {
        $rules = ['id' => 'required','name' => '', 'description' => '', 'start_date' => 'after_or_equal:\' . \Carbon\Carbon::now()', 'end_date' => 'after_or_equal:\' . \Carbon\Carbon::now()', 'start_time' => 'after_or_equal:\' . \Carbon\Carbon::now()', 'end_time' => 'after_or_equal:\' . \Carbon\Carbon::now()', 'hourly_rate' => '', 'images_1' => '', 'images_2' => '', 'images_3' => '', 'images_4' => '', 'images_5' => '', 'phone' => '', 'location'=>'', 'latitude'=>'', 'longitude'=>'','guest_allowed' => '', 'created_by' => ''];
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
                    $input[$var] = parent::__uploadImage($request->file($var), public_path('uploads/session'));
            endfor;

//            if (count($images) > 0)
//                $input['images'] = json_encode($images);
            $input['state']= '1';
            $session = MyModel::findOrFail($request->id);
            $session->fill($input);
            $session->save();
            return parent::successCreated(['Message' => 'Updated Successfully', 'session' => $session]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        $rules = ['id' => 'required'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
//         dd($id);
        try {
            if (MyModel::where('id', $request->id)->where('created_by', \Auth::id())->get()->isEmpty() === true)
                return parent::error('Please use valid id');
            MyModel::destroy($request->id);
            return parent::success(['message' => 'Deleted Successfully']);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getOrganiserSession(Request $request)
    {
        $rules = ['order_by'=>'','search'=>'','limit'=>''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
//            $model = new MyModel();
            $model = MyModel::where('created_by', \Auth::id())->Select('id', 'name', 'description', 'start_date', 'end_date', 'start_time','end_time','hourly_rate','location', 'latitude', 'longitude', 'images_1','images_2','images_3','images_4','images_5', 'phone', 'guest_allowed', 'guest_allowed_left','created_by');
            if ($request->order_by == 'upcoming')
                $model = $model->whereDate('start_date','>=',\Carbon\Carbon::now());
            if ($request->order_by == 'completed')
                $model = $model->whereDate('start_date','<=',\Carbon\Carbon::now());
            if (isset($request->search))
                $model = $model->Where('name', 'LIKE', "%$request->search%");
            $perPage = isset($request->limit) ? $request->limit : 20;
            return parent::success($model->paginate($perPage));
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getCoachSession(Request $request)
    {
        //Validating attributes
        $rules = ['order_by'=>'','search'=>'','limit'=>''];
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


            $model = MyModel::where('created_by', \Auth::id())->Select('id', 'name', 'description', 'start_date', 'end_date', 'start_time','end_time','location', 'latitude', 'longitude','hourly_rate', 'images_1','images_2','images_3','images_4','images_5', 'phone', 'guest_allowed','guest_allowed_left', 'created_by');
            if ($request->order_by == 'upcoming')
                $model = $model->whereDate('start_date','>=',\Carbon\Carbon::now());
            if ($request->order_by == 'completed')
                $model = $model->whereDate('start_date','<=',\Carbon\Carbon::now());
            if (isset($request->search))
                $model = $model->Where('name', 'LIKE', "%$request->search%");
            $perPage = isset($request->limit) ? $request->limit : 20;
            return parent::success($model->paginate($perPage));
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getAthleteSession(Request $request)
    {
        //Validating attributes
        $rules = ['search' => '', 'order_by' => 'required|in:price_high,price_low,latest,distance', 'limit' => '','coach_id'=>''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $user = \App\User::findOrFail(\Auth::id());
            if ($user->get()->isEmpty())
                return parent::error('User Not found');
            if ($user->hasRole('athlete') === false)
                return parent::error('Please use valid token');


            $model = new MyModel();
            $perPage = isset($request->limit) ? $request->limit : 20;
            $latKey = 'latitude';
            $lngKey = 'longitude';
            $model = $model->select('id', 'name', 'description', 'start_date', 'end_date', 'start_time','end_time','hourly_rate', 'location', 'latitude', 'longitude', 'images_1','images_2','images_3','images_4','images_5', 'phone', 'guest_allowed','guest_allowed_left','created_by', \DB::raw('( 3959 * acos( cos( radians(' . $user->latitude . ') ) * cos( radians( ' . $latKey . ' ) ) * cos( radians( ' . $lngKey . ' ) - radians(' . $user->longitude . ') ) + sin( radians(' . $user->latitude . ') ) * sin( radians(' . $latKey . ') ) ) ) AS distance'));
//            $model = $model->havingRaw('distance < ' . $request->radius . '');
            if (isset($request->search))
                $model = $model->Where('name', 'LIKE', "%$request->search%");
            switch ($request->order_by):
                case 'price_high':
                    $model = $model->orderBy('hourly_rate', 'desc');
                    break;
                case 'price_low':
                    $model = $model->orderBy('hourly_rate', 'asc');
                    break;
                case 'latest':
                    $model = $model->orderBy('created_at', 'desc');
                    break;
                case 'distance':
                    $model = $model->orderBy('distance');
                    break;
                default :
                    $model = $model->orderBy('distance');
                    break;
            endswitch;

            if($request->coach_id){
                $model = $model->where('created_by', $request->input('coach_id'));

            }
            $model = $model->whereDate('start_date', '>=', \Carbon\Carbon::now());
            return parent::success($model->paginate($perPage));
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getitem(Request $request)
    {

        $rules = ['id' => 'required'];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), true);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
            $model = new \App\Session();
            $model = $model->where('id', $request->id);
            return parent::success($model->first());
        } catch (\Exception $ex) {

            return parent::error($ex->getMessage());
        }

    }

}
