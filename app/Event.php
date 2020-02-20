<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model {

    use LogsActivity;
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'events';

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
    protected $fillable = ['name', 'description', 'start_date', 'end_date', 'start_time', 'end_time', 'price', 'images_1', 'images_2', 'images_3', 'images_4', 'images_5', 'location', 'latitude', 'longitude', 'created_by', 'guest_allowed', 'guest_allowed_left', 'equipment_required', 'state', 'sport_id'];

    /**
     * Change activity log event description
     *
     * @param string $eventName
     *
     * @return string
     */
    public function getDescriptionForEvent($eventName) {
        return __CLASS__ . " model has been {$eventName}";
    }

    public function getDistanceAttribute($value) {
        return $value == null ? '0' : number_format((float) $value, 2, '.', '');
    }

    public function getRatingAttribute($value) {
        return $value == null ? '0' : number_format((float) $value, 2, '.', '');
    }

    protected $appends = array('images', 'IsBooked');

    public function getBookingAttribute($value) {
        return $value == null ? '0' : number_format((float) $value, 2, '.', '');
    }

    public function getIsBookedAttribute() {

        $model = Booking::where('target_id', $this->id)->get();
        if ($model->isEmpty() !== true):
            return true;
        else:
            return false;
        endif;
    }

    public function getIsExpiredAttribute() {
        $model = Event::where('id', $this->id)->get();

        $model = $model->whereDate('start_date', '>=', \Carbon\Carbon::now())->get();
        if ($model->isEmpty() !== true):
            return true;
        else:
            return false;
        endif;
    }

    public function getImagesAttribute() {
        $images = [];
        for ($i = 1; $i <= 5; $i++):
            $var = 'images_' . $i;
            if (isset($this->$var))
                $images[] = $this->$var;
        endfor;
        return json_encode($images);
    }

}
