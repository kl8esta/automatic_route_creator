<!DOCTYPE HTML>
@extends('layouts.app')

@section('content')
    <h1 style="padding: 10px">マイページ</h1>
    <h4 class="name" style="padding: 10px">ユーザー【{{Auth::user()->name}}】</h4><br>
    <div class="content" style="padding: 20px">
        <div class="posts_list">
            <h3>ルート閲覧</h3>
            <div>
                <h6>最近投稿したルート(3つ)</h6>
                <p><a href="/posts/public_list">公開済みマイルート一覧</a></p>
            </div>
            <div>
                <h6>最近いいねしたルート(3つ)</h6>
                <p><a href="/posts/favorite/">いいねルート一覧</a></p>
            </div>
            <div>
                <h6>最近作成したルート(3つ)</h6>
                <p><a href="/posts/private_list">マイルート一覧</a></p>
            </div>
        </div>
        <div class="route_creation">
            <h3>ルートガイド</h3>
            <div>
                <h5><a href="/posts/create_route">自分で観光地を決める</a></h5>
            </div>
            <div>
                <h5><a href="/posts/public_list">みんなのルートを見る</a></h5>
            </div>
        </div>
    </div>
@endsection