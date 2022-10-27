<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoutePost extends Model
{
    public function users()
    {
        return $this->hasMany('App\User');
    }
}
