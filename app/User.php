<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
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
    
    // マイルート一覧の取得
    public function getOwnPaginateByLimit(int $limit_count = 5)
    {
        return $this::with('routePosts')->find(Auth::id())->routePosts()->withCount('favorites')->orderBy('updated_at', 'DESC')->paginate($limit_count);
    }
    
    // リレーション
    public function routePosts()
    {
        return $this->hasMany('App\RoutePost');
    }
    public function favorites()
    {
        return $this->hasMany('App\Favorite');
    }
}
