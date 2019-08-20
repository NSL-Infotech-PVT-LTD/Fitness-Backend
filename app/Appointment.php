<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model {

    use LogsActivity;
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'appointments';

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
    protected $fillable = ['service_id', 'date', 'start_time', 'end_time', 'comments', 'salon_user_id', 'customer_user_id'];

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

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getServiceIdAttribute($value) {
        try {
            $model = Service::where('id', $value)->get();
            if ($model->isEmpty() !== true)
                return $model->first()->name;
            else
                return $value;
        } catch (Exception $ex) {
            return $value;
        }
    }

}
