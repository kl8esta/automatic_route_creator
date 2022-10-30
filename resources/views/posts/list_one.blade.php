<!DOCTYPE HTML>
@extends('layouts.app')

@section('content')
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <link rel="stylesheet" href="/css/app.css">
    </head>
    <body>
        <p><h1 class="title">{{ $route_post->title }}</h1></p>
        <p><h5 class="publisher">ユーザー【{{ $posted_user }}】</h5></p>
        <p class='edit'>
            <a href="/posts/{{ $route_post->id }}/edit">edit</a>
        </p>
        <div class="content">
            <div class="post_information">
                <h5>観光ルート【a → b → c】</h5>
            </div>
            <div class="post_information">
                <h5>補足情報</h5>
                <p>{{ $route_post->information }}</p>
                <p class="updated_at">{{ $route_post->updated_at }}</p>
            </div>
            <div class="post_comment">
                <h5>感想</h5>
                <p>{{ $route_post->comment }}</p>
            </div>
            <div class="footer">
                <a href="/list">公開済みルート一覧に戻る</a>
                <a href="/">再編集する</a></a>
            </div>
        </div>
    </body>
</html>
@endsection