<!DOCTYPE HTML>
@extends('layouts.app')

@section('content')
    <h1>マイページ</h1>
    <h5 class="name">
        {{ $user->name }}
    </h5>
    <div class="content">
        <div class="posts_list">
            <div>
                <p>最近投稿したルート(3つ)</p>
                <a href="/posts/myroute/public">公開済みマイルート一覧</a>
            </div>
            <div>
                <p>最近いいねしたルート(3つ)</p>
                <a href="/posts/favorite/">いいねルート一覧</a>
            </div>
            <div>
                <p>最近作成したルート(3つ)</p>
                <a href="/posts/myroute/private">マイルート一覧</a>
            </div>
        </div>
        <div class="route_creation">
            <h3>ルートガイド</h3>
            <div>
                <a href="/routes/create">自分で観光地を決める</a>
            </div>
            <div>
                <a href="/posts/list">みんなのルートを見る</a>
            </div>
        </div>
    </div>
@endsection