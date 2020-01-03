<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingSpace extends Model {

    protected $fillable = ['booking_id', 'booking_date', 'from_time', 'to_time', 'hours'];

    public function userDetails() {
        return $this->hasOne(User::class, 'id', 'user_id')->select('name', 'email', 'phone',
                        'address', 'profile_image', 'location', 'business_hour_starts', 'business_hour_ends', 'bio',
                        'expertise_years', 'hourly_rate', 'portfolio_image_1', 'portfolio_image_2', 'portfolio_image_3', 'portfolio_image_4', 'latitude', 'longitude', 'id');
    }

}
