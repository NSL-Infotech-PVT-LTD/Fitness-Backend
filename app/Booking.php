<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model {

    use LogsActivity;
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bookings';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['type','user_id','target_id','tickets','price','status','payment_details','space_date_start','space_date_end'];

    public function userDetails() {
        return $this->hasOne(User::class, 'id', 'user_id')->select('name', 'email', 'phone',
            'address','profile_image','location','business_hour_starts','business_hour_ends','bio',
            'expertise_years','hourly_rate','portfolio_image','latitude','longitude','id');
    }


    public function targetData() {
//dd($this->type);
        return $this->hasOne(Event::class, 'id', 'target_id')->select('id','name','description','start_date','end_date','start_time','end_time','price','images','location','latitude','longitude','created_by','guest_allowed','guest_allowed_left','equipment_required');
    }

    public function event() {
        return $this->hasOne(Event::class, 'id', 'target_id')->select('id','name','description','start_date','end_date','start_time','end_time','price','images','location','latitude','longitude','created_by','guest_allowed','guest_allowed_left','equipment_required');
    }

    public function session() {
        return $this->hasOne(Session::class, 'id', 'target_id')->select('id','name','description','business_hour','date','hourly_rate','images','phone','location','latitude','longitude','guest_allowed','guest_allowed_left','created_by');
    }
    public function space() {
        return $this->hasOne(Space::class, 'id', 'target_id')->select('id','name','images','description','price_hourly','availability_week','location','latitude','longitude','created_by','price_daily');
    }

}
