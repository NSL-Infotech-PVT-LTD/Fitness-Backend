<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
     protected $fillable = ['title','body','data','user_id','is_read'];
}
