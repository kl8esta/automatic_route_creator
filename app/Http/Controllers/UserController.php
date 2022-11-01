<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use APP\User;
use App\RoutePost;

class UserController extends Controller
{
    public function index(User $user)
    {
        return view('users/index');
    }
    
    public function show_private_list(User $user)
    {
        return view('posts/private_list')->with(['own_posts' => $user->getOwnPaginateByLimit()]);
    }
}
