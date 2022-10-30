<!DOCTYPE HTML>
@extends('layouts.app')

@section('content')
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h1>ルート投稿</h1>
        <form action="/posts" method="POST">
            @csrf
            <div class="title">
                <h4>タイトル(必須)</h4>
                <input type="text" name="route_post[title]" placeholder="このルートのタイトルは？" value="{{ old('route_post.title') }}"/>
                <p class="title__error" style="color:red">{{ $errors->first('route_post.title') }}</p>
            </div>
            <div class="route_map">
                <p><h4>計算されたルートが表示されます</h4></p>
            </div>
            <div class="information">
                <h4>補足情報(任意)</h4>
                <textarea name="route_post[information]" placeholder="みんなに教えたい情報を共有しよう">
                    {{ old('route_post.information') }}
                </textarea>
                <p class="information__error" style="color:red">
                    {{ $errors->first('route_post.information') }}
                </p>
            </div>
            <div class="comment">
                <h4>感想(任意)</h4>
                <textarea name="route_post[comment]" placeholder="このルートを観光してみた感想は？">
                    {{ old('route_post.comment') }}
                </textarea>
                <p class="comment__error" style="color:red">
                    {{ $errors->first('route_post.comment') }}
                </p>
            </div>
            <div class="post_option">
            </div>
            <button type="submit" name="route_post[status_flag]" value="0">非公開する</button>
            <button type="submit" name="route_post[status_flag]" value="1">公開する</button>
        </form>
        <div class="back">
            <p><a href="/posts/create_route">観光地の編集・追加</a></p>
        </div>

    </body>
</html>
@endsection