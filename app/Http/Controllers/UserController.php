<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use APP\User;
use App\RoutePost;
use App\Favorite;
use Auth;

class UserController extends Controller
{
    // マイページの表示
    public function index(RoutePost $route_post, User $user, Favorite $favorite)
    {
        //$fav_posts = RoutePost::with('favorites')->where('favorites.user_id', Auth::id())->orderBy('updated_at', 'DESC')->paginate(3);
        //'fav_posts' => $favorite->getFavPaginateBylimit(),
        //dd($favorite->getFavPaginateBylimit());
        $attach = [
            'route_posts' => $route_post->getPaginateByLimit(3),
            'own_posts' => $user->getOwnPaginateByLimit(3),
            'fav_posts'=> $route_post->getOnlyFavRoute(3),
            'favorite' => $favorite,
            ];
        return view('users/index')->with($attach);
    }
    
    // マイルート一覧ページの表示
    public function show_private_list(User $user, Favorite $favorite)
    {
        $send = [
            'own_posts' => $user->getOwnPaginateByLimit(),
            'favorite' => $favorite,
            ];
        return view('posts/private_list')->with($send);
    }
    
    // いいねルート一覧ページの表示
    public function show_favorite_list(RoutePost $route_post, Favorite $favorite)
    {
        $push = [
            'fav_posts' => $route_post->getOnlyFavRoute(),
            'favorite' => $favorite,
            ];
        return view('users/favorite_list')->with($push);
    }
}
