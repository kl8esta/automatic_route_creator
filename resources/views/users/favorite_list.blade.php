<!DOCTYPE html>
@extends('layouts.app')

@section('content')
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <script src="https://kit.fontawesome.com/{{ env('FONT_AWESOME_API_KEY') }}.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="{{ asset('css/route_post_list.css') }}">
    </head>
    <body>
        <h1>いいねしたルート</h1>
        <h5>{{Auth::user()->name}}</h5>
        <p class='create'>
            <a href='/posts/public_list'>→ 公開されているルートを見る</a>
        </p>
        <p class='create'>
            <a href='/posts/create_route'>ルートの新規作成</a>
        </p>
        <div class='fav_posts'>
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
                            <div class="route_maps" id="route_map_{{ $loop->index }}"></div>
                            <input type="hidden" id="route_input_{{ $loop->index }}" value="{{ json_encode($route_post->route_json) }}">
                            <!--p id="route_input_{{ $loop->index }}" style="visibility:hidden;">{ $route_post->route_json }}</p-->
                            <div class="post_comment">
                                "{{ $route_post->comment }}"
                            </div>                    
                        </div>                      
                        <div class="updated_at">
                            最終更新【{{ date('Y/m/d', strtotime($route_post->updated_at)) }}】
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        <div class='paginate'>{{ $fav_posts->links() }}</div>
        <div class="home_back">
            <p><a href="/">マイページに戻る</a></p>
        </div>
    </body>
    <script src="{{ asset('js/ajaxfav.js') }}" defer></script>
    <script>
        // Googleマップ上の処理を常時行うための関数
        function initMap() {
            for (let i = 0; i < {{ count($fav_posts) }}; i++)
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