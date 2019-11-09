<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\testnotification;

class User extends Authenticatable {

    use HasApiTokens,
        Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'address', 'profile_image', 'location', 'business_hour_starts', 'business_hour_ends', 'bio', 'profession', 'expertise_years', 'hourly_rate', 'portfolio_image', 'service_ids', 'latitude', 'longitude','sport_id','achievements','experience_detail','profession','training_service_detail'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute() {
        return $this->name;
    }

    protected $appends = array('roles');

    public function getServiceIdsAttribute($value) {
        return $value == null ? [] : json_decode($value);
    }
    public function getSportIdAttribute($value) {
//        dd($value);
//        return $value == null ? "" : json_decode($value);
        return $value == null ? "" : $value;

    }

    public function getRolesAttribute() {
        try {
            $rolesID = \DB::table('role_user')->where('user_id', $this->id)->pluck('role_id');
            if ($rolesID->isEmpty() !== true):
                $role = Role::whereIn('id', $rolesID);
                if ($role->get()->isEmpty() !== true)
                    return $role->select('name')->get();
            endif;
            return [];
        } catch (Exception $ex) {
            return [];
        }
    }

}
