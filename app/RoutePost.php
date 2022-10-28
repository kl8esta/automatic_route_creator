<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoutePost extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'route_json',
        'status_flag',
        'information',
        'comment',
    ];
    
    public function users()
    {
        return $this->hasMany('App\User');
    }
}
