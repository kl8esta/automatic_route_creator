<!DOCTYPE html>
@extends('layouts.app')

@section('content')
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <body style="padding: 0 10px;">
        <h1>公開済みルート一覧</h1>
        <h5>{{Auth::user()->name}}</h5>
        <p class='create' style="display:inline-block; padding: 10px; margin-bottom: 10px; border: 1px solid #333333; border-radius: 10px;">
            <a href='/posts/private_list'>→ マイルート一覧へ</a>
        </p>
        <p class='create' style="display:inline-block; padding: 10px; margin-bottom: 10px; border: 1px solid #333333; border-radius: 10px;">
            <a href='/posts/create_route'>ルートの新規作成</a>
        </p>
        <div class='route_posts'>
            @foreach ($route_posts as $route_post)
                @if($route_post->status_flag == 1)
                    <div class='route_post'>
                        <a href="/posts/public_list/{{ $route_post->id}}">
                            <h4>タイトル【{{ $route_post->title}}】</h4>
                        </a>
                        <small>投稿者【{{ $route_post->user->name }}】</small>
                        <p class='spot_name'>観光ルート【a → b → c】</p>
                        <p class="updated_at">最終更新【{{ $route_post->updated_at }}】</p>
                    </div>
                @endif
            @endforeach
        </div>
        <div class='paginate'>{{ $route_posts->links() }}</div>
        <div class="home_back" style="margin: 30px 0 0 0; font-size: 15px;">
            <p><a href="/">マイページに戻る</a></p>
        </div>
    </body>
</html>
@endsection