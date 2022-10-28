<?php

namespace App\Http\Controllers;

use App\RoutePost;
use Illuminate\Http\Request;
use App\Http\Requests\RoutePostRequest;

class RoutePostController extends Controller
{
    public function create_route()
    {
        return view('posts/create_route');
    }
    
    public function post_route()
    {
        return view('posts/post_route');
    }
    
        public function save_form(RoutePost $route_post, RoutePostRequest $request)
    {
        $input = $request['route_post'];
        //9-3補足(Request.phpインスタンスのuser()→requestを送信したユーザの情報)
        $input += ['user_id' => $request->user()->id];
        $post->fill($input)->save();
        return redirect('/posts/list' . $post->id);
        //dd($request->all());
    }
}
