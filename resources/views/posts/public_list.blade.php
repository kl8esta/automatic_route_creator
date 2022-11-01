<!DOCTYPE html>
@extends('layouts.app')

@section('content')
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <body>
        <h1>公開済みルート一覧</h1>
        <h5>{{Auth::user()->name}}</h5>
        <p class='create'>
            <a href='/posts/create_route'>ルートの新規作成</a>
        </p>
        <div class='route_posts'>
            @foreach ($route_posts as $route_post)
                <div class='route_post'>
                    <a href="/posts/public_list/{{ $route_post->id}}">
                        <h4>タイトル【{{ $route_post->title}}】</h4>
                    </a>
                    <small>投稿者【{{ $route_post->user->name }}】</small>
                    <p class='spot_name'>観光ルート【a → b → c】</p>
                    <p class="updated_at">最終更新【{{ $route_post->updated_at }}】</p>
                </div>
            @endforeach
        </div>
        <div class='paginate'>{{ $route_posts->links() }}</div>
    </body>
</html>
@endsection