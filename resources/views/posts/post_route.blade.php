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
            <div style="display: flex;">
                <div id="route_map" style="width: 500px; height: 350px; margin: 10px 5px 5px 10px; padding: 5px;"></div>
                <div id="route_panel" style="width: 500px; height: 350px; margin: 10px 10px 10px 5px; padding: 5px; overflow: scroll;"></div>
            </div>
            <div class="json_data" type="text">
                <p id="route_array" style="visibility:hidden;">{{ $input_route }}</p>
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
        <script>
        const route_array = document.getElementById('route_array');
        const original_data = JSON.parse(route_array.textContent);
        //console.log(original_data);
        function initMap() {
            const route_map = new google.maps.Map(
                document.getElementById("route_map"),
                {
                    center: { lat: 35.6810, lng: 139.7673 },
                    zoom: 13,
                    mapTypeId: "roadmap",
                }
            );
            // DirectionsService生成
            var directionsService = new google.maps.DirectionsService();
            // DirectionｓRenderer生成
            var directionsRenderer = new google.maps.DirectionsRenderer();
            // const waypoint_marker = new google.maps.MarkerOptions();
            directionsRenderer.setPanel(document.getElementById('route_panel'));
            /*directionsRenderer.setOptions({
                suppressMarkers: false,
                suppressPolylines: true,
                suppressInfoWindows: false,
                draggable: true,
                preserveViewport: false,
                markerOptions: {
                    title: 'title'
                },
            });*/
            directionsRenderer.setMap(route_map);
            // ルート検索実行
            directionsService.route(original_data, function(response, status) {
                    console.log(response);
                    if (status === google.maps.DirectionsStatus.OK) {
                    directionsRenderer.setDirections(response);
                    var legs = response.routes[0].legs;
                    
                    // 総距離と総時間の合計する
                    var dis = 0;
                    var sec = 0;
                    $.each(legs, function(i, val) {
                        sec += val.duration.value;
                        dis += val.distance.value;
                    });
                    console.log("distance=" + dis + ", secound=" + sec);
                    } else {
                    alert('Directions 失敗(' + status + ')');
                    }
            });	 
        }
        window.initMap = initMap;
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $gapi }}&callback=initMap&libraries=geometry" defer>
    </script>
</html>
@endsection