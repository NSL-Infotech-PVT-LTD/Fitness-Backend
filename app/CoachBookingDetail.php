<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoachBookingDetail extends Model {

    protected $fillable = ['booking_id', 'booking_date', 'from_time', 'to_time', 'hours'];

    
    
    
}
