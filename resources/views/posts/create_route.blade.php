<!DOCTYPE HTML>
@extends('layouts.app')

@section('content')
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h1>ルートガイド</h1>
        <form action="/post_route" method="POST">
            @csrf
            <div class="search">
                <h4>キーワード検索</h4>
                <input type="text" name="post[title]" style="width:30%" placeholder="行きたい観光地名を入力して下さい" value="{{ old('post.title') }}"/>
                <div>
                <a href="/posts/myroute/private">検索</a>
                <a href="/posts/myroute/private">観光地リストに追加</a>
                </div>
            </div>
            <br>
            <div class="spots_list">
                <h4>観光地リスト</h4>
            </div>
            <input type="submit" value="ルート投稿画面へ進む{最適なルートが計算されます}"/>
            <p><a href="/posts/post_route">APIができるまではこのページをクリック</a></p>
        </form>
        <div class="back">
            <a href="/">マイページに戻る</a>
        </div>
    </body>
</html>
@endsection