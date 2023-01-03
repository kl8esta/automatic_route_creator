<!DOCTYPE HTML>
@extends('layouts.app')

@section('content')
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
    </head>
    <body style="padding: 0 10px;">
        <h1 "margin: 10px">ルート投稿</h1>
        <form action="/posts" method="POST" onsubmit="return preValidation()">
            @csrf
            <div class="title">
                <h4 style="margin: 10px">タイトル(必須)</h4>
                <input type="text" name="route_post[title]" id="in_title" placeholder="このルートのタイトルは？" value="{{ old('route_post.title') }}"/>
                <p class="title__error" style="color:red">{{ $errors->first('route_post.title') }}</p>
            </div>
            <div class="visit" style="width: 800px">
                <!--input type="hidden" name="route_post[title]" value="{ $place_names }}"/-->
                <h4 type="text" id="visit_info"></h4>
                <h4 type="text" id="visit_time"></h4>
            </div>
            <div style="display: flex;">
                <div id="route_map" style="width: 500px; height: 350px; margin: 10px 5px 5px 10px; padding: 5px;"></div>
                <div id="route_panel" style="width: 500px; height: 350px; margin: 10px 10px 10px 5px; padding: 5px; overflow: scroll;"></div>
            </div>
            <div class="json_data" type="text">
                <!--p id="route_array" style="visibility:hidden;"> session('input_route') </p-->
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
            //console.log json_decode session'input_route'), JSON_UNESCAPED_UNICODE) }});
            //const visit_order_json = json$place_names);
            //const visit_order = "{$place_names}}";
        //// 観光ルートの地名の表示
        // 観光地リストの地名取得
        const visit_order_json = @json(session('place_names'));
        const visit_order = JSON.parse(visit_order_json);
        
        // 観光順番のアルファベット生成
        let alphabets = []
        for (let i = 'A'.charCodeAt(0); i <= 'Z'.charCodeAt(0); i++) 
        {
            alphabets.push(String.fromCharCode([i]))
        }
        
        // 観光ルートの順番テキストを生成
        let visit_numbers = "";
        for (let i = 0; i < visit_order.length; i++) 
        {
            if (i == visit_order.length-1) {
                visit_numbers += "[" + alphabets[i] + "]：" + visit_order[i];
            } else {
                visit_numbers += "[" + alphabets[i] + "]：" + visit_order[i] + "→ ";
            } 
        }
        
        // 観光ルートをbladeに表示
        const visit_info = document.getElementById('visit_info');
        visit_info.textContent = visit_numbers;
        
        //// 観光ルートのマップ表示
        // 前ページからの Direction API リクエストの取得
        const route_array = document.getElementById('route_array');
            //const original_data = JSON.parse(route_array.textContent);
            //const original_data = @json(session()->get('input_route'));
            //console.log//jsonkakko//input_route));
            //const encode_data = @json(session('input_route'));
            //const original_data = JSON.parse(encode_data);
        const original_data = @json(session('input_route'));
        
        // Googleマップ上の処理を常時行うための関数
        function initMap() {
            // 初回の表示マップの設定オプション
            const route_map = new google.maps.Map(
                document.getElementById("route_map"),
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
            // const waypoint_marker = new google.maps.MarkerOptions();
            
            // ルートナビの起動
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
                
            // マップの起動
            directionsRenderer.setMap(route_map);
            
            // 観光ルートの計算
            directionsService.route(original_data, function(response, status) {
                // Direction APIレスポンスの状態確認
                if (status === google.maps.DirectionsStatus.OK) {
                    // マップに観光ルートを描画する
                    directionsRenderer.setDirections(response);
                    console.log(response);
                    //// 観光ルートの移動距離、所要時間の計算
                    // 観光地から観光地の距離・時間の情報を取得
                    let route_res = response.routes[0].legs;
                    //console.log(response.routes[0]);
                    //console.log(legs);
                    
                    // 移動距離時間の合計
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
                    
                    // 総移動距離と総所要時間をbladeに表示
                    const visit_time = document.getElementById('visit_time');
                    visit_time.textContent = km_str +" / " + time_str ;
                } else {
                    alert('観光ルートを表示できませんでした。前ページからもう一度お願いします。(' + status + ')');
                    }
            });	 
        }
        window.initMap = initMap;
        
        // タイトルが入力されているかの確認
        function preValidation() {
            in_title = document.getElementById('in_title');
            if (in_title.value === "") {
                alert('タイトルを入力して下さい');
                return false;
            }
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ session('gapi') }}&callback=initMap&libraries=geometry" defer>
    </script>
</html>
@endsection