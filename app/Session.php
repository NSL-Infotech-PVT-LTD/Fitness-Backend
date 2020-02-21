<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Session extends Model {

    use LogsActivity;
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sessions';

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
    protected $fillable = ['name', 'description', 'start_date', 'end_date', 'start_time','end_time','location', 'latitude', 'longitude','hourly_rate', 'images_1','images_2','images_3','images_4','images_5', 'phone', 'guest_allowed','guest_allowed_left', 'created_by','state','sport_id'];

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
     public function getRatingAttribute($value)
    {
        return $value == null ? '0' : number_format((float)$value, 2, '.', '');

    }
    
       protected $appends = array('images','IsBooked');

    public function getImagesAttribute() {
        $images = [];
        for ($i = 1; $i <= 5; $i++):
            $var = 'images_' . $i;
            if (isset($this->$var))
                $images[] = $this->$var;
        endfor;
        return json_encode($images);
    }
    
     public function getIsBookedAttribute() {

        $model = Booking::where('target_id', $this->id)->get();
        if ($model->isEmpty() !== true):
            return true;
        else:
            return false;
        endif;
    }

}
