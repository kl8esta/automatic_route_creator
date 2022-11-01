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
        <p>
            <p><h1 class="header">公開済みルート</h1></p>
            <p><h4 class="title">タイトル【{{ $route_post->title }}】</h4></p>
        <p>
            <h5 class="publisher">ユーザー【{{ $posted_user }}】</h5>
            <h5 class="updated_at">最終更新【{{ $route_post->updated_at }}】</h5>
        </p>
        <div class="content">
            <div class="post_information">
                <h5>観光ルート【a → b → c】</h5>
            </div>
            <div class="post_information">
                <h5>補足情報</h5>
                <p>{{ $route_post->information }}</p>
            </div>
            <div class="post_comment">
                <h5>感想</h5>
                <p>{{ $route_post->comment }}</p>
            </div>
            <div class='edit' style="display:inline-block; padding: 10px; margin-bottom: 10px; border: 5px double #333333; border-radius: 10px;">
                <a href="/posts/public_list">公開済みルート一覧に戻る</a>
            </div>
            <div class='edit' style="display:inline-block; padding: 10px; margin-bottom: 10px; border: 5px double #333333; border-radius: 10px;">
               <a href="/posts/{{ $route_post->id }}/edit">再編集する</a>
            </div>
        </div>
    </body>
</html>
@endsection