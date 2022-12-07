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
        'comment'
    ];
    
    protected $casts = [
        'route_json' => 'json',
    ];
    
    public function getPaginateByLimit(int $limit_count = 5)
    {
        //return $this->orderBy('updated_at', 'DESC')->paginate($limit_count);
        return $this::with('user')->orderBy('updated_at', 'DESC')->paginate($limit_count);
    }
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
