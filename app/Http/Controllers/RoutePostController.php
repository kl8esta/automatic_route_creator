<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoutePostController extends Controller
{
    public function create()
    {
        return view('posts/create');
    }
}
