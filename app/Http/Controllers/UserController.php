<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use APP\User;
use App\RoutePost;
use App\Favorite;

class UserController extends Controller
{
    public function index(User $user)
    {
        return view('users/index');
    }
    
    public function show_private_list(User $user, Favorite $favorite)
    {
        $send = [
            'own_posts' => $user->getOwnPaginateByLimit(),
            'favorite' => $favorite,
            ];
        return view('posts/private_list')->with($send);
    }
}
