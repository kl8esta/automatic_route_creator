<!DOCTYPE html>
@extends('layouts.app')

@section('content')
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <body style="padding: 0 10px;">
        <h1>マイルート一覧</h1>
        <h5>{{Auth::user()->name}}</h5>
        <p class='create' style="display:inline-block; padding: 10px; margin-bottom: 10px; border: 1px solid #333333; border-radius: 10px;">
            <a href='/posts/public_list'>→ 公開されているルートを見る</a>
        </p>
        <p class='create' style="display:inline-block; padding: 10px; margin-bottom: 10px; border: 1px solid #333333; border-radius: 10px;">
            <a href='/posts/create_route'>ルートの新規作成</a>
        </p>
        <div class='own_posts'>
            @foreach ($own_posts as $route_post)
                <div class='route_post'>
                    @if ($route_post->status_flag == 0)
                        <a href="/posts/private_list/{{ $route_post->id}}">
                            <h4>(非公開)タイトル【{{ $route_post->title}}】</h4>
                        </a>
                    @else
                        <a href="/posts/private_list/{{ $route_post->id}}">
                            <h4>(公開済み)タイトル【{{ $route_post->title}}】</h4>
                        </a>
                    @endif
                    <p class='spot_name'>観光ルート【a → b → c】</p>
                    <p class="updated_at">最終更新【{{ $route_post->updated_at }}】</p>
                </div>
            @endforeach
        </div>
        <div class='paginate'>{{ $own_posts->links() }}</div>
        <div class="home_back" style="margin: 30px 0 0 0; font-size: 15px;">
            <p><a href="/">マイページに戻る</a></p>
        </div>
    </body>
</html>
@endsection