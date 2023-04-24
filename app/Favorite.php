<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $fillable = [
        'route_post_id', 
        'user_id'
    ];
    
    public $timestamps = false;
    
    /*public function favIsPushed($auth_id, $route_id)
    {
        return $this::where('route_post_id', $route_id)->where('user_id', $auth_id)->first(); 
    }*/
    
    public function favIsNull($route_id, $auth_id): bool
    {
        return $this::where('route_post_id', $route_id)->where('user_id', $auth_id)->first() == null;
    }
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
    public function routePosts()
    {
        return $this->belongsTo('App\RoutePost');
    }
}
