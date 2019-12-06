<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Twilio\Rest\Client;
use Validator;
use DB;
use Auth;

class NotificationController extends ApiController {
    
     public function getnotifications(Request $request) {
        $rules = ['search' => '', 'limit' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
//            $user = \App\User::find(Auth::user()->id);
            $model = new \App\UserNotification();
            $model = $model->where('user_id', \Auth::id())->select('id', 'title', 'body', 'data', 'user_id', 'created_at');
            if (isset($request->search))
                $model = $model->Where('title', 'LIKE', "%$request->search%")
                        ->orWhere('body', 'LIKE', "%$request->search%")
                        ->orWhere('data', 'LIKE', "%$request->search%");
            $perPage = isset($request->limit) ? $request->limit : 20;
            return parent::success($model->paginate($perPage));
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function getUserDashboard(Request $request) {
        $rules = [];
        $validateAttributes = parent::validateAttributes($request, 'GET', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
            $model = new \App\UserNotification();
            $model = $model->where('user_id', \Auth::id())->select('id', 'title', 'body', 'data', 'user_id', 'created_at');
            $model = $model->where('is_read', '0');
            return parent::success(['notification_count' => $model->get()->count()]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

    public function MarkReadNotication(Request $request) {
        $rules = [];
        $validateAttributes = parent::validateAttributes($request, 'GET', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        // dd($category_id);
        try {
            $model = new \App\UserNotification();
            $model = $model->where('user_id', \Auth::id());

            UserNotification::whereIn('id', $model->get()->pluck('id'))->update(['is_read' => '1']);
            return parent::success(['message' => 'Mark Read']);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

}

