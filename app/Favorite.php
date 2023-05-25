<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

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
    
    // いいねされているか(いいねした情報がfavoritesテーブルにあるか)確認
    public function favIsNull($route_id, $auth_id): bool
    {
        return $this::where('route_post_id', $route_id)->where('user_id', $auth_id)->first() == null;
    }

    // いいねしたルート一覧の取得
    /*public function getFavPaginateByLimit(int $limit_count = 3)
    {
        /*return $this::with(['routePosts' => function ($query) {
            $query->orderBy('updated_at', 'desc');
        }])->where('user_id', Auth::id())->paginate($limit_count);*/
        //return $this::with('routePosts')->where('user_id', Auth::id())->orderBy('updated_at', 'DESC')->paginate($limit_count);
        //return $this::with('routePosts')->where('user_id', Auth::id())->orderBy('updated_at', 'DESC')->paginate($limit_count);
        //return $this::->join('route_posts', 'favorites.route_post_id', '=', 'route_posts.id')->orderBy('route_posts.updated_at', 'DESC')->paginate($limit_count);
        //return $this::with('routePosts')->find(Auth::id())->routePosts()->withCount('favorites')->orderBy('updated_at', 'DESC')->paginate($limit_count);
    //}*/
    
    // リレーション
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
    public function routePosts()
    {
        return $this->belongsTo('App\RoutePost');
    }
}
