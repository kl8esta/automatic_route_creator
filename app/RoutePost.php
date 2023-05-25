<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class RoutePost extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'route_json',
        'status_flag',
        'information',
        'comment',
        'tour_list',
        'duration'
    ];
    //'route_order','route_time',
    protected $casts = [
        'route_json' => 'json',
    ];
    
    // 公開済みルートの一覧といいね数の取得
    public function getPaginateByLimit(int $limit_count = 5)
    {
        //return $this->orderBy('updated_at', 'DESC')->paginate($limit_count);
        return $this::with('user')->withCount('favorites')->where('status_flag', 1)->orderBy('updated_at', 'DESC')->paginate($limit_count);
    }
    
    // いいねしたルートの一覧取得
    public function getOnlyFavRoute(int $get_limit = 5)
    {
        return $this::whereHas('favorites',function($q){
            $q->where('user_id', Auth::id());})->where('user_id', '!=', Auth::id())->where('status_flag', 1)->orderBy('updated_at', 'DESC')->paginate($get_limit);
        //dd($this::with('favorites')->where('user_id', Auth::id())->orderBy('updated_at', 'DESC')->paginate($get_limit));
    }
    
    // リレーション
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function favorites()
    {
        return $this->hasMany('App\Favorite');
    }
}
