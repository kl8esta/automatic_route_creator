<!DOCTYPE HTML>
@extends('layouts.app')

@section('content')
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <link rel="stylesheet" href="{{ asset('css/route_post_list.css') }}">
    </head>
    <body>
        <div class='pre_content'>
            <h1>マイページ</h1>
            <h4>ユーザー【{{Auth::user()->name}}】</h4><br>
            <p class="route_creation"><a href="/posts/create_route">観光ルートを検索・作成する</a></p>    
        </div>
        <div class="content">
            <div class="posts_list">
                <h1>ルート閲覧</h1>
                <div class="index_loop">
                    <h3>・最近投稿されたルート</h3>
                    <div class='route_posts' style="display: flex;">
                        @foreach ($route_posts as $route_post)
                            @if($route_post->status_flag == 1)
                                <div class='route_post'>
                                    <a href="/posts/public_list/{{ $route_post->id}}">
                                        <h3>タイトル【{{ $route_post->title}}】</h3>
                                    </a>
                                    <h4 class='user_name'>
                                        投稿者【{{ $route_post->user->name }}】
                                    </h4>
                                    <div class='spot_name'>
                                        巡る場所：{{$route_post->tour_list}}
                                    </div>
                                    <div class='duration'>
                                        {{$route_post->duration}}
                                    </div>
                                    <div style="display: flex;">
                                        <div class="route_maps" id="public_map_{{ $loop->index }}"></div>
                                        <input type="hidden" id="public_input_{{ $loop->index }}" value="{{ json_encode($route_post->route_json) }}">
                                        <!--p id="route_input_{{ $loop->index }}" style="visibility:hidden;">{ $route_post->route_json }}</p-->
                                        <div class="post_comment">
                                            "{{ $route_post->comment }}"
                                        </div>                    
                                    </div>    
                                </div>
                            @endif
                        @endforeach
                    </div>                
                    <p class='view_list'><a href="/posts/public_list">公開されているルートを見る</a></p>
                </div>
                <div class="index_loop">
                    <h3>・最近作成したルート</h3>
                    <div class='own_posts' style="display: flex;">
                        @foreach ($own_posts as $route_post)
                            <div class='route_post'>
                                @if ($route_post->status_flag == 0)
                                    <a href="/posts/private_list/{{ $route_post->id}}">
                                        <h3>(非公開)タイトル【{{ $route_post->title}}】</h3>
                                    </a>
                                @else
                                    <a href="/posts/private_list/{{ $route_post->id}}">
                                        <h3>(公開済み)タイトル【{{ $route_post->title}}】</h3>
                                    </a>
                                @endif
                                <div class='spot_name'>
                                    巡る場所：{{$route_post->tour_list}}
                                </div>
                                <div class='duration'>
                                    {{$route_post->duration}}
                                </div>
                                <div style="display: flex;">
                                    <div class="route_maps" id="private_map_{{ $loop->index }}"></div>
                                    <input type="hidden" id="private_input_{{ $loop->index }}" value="{{ json_encode($route_post->route_json) }}">
                                    <!--p id="route_input_{{ $loop->index }}" style="visibility:hidden;">{ $route_post->route_json }}</p-->
                                    <div class="post_comment">
                                        "{{ $route_post->comment }}"
                                    </div>                    
                                </div>                      
                            </div>           
                        @endforeach
                    </div>
                    <p class='view_list'><a href="/posts/private_list">作成したルートを全て見る</a></p>
                </div>            
                <div class="index_loop">
                    <h3>・最近いいねしたルート</h3>
                    <div class='route_posts' style="display: flex;">
                        @foreach ($fav_posts as $route_post)
                            @if($route_post->status_flag == 1)
                                <div class='route_post'>
                                    <a href="/posts/public_list/{{ $route_post->id}}">
                                        <h3>タイトル【{{ $route_post->title}}】</h3>
                                    </a>
                                    <h4 class='user_name'>
                                        投稿者【{{ $route_post->user->name }}】
                                    </h4>
                                    <div class='spot_name'>
                                        巡る場所：{{$route_post->tour_list}}
                                    </div>
                                    <div class='duration'>
                                        {{$route_post->duration}}
                                    </div>
                                    <div style="display: flex;">
                                        <div class="route_maps" id="favorite_map_{{ $loop->index }}"></div>
                                        <input type="hidden" id="favorite_input_{{ $loop->index }}" value="{{ json_encode($route_post->route_json) }}">
                                        <!--p id="route_input_{{ $loop->index }}" style="visibility:hidden;">{ $route_post->route_json }}</p-->
                                        <div class="post_comment">
                                            "{{ $route_post->comment }}"
                                        </div>                    
                                    </div>    
                                </div>
                            @endif
                        @endforeach
                    </div>             
                    <p class='view_list'><a href="/posts/favorite_list">いいねしたルートを全て見る</a></p>
                </div>
            </div>
        </div>
    </body>
    <script src="{{ asset('js/ajaxfav.js') }}" defer></script>
    <script>
        // Googleマップ上の処理を常時行うための関数
        function initMap() {
            //// 公開ルート一覧用
            for (let i = 0; i < {{ count($route_posts) }}; i++)
            {
                let public_input = document.getElementById('public_input_' + String(i));
                //let response = JSON.parse(@json($route_post->route_json)));
                let response_public = JSON.parse(JSON.parse(public_input.value));
                //console.log(response);
                //console.log(typeof(response));
                
                // 初回の表示マップの設定オプション
                let public_map = new google.maps.Map(
                    document.getElementById("public_map_" + String(i)),
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
                directionsRenderer.setMap(public_map);
                // マップに観光ルートを描画する
                directionsRenderer.setDirections(response_public);
            }
            
            //// マイルート一覧用
            for (let j = 0; j < {{ count($own_posts) }}; j++)
            {
                let private_input = document.getElementById('private_input_' + String(j));
                let response_private = JSON.parse(JSON.parse(private_input.value));
                
                // 初回の表示マップの設定オプション
                let private_map = new google.maps.Map(
                    document.getElementById("private_map_" + String(j)),
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
                directionsRenderer.setMap(private_map);
                // マップに観光ルートを描画する
                directionsRenderer.setDirections(response_private);
            }  
            
            //// いいねルート一覧用
            for (let k = 0; k < {{ count($fav_posts) }}; k++)
            {
                let favorite_input = document.getElementById('favorite_input_' + String(k));
                let response_favorite = JSON.parse(JSON.parse(favorite_input.value));
                
                // 初回の表示マップの設定オプション
                let favorite_map = new google.maps.Map(
                    document.getElementById("favorite_map_" + String(k)),
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
                directionsRenderer.setMap(favorite_map);
                // マップに観光ルートを描画する
                directionsRenderer.setDirections(response_favorite);
            }        
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap&libraries=places,geometry" defer>
    </script>
@endsection