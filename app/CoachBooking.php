<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Event;

class CoachBooking extends Model {

    protected $fillable = ['coach_id', 'athlete_id', 'service_id', 'price', 'status', 'payment_details'];

    public function getServiceIdAttribute($value) {
        return $value == null ? [] : json_decode($value);
    }

    public function userDetails() {
        return $this->hasOne(User::class, 'id', 'user_id')->select('name', 'email', 'phone',
                        'address', 'profile_image', 'location', 'business_hour_starts', 'business_hour_ends', 'bio',
                        'expertise_years', 'hourly_rate', 'portfolio_image_1', 'portfolio_image_2', 'portfolio_image_3', 'portfolio_image_4', 'latitude', 'longitude', 'id');
    }

    public function events() {
        return $this->hasOne(Event::class, 'created_by', 'coach_id')->select('id', 'name', 'description', 'start_date', 'end_date', 'start_time', 'end_time', 'price', 'images_1', 'images_2', 'images_3', 'images_4', 'images_5', 'location', 'latitude', 'longitude', 'service_id', 'created_by', 'guest_allowed', 'equipment_required', 'guest_allowed_left', 'sport_id');
    }

    protected $appends = array('booking_date','images');

    public function getBookingDateAttribute() {

        switch ($this->type):

            case 'coach':
                $targetModel = new \App\CoachBookingDetail();
                $model = $targetModel->where('booking_id', $this->id)->get();
//                dd($this->id);
//                dd($model->first()->booking_date);
                if ($model->isEmpty() !== true)
                    return ['start' => $model->first()->booking_date, 'end' => $model->last()->booking_date];

        endswitch;
        return [];
    }

    public function getImagesAttribute() {
        $model = Event::where('id', $this->created_by)->get();
        $images = [];
        for ($i = 1; $i <= 5; $i++):
            $var = 'images_' . $i;
            if (isset($model->$var))
                $images[] = $model->$var;
        endfor;
        return json_encode($images);
    }

}
