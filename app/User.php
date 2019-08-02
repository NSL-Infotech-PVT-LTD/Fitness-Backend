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
        'firstname', 'lastname', 'email', 'password', 'phone', 'experience', 'hourly_rate', 'latitude', 'longitude', 'category_id', 'profile_pic', 'portfolio_image', 'bio'
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
     * Get the user's portfolio image.
     *
     * @param  string  $value
     * @return string
     */
    public function getPortfolioImageAttribute($value) {
        return json_decode($value);
    }

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute() {
        return $this->firstname . ' ' . $this->lastname;
    }

    /**
     * Get the user's Category Id.
     *
     * @param  string  $value
     * @return string
     */
    public function getCategoryIdAttribute($value) {
        try {
            $category = Category::where('id', $value)->get();
            if ($category->isEmpty() !== true)
                return $category->first()->name;
            else
                return $value;
        } catch (Exception $ex) {
            return $value;
        }
    }

}
