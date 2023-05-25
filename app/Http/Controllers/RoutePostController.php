<?php

namespace App\Http\Controllers;

use App\RoutePost;
use App\Favorite;
use Illuminate\Http\Request;
use App\Http\Requests\RoutePostRequest;
use Illuminate\Support\Facades\DB;
use Auth;

class RoutePostController extends Controller
{
    # ルート作成ページの表示
    public function create_route()
    {   
        $gapi = env('GOOGLE_MAPS_API_KEY'); 
        return view('posts/create_route')->with(['gapi' => $gapi]);
    }
    
    # ルート投稿ページの表示
    public function post_route(Request $request)
    {
        $gapi = env('GOOGLE_MAPS_API_KEY'); 
        // バリデーションエラーなどでリロードされるとsave_route()で送った
        // セッションデータがなくなるので再生成
        if (!($request->session()->has('gapi'))) {
            //dd($request->session());
            session(['gapi' => $gapi]);
        }
        return view('posts/post_route');
    }
    
    # ルート作成リクエスト
    public function save_route(RoutePost $route_post, Request $request)
    {
        $input_route = $request['list_json'];
        $input_route = json_decode($request['list_json'], true);
        //$place_names = json_decode($request['list_name'], true);
        $place_names = $request['list_name'];
        $gapi = env('GOOGLE_MAPS_API_KEY'); 
        return redirect('/posts/post_route')->with(['place_names' => $place_names, 'input_route' => $input_route, 'gapi' => $gapi]);
        /*return redirect()->route('posts.post_route')->with(['place_names' => $place_names, 'input_route' => $input_route, 
                    'gapi' => $gapi
                ]);*/
    }
    
    # ルート投稿(DB保存)
    public function save_form(RoutePost $route_post, RoutePostRequest $request)
    {
        $gapi = env('GOOGLE_MAPS_API_KEY'); 
        $input = $request['route_post'];
        $input += ['user_id' => $request->user()->id];
        //$input += ['route_json' => '{ "name": "Tanaka" }'];
        $route_post->fill($input)->save();
        if ($route_post->status_flag == 0)
        {
            return redirect('/posts/private_list/' . $route_post->id);
        }
        else 
        {
            return redirect('/posts/public_list/' . $route_post->id);
        }
    }
    
    # 公開ルート詳細ページの表示
    public function public_list_one(RoutePost $route_post)
    {
        //$posted_user = User::where('name','=',$route_post['user_id'])->first();
        $posted_user = DB::table('users')->where('id',$route_post['user_id'])->value('name');
        //dd($posted_user);
        return view('posts/public_list_one')->with(['route_post' => $route_post, 'posted_user' => $posted_user]);
    }
    
    # マイルート詳細ページの表示
    public function private_list_one(RoutePost $route_post)
    {
        # ユーザが他ユーザのマイルートページに入らないようにする
        if (Auth::id()!=$route_post['user_id']) {
            return redirect('/');
        }
        //$posted_user = User::where('name','=',$route_post['user_id'])->first();
        $posted_user = DB::table('users')->where('id',$route_post['user_id'])->value('name');
        return view('posts/private_list_one')->with(['route_post' => $route_post, 'posted_user' => $posted_user]);
    }
    
    # 公開ルート一覧ページの表示
    public function show_public_list(RoutePost $route_post, Favorite $favorite)
    {
        //dd($route_post->getPaginateByLimit());
        $send = [
            'route_posts' => $route_post->getPaginateByLimit(),
            'favorite' => $favorite,
            ];
        
        return view('posts/public_list')->with($send);        
    }
    
    # ルート投稿の編集
    public function editPost(RoutePost $route_post)
    {
        return view('posts/edit_post')->with(['route_post' => $route_post]);
    }
    
    # ルート投稿更新(DB更新)
    public function updatePost(RoutePost $route_post, RoutePostRequest $request)
    {
        $input = $request['route_post'];
        $input += ['user_id' => $request->user()->id];
        $input += ['route_json' => '{ "spot": "Tokyo" }'];
        $route_post->fill($input)->save();
        if ($route_post->status_flag == 0)
        {
            return redirect('/posts/private_list/' . $route_post->id);
        }
        else 
        {
            return redirect('/posts/public_list/' . $route_post->id);
        }
    }
    
    # ルート投稿削除(DB除外)
    public function delete(RoutePost $route_post)
    {
        $route_post->delete();
        return redirect('/posts/private_list/');
    }

}

