<!DOCTYPE HTML>
@extends('layouts.app')

@section('content')
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Fonts -->
        <title></title>
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <link rel="stylesheet" href="/css/app.css">
    </head>
    <body style="padding: 0 10px;">
        <p>
            <p><h1 class="header">マイルート</h1></p>
            <p><h4 class="title" style="margin: 10px;">タイトル【{{ $route_post->title }}】</h4></p>
        </p>
        <div style="margin: 10px;">
            <h5 class="publisher">ユーザー【{{ $posted_user }}】</h5>
            <h5 class="updated_at">最終更新【{{ date('Y/m/d', strtotime($route_post->updated_at)) }}】</h5>
        </div>
        <div class="content">
            <div class="spot_names" style="margin: 20px 0 10px 10px;">
                <h5 style="display:inline-block; padding: 10px; margin-bottom: 10px; border: 1px solid #333333; border-radius: 10px;">
                    巡る場所：{{$route_post->tour_list}}
                </h5>
                <h5>{{$route_post->duration}}</h5>
            </div>
            <div class="post_information" style="margin: 20px 0 10px 10px;">
                <h5>補足情報</h5>
                <p>{{ $route_post->information }}</p>
            </div>
            <div class="post_comment" style="margin: 20px 0 10px 10px;">
                <h5>感想</h5>
                <p>{{ $route_post->comment }}</p>
            </div>
            <div style="display:inline-block; padding: 10px; margin-bottom: 10px; border: 5px double #333333; border-radius: 10px;">
                <a href="/posts/private_list">マイルート一覧に戻る</a>
            </div>
            <div style="display:inline-block; padding: 10px; margin-bottom: 10px; border: 5px double #333333; border-radius: 10px;">
               <a href="/posts/{{ $route_post->id }}/edit">再編集する</a>
            </div>
            <form action="/posts/{{ $route_post->id }}" id="form_{{ $route_post->id }}" method="post" style="margin: 50px 0">
                @csrf
                @method('DELETE')
                <button type="button" onclick="deletePost({{ $route_post->id }})">この投稿を削除する</button>
            </form>
        </div>
    </body>
    <script>
        function deletePost(id) {
            'use strict'
    
            if (confirm('この投稿を削除します。本当によろしいですか？\n(公開している場合は他の人が見られなくなります)')) 
            {
                document.getElementById(`form_${id}`).submit();
            }
        }
    </script>
</html>
@endsection