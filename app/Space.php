<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Space extends Model {

    use LogsActivity;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'spaces';

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
    protected $fillable = ['name', 'images_1', 'images_2', 'images_3', 'images_4', 'images_5', 'description', 'price_hourly', 'availability_week', 'open_hours_from', 'open_hours_to', 'location', 'latitude', 'longitude', 'created_by', 'price_daily', 'state'];

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

//    public function getAvailabilityWeekAttribute($value) {
//        try {
//            $weekdays = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wekhd'];
//            $return = [];
//            foreach (json_decode($value) as $key):
//                $return[] = $weekdays[$key];
//            endforeach;
//            return $return;
//        } catch (\Exception $ex) {
//            return [];
//        }
////        return $value == null ? "" : $value;
//    }

    protected $appends = array('images');

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
