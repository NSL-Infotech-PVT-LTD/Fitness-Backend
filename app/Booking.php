<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
            
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
    protected $fillable = ['type', 'user_id', 'target_id', 'tickets', 'price', 'status', 'payment_details', 'payment_id', 'owner_id', 'rating'];

    public function userDetails() {
        return $this->hasOne(User::class, 'id', 'user_id')->select('name', 'email', 'phone',
                        'address', 'profile_image', 'location', 'business_hour_starts', 'business_hour_ends', 'bio',
                        'expertise_years', 'hourly_rate', 'portfolio_image_1', 'portfolio_image_2', 'portfolio_image_3', 'portfolio_image_4', 'latitude', 'longitude', 'id');
    }

    protected $appends = array('target_data', 'booking_date');

    public function getBookingDateAttribute() {

        switch ($this->type):
            case 'event':
                $targetModel = new \App\Event();
                $model = $targetModel->whereId($this->target_id)->get();
                if ($model->isEmpty() !== true)
                    return ['start' => $model->first()->start_date, 'end' => $model->first()->end_date];
                break;
            case 'space':
                $targetModel = new \App\BookingSpace();
                $model = $targetModel->where('booking_id', $this->id)->get();
//                dd($this->id);
//                dd($model->first()->booking_date);
                if ($model->isEmpty() !== true)
                    return ['start' => $model->first()->booking_date, 'end' => $model->last()->booking_date];
                break;
            case 'session':
                $targetModel = new \App\Session();
                $model = $targetModel->whereId($this->target_id)->get();
                if ($model->isEmpty() !== true)
                    return ['start' => $model->first()->start_date, 'end' => $model->first()->end_date];
                break;
        endswitch;
        return [];
    }

    public function getTargetDataAttribute() {

//            dd($_REQUEST['filter_by']);
        if (isset($_REQUEST['filter_by']))
            $date = \Carbon\Carbon::createFromFormat('Y-m', $_REQUEST['filter_by']);
        try {
            switch ($this->type):
                case 'event':
                    $targetModel = new \App\Event();
                    $targetModel = $targetModel->select('id', 'name', 'description', 'start_date', 'end_date', 'start_time', 'end_time', 'price', 'images_1', 'images_2', 'images_3', 'images_4', 'images_5', 'location', 'latitude', 'longitude', 'created_by', 'guest_allowed', 'guest_allowed_left', 'equipment_required');
                    if (isset($date))
                        $targetModel->whereYear('start_date', $date->year)->whereMonth('start_date', $date->month);
                    break;
                case 'space':
                    $targetModel = new \App\Space();
                    $targetModel = $targetModel->select('id', 'name', 'images_1', 'images_2', 'images_3', 'images_4', 'images_5', 'description', 'price_hourly', 'availability_week', 'location', 'latitude', 'longitude', 'created_by', 'price_daily');
//                    if (isset($date)):
//                        $bookingSpaceIds = BookingSpace::whereYear('booking_date', $date->year)->whereMonth('booking_date', $date->month)->get()->pluck('booking_id')->toarray();
//                    dd($bookingSpaceIds);
//                        $targetModel = $targetModel->whereIn('id', $bookingSpaceIds);
//                        dd($this->target_id);
//                    endif;
                    break;
                case 'session':
                    $targetModel = new \App\Session();
                    $targetModel = $targetModel->select('id', 'name', 'description', 'start_date', 'end_date', 'start_time', 'end_time', 'hourly_rate', 'images_1', 'images_2', 'images_3', 'images_4', 'images_5', 'phone', 'location', 'latitude', 'longitude', 'guest_allowed', 'guest_allowed_left', 'created_by');
//                    dd(\Carbon\Carbon::now()->month);
                    if (isset($date))
                        $targetModel->whereYear('start_date', $date->year)->whereMonth('start_date', $date->month);
                    break;
            endswitch;
            $model = $targetModel->whereId($this->target_id)->get();
            if ($model->isEmpty() !== true)
                return $model->first();
            return [];
        } catch (Exception $ex) {
            return [];
        }
    }

//    public function targetData() {
//dd($this);
//        return $this->hasOne(Event::class, 'id', 'target_id')->select('id','name','description','start_date','end_date','start_time','end_time','price','images','location','latitude','longitude','created_by','guest_allowed','guest_allowed_left','equipment_required');
//    }

    public function event() {
        return $this->hasOne(Event::class, 'id', 'target_id')->select('id', 'name', 'description', 'start_date', 'end_date', 'start_time', 'end_time', 'price', 'images_1', 'images_2', 'images_3', 'images_4', 'images_5', 'location', 'latitude', 'longitude', 'created_by', 'guest_allowed', 'guest_allowed_left', 'equipment_required');
    }

    public function session() {
        return $this->hasOne(Session::class, 'id', 'target_id')->select('id', 'name', 'description', 'start_date', 'end_date', 'start_time', 'end_time', 'hourly_rate', 'images_1', 'images_2', 'images_3', 'images_4', 'images_5', 'phone', 'location', 'latitude', 'longitude', 'guest_allowed', 'guest_allowed_left', 'created_by');
    }

    public function space() {
        return $this->hasOne(Space::class, 'id', 'target_id')->select('id', 'name', 'images_1', 'images_2', 'images_3', 'images_4', 'images_5', 'description', 'price_hourly', 'availability_week', 'location', 'latitude', 'longitude', 'created_by', 'price_daily');
    }

    public function booking_details() {
        return $this->hasMany(BookingSpace::class, 'booking_id', 'id')->select('id', 'booking_id', 'booking_date', 'from_time', 'to_time');
    }

    public function getRatingAttribute($value) {
        return $value == null ? '0' : number_format((float) $value, 2, '.', '');
    }

}
