<?php

namespace App\Http\Controllers;

use App\RoutePost;
use Illuminate\Http\Request;
use App\Http\Requests\RoutePostRequest;
use Illuminate\Support\Facades\DB;

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
        $input += ['user_id' => $request->user()->id];
        $input += ['route_json' => '{ "name": "Tanaka" }'];
        $route_post->fill($input)->save();
        if ($route_post->status_flag == 0)
        {
            return redirect('/posts/private_list/' . $route_post->id);
        }
        else 
        {
            return redirect('/posts/public_list/' . $route_post->id);
        }
        //dd($request->all());
    }
    
    public function public_list_one(RoutePost $route_post)
    {
        //$posted_user = User::where('name','=',$route_post['user_id'])->first();
        $posted_user = DB::table('users')->where('id',$route_post['user_id'])->value('name');
        //dd($posted_user);
        return view('posts/public_list_one')->with(['route_post' => $route_post, 'posted_user' => $posted_user]);
    }
    
        public function private_list_one(RoutePost $route_post)
    {
        //$posted_user = User::where('name','=',$route_post['user_id'])->first();
        $posted_user = DB::table('users')->where('id',$route_post['user_id'])->value('name');
        //dd($posted_user);
        return view('posts/private_list_one')->with(['route_post' => $route_post, 'posted_user' => $posted_user]);
    }
}
