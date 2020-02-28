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
        return $this->hasOne(User::class, 'id', 'coach_id')->select('name', 'email', 'phone',
                        'address', 'profile_image', 'location', 'business_hour_starts', 'business_hour_ends', 'bio',
                        'expertise_years', 'hourly_rate', 'portfolio_image_1', 'portfolio_image_2', 'portfolio_image_3', 'portfolio_image_4', 'latitude', 'longitude', 'id');
    }

    public function events() {
        return $this->hasOne(Event::class, 'created_by', 'coach_id')->select('id', 'name', 'description', 'start_date', 'end_date', 'start_time', 'end_time', 'price', 'images_1', 'images_2', 'images_3', 'images_4', 'images_5', 'location', 'latitude', 'longitude', 'service_id', 'created_by', 'guest_allowed', 'equipment_required', 'guest_allowed_left', 'sport_id');
    }

   

   

   

}
