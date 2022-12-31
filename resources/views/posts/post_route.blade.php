<!DOCTYPE HTML>
@extends('layouts.app')

@section('content')
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
    </head>
    <body style="padding: 0 10px;">
        <h1 "margin: 10px">ルート投稿</h1>
        <form action="/posts" method="POST">
            @csrf
            <div class="title">
                <h4 style="margin: 10px">タイトル(必須)</h4>
                <input type="text" name="route_post[title]" placeholder="このルートのタイトルは？" value="{{ old('route_post.title') }}"/>
                <p class="title__error" style="color:red">{{ $errors->first('route_post.title') }}</p>
            </div>
            <div class="visit" style="width: 800px">
                <!--input type="hidden" name="route_post[title]" value="{{ $place_names }}"/-->
                <h4 type="text" id="visit_info" name="route_post[route_order]"></h4>
                <h4 type="text" id="visit_time" name="route_post[route_time]"></h4>
            </div>
            <div style="display: flex;">
                <div id="route_map" style="width: 500px; height: 350px; margin: 10px 5px 5px 10px; padding: 5px;"></div>
                <div id="route_panel" style="width: 500px; height: 350px; margin: 10px 10px 10px 5px; padding: 5px; overflow: scroll;"></div>
            </div>
            <div class="json_data" type="text">
                <p id="route_array" style="visibility:hidden;">{{ $input_route }}</p>
                <!--p type="hidden" id="route_array"></p-->
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
            <button type="submit" name="route_post[status_flag]" value="0">マイルートに保存【非公開】</button>
            <button type="submit" name="route_post[status_flag]" value="1">公開する</button>
        </form>
        <div class="back" style="margin: 10px 0 0 0; font-size: 15px;">
            <p><a href="/posts/create_route">観光地の編集・追加</a></p>
        </div>

    </body>
        <script>
        const visit_order_json = @json($place_names);
        //const visit_order = "{{$place_names}}";
        const visit_order = JSON.parse(visit_order_json);
        //console.log(visit_order);
        //console.log(visit_order[0]);
        let alphabets = []
        for (let i = 'A'.charCodeAt(0); i <= 'Z'.charCodeAt(0); i++) 
        {
            alphabets.push(String.fromCharCode([i]))
        }
        let visit_numbers = "";
        for (let i = 0; i < visit_order.length; i++) 
        {
            if (i == visit_order.length-1) {
                visit_numbers += "[" + alphabets[i] + "]：" + visit_order[i];
            } else {
                visit_numbers += "[" + alphabets[i] + "]：" + visit_order[i] + "→ ";
            } 
        }
        const visit_info = document.getElementById('visit_info');
        visit_info.textContent = visit_numbers;
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
                //console.log(response);
                if (status === google.maps.DirectionsStatus.OK) {
                    directionsRenderer.setDirections(response);
                    
                    // 移動距離、所要時間の計算
                    let route_res = response.routes[0].legs;
                    //console.log(response.routes[0]);
                    //console.log(legs);
                    let dist_meter = 0;
                    let time_sec = 0;
                    route_res.forEach((res) => {
                        //console.log(res);
                        dist_meter += res.distance.value;
                        time_sec += res.duration.value;
                    });
                    // メートル => キロメートルに変換
                    let dist_kmeter = Math.round(dist_meter / 100) / 10;
                    let km_str = "移動距離：約" + String(dist_kmeter) + "km";
                    // 秒 => 日/時間/分に変換
                    let time_str = "";
                    let time_day = Math.floor(time_sec / 86400);
                    let time_hour = Math.floor(time_sec % 86400 / 3600);
                    let time_min = Math.floor(time_sec % 3600 / 60);
                    if (time_sec < 3600) {
                        time_str = "所要時間：約" + String(time_min) + "分";
                    } else if (time_sec >= 86400) {
                        time_str = "所要時間：約" + String(time_day) + "日"
                        + String(time_hour) + "時間" + String(time_min) + "分";
                    } else {
                        time_str = "所要時間：約" + String(time_hour) + "時間" 
                        + String(time_min) + "分";
                    }
                    //console.log("総移動距離=" + dist_kmeter + ", 総所要時間=" + time_hour);
                    const visit_time = document.getElementById('visit_time');
                    visit_time.textContent = km_str +" / " + time_str ;
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