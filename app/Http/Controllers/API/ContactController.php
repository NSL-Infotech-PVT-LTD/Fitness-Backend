<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Contact as MyModel;
use Twilio\Rest\Client;
use Validator;
use DB;
use Auth;

class ContactController extends ApiController {

    public function store(Request $request) {
        $rules = ['message' => 'required', 'media' => ''];
        $validateAttributes = parent::validateAttributes($request, 'POST', $rules, array_keys($rules), false);
        if ($validateAttributes):
            return $validateAttributes;
        endif;
        try {
            $input = $request->all();
            $input['created_by'] = \Auth::id();
            if (isset($request->media))
            $input['media'] = parent::__uploadImage($request->file('media'), public_path('uploads/contact'), false);
            $contact = MyModel::create($input);
            return parent::successCreated(['message' => 'Submitted Successfully', 'contact' => $contact]);
        } catch (\Exception $ex) {
            return parent::error($ex->getMessage());
        }
    }

}
