<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganiserCoach extends Model
{
    protected $fillable = ['name', 'profile_image', 'bio', 'sport_id', 'organisation_id','hourly_rate','experience_detail', 'expertise_years', 'profession','training-service_detail'];

    public function getSportIdAttribute($value) {
//        dd($value);
        return $value == null ? "" : json_decode($value);
    }
}
