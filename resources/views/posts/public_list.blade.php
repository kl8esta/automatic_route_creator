<!DOCTYPE html>
@extends('layouts.app')

@section('content')
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <script src="https://kit.fontawesome.com/{{ env('FONT_AWESOME_API_KEY') }}.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="{{ asset('css/route_post_list.css') }}">
    </head>
    <body>
        <h1>公開済みルート一覧</h1>
        <h3>{{Auth::user()->name}}</h3>
        <p class='create'>
            <a href='/posts/private_list'>→ マイルート一覧へ</a>
        </p>
        <p class='create'>
            <a href='/posts/create_route'>ルートの新規作成</a>
        </p>
        <div class='route_posts'>
            @foreach ($route_posts as $route_post)
                @if($route_post->status_flag == 1)
                    <div class='route_post'>
                        <a href="/posts/public_list/{{ $route_post->id}}">
                            <h3>タイトル【{{ $route_post->title}}】</h3>
                        </a>
                        <h4 class='user_name'>
                            投稿者【{{ $route_post->user->name }}】
                        </h4>
                        <div class='spot_name'>
                            巡る場所：{{$route_post->tour_list}}
                        </div>
                        <div class='duration'>
                            {{$route_post->duration}}
                        </div>
                        <div class="post_comment">
                        "{{ $route_post->comment }}"
                        </div>    
                        <div class="updated_at">
                            最終更新【{{ date('Y/m/d', strtotime($route_post->updated_at)) }}】
                        </div>
                    </div>
                    @if($favorite->favIsNull($route_post->id,Auth::user()->id))
                    <span class="favorite-icon">
                        <i class="fas fa-heart fav-toggle" data-rtpost-id="{{ $route_post->id }}"></i>
                        <span class="favoritesCount">{{$route_post->favorites_count}}</span>
                    </span>
                    @else
                    <span class="favorite-icon">
                        <i class="fas fa-heart fav-toggle faved" data-rtpost-id="{{ $route_post->id }}"></i>
                        <span class="favoritesCount">{{$route_post->favorites_count}}</span>
                    </span>
                    @endif
                @endif
            @endforeach
        </div>
        <div class='paginate'>{{ $route_posts->links() }}</div>
        <div class="home_back">
            <p><a href="/">マイページに戻る</a></p>
        </div>
    </body>
    <script src="{{ asset('js/ajaxfav.js') }}" defer></script>
</html>
@endsection