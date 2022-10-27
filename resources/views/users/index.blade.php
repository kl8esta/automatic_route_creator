<!DOCTYPE HTML>
@extends('layouts.app')

@section('content')
    <h1>マイページ</h1>
    <h4 class="name">ユーザー【{{Auth::user()->name}}】</h4><br>
    <div class="content">
        <div class="posts_list">
            <h3>ルート閲覧</h3>
            <div>
                <h6>最近投稿したルート(3つ)</h6>
                <p><a href="/posts/myroute/public">公開済みマイルート一覧</a></p>
            </div>
            <div>
                <h6>最近いいねしたルート(3つ)</h6>
                <p><a href="/posts/favorite/">いいねルート一覧</a></p>
            </div>
            <div>
                <h6>最近作成したルート(3つ)</h6>
                <p><a href="/posts/myroute/private">マイルート一覧</a></p>
            </div>
        </div>
        <div class="route_creation">
            <h3>ルートガイド</h3>
            <div>
                <h5><a href="/routes/create">自分で観光地を決める</a></h5>
            </div>
            <div>
                <h5><a href="/posts/list">みんなのルートを見る</a></h5>
            </div>
        </div>
    </div>
@endsection