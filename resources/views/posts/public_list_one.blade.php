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
    <body style="padding: 0 10px;">
        <p>
            <p><h1 class="header">公開済みルート</h1></p>
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
            <div style="display: flex;">
                <div id="route_map" style="width: 500px; height: 350px; margin: 10px 5px 5px 10px; padding: 5px;"></div>
                <div id="route_panel" style="width: 500px; height: 350px; margin: 10px 10px 10px 5px; padding: 5px; overflow: scroll;"></div>
            </div>
            <div class="post_information" style="margin: 20px 0 10px 10px;">
                <h5>補足情報</h5>
                <p>{{ $route_post->information }}</p>
            </div>
            <div class="post_comment" style="margin: 20px 0 10px 10px;">
                <h5>感想</h5>
                <p>{{ $route_post->comment }}</p>
            </div>
            <div class='edit' style="display:inline-block; padding: 10px; margin-bottom: 10px; border: 5px double #333333; border-radius: 10px;">
                <a href="/posts/public_list">公開済みルート一覧に戻る</a>
            </div>
            <div class='edit' style="display:inline-block; padding: 10px; margin-bottom: 10px; border: 5px double #333333; border-radius: 10px;">
                <a href="/posts/private_list">マイルート一覧に戻る</a>
            </div>
        </div>
    </body>
    <script>
        // Googleマップ上の処理を常時行うための関数
        function initMap() {
            for (let i = 0; i < {{ count($route_posts) }}; i++)
            {
                let route_input = document.getElementById('route_input_' + String(i));
                //let response = JSON.parse(@json($route_post->route_json)));
                let response = JSON.parse(JSON.parse(route_input.value));
                //console.log(response);
                //console.log(typeof(response));
                
                // 初回の表示マップの設定オプション
                let route_map = new google.maps.Map(
                    document.getElementById("route_map_" + String(i)),
                    {
                        center: { lat: 35.6810, lng: 139.7673 },
                        zoom: 13,
                        mapTypeId: "roadmap",
                    }
                );
                
                // Directions APIの起動
                let directionsService = new google.maps.DirectionsService();
                
                // マップ描画機能の起動
                let directionsRenderer = new google.maps.DirectionsRenderer();
                    
                // マップの起動
                directionsRenderer.setMap(route_map);
                // マップに観光ルートを描画する
                directionsRenderer.setDirections(response);
            }
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap&libraries=places,geometry" defer>
    </script>
</html>
@endsection