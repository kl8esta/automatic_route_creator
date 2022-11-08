<!DOCTYPE HTML>
@extends('layouts.app')

@section('content')
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    </head>
    <body>
        <h1>ルートガイド</h1>
        <form action="/post_route" method="POST">
            @csrf
            <div class="search">
                <h4>キーワード検索</h4>
                <input type="text" name="post[title]" style="width:30%" placeholder="行きたい観光地名を入力して下さい" value="{{ old('post.title') }}"/>
                <div>
                <a href="/posts/myroute/private">検索</a>
                <a href="/posts/myroute/private">観光地リストに追加</a>
                </div>
            </div>
            <div id="route-condition" class="form-group">
                <div>
                    <div style="float:right">
                        <button id="route-button" class="btn btn-primary" type="button">検索</button>
                        <button id="clear-button" class="btn btn-default" type="button">クリア</button>
                    </div>
                    <div style="clear:both"></div>
                </div>                    
                <br>
                <span>出発地</span>
                <br>
                <input type="text" id="start" value="" placeholder="観光地1" class="form-control">
                <span>経由地1</span>
                <br>
                <input type="text" id="way1" value="" placeholder="観光地2" class="form-control">
                <span>経由地2</span>
                <br>
                <input type="text" id="way2" value="" placeholder="観光地3" class="form-control">
                <span>到着地</span>
                <br>
                <input type="text" id="end" value="" placeholder="観光地4" class="form-control">
                <div id="route-result" style="height: 52px;">
                <div id="route-panel"></div>
                </div>
            </div>
            <br>
            <div class="spots_list">
                <h4>観光地リスト</h4>
            </div>
            <input type="submit" value="ルート投稿画面へ進む{最適なルートが計算されます}"/>
            <p><a href="/posts/post_route">APIができるまではこのページをクリック</a></p>
        </form>
        <div class="back">
            <a href="/">マイページに戻る</a>
        </div>
        <input id="pac-input" class="controls" type="text" placeholder="Search Box"/>
        <div id="map" style="width: 700px; height: 500px;">
            
        </div>
    </body>
    <!-- ↓Googleマップに関するJavaScript記述 -->
    <script>
        function initAutocomplete() {
            // マップの生成
            // 参考「https://developers.google.com/maps/documentation/javascript/examples/places-searchbox」
            const map = new google.maps.Map(
                document.getElementById("map"),
                {
                    center: { lat: 35.6810, lng: 139.7673 },
                    zoom: 13,
                    mapTypeId: "roadmap",
                }
            );
        
            // 検索BOXの作成
            const input = document.getElementById("pac-input");
            const searchBox = new google.maps.places.SearchBox(input);
         
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
        
            // 検索範囲を今見えている範囲に限定
            map.addListener("bounds_changed", () => {
                searchBox.setBounds(map.getBounds());
            });
            
            // マーカー用の変数
            let markers = [];
            
            //キーワードを変えたときの検索結果を変化させる処理
            searchBox.addListener("places_changed", () => {
                // 検索結果の複数の場所を格納
                const places = searchBox.getPlaces();
        
                if (places.length == 0) {
                    return;
                }
        
                // 表示されていたマーカの消去
                markers.forEach((marker) => {
                    marker.setMap(null);
                });
                markers = [];
        
                // **検索結果の場所ごとにマーカを付与**
                const bounds = new google.maps.LatLngBounds();
                // 場所ごとの情報をウィンドウ表示するための変数
                const infoWindow = new google.maps.InfoWindow();        
                // 各場所に対する処理
                places.forEach((place) => {
                    if (!place.geometry || !place.geometry.location) {
                        console.log("Returned place contains no geometry");
                        return;
                    }
        
                    /*const icon = {
                        url: place.icon,
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(25, 25),
                    };*/
        
                    /*markers.push(
                        new google.maps.Marker({
                            map,
                            title: place.name,
                            position: place.geometry.location,
                        })
                    );*/
                
                    // 各マーカーに付与する場所の情報(場所名, 平均評価, 総レビュー数, 
                    // 場所の写真, 住所, その他未実装)
                    const marker = new google.maps.Marker({
                        map,
                        title: place.name,
                        stars: place.rating,
                        total_reviews: place.user_ratings_total,
                        picture: place.photos[0].getUrl(),
                        address: place.formatted_address,
                        attributions: place.html_attributions[0],
                        url: place.url,
                        position: place.geometry.location,
                    });
              
                    /*const infoWindow = new google.maps.InfoWindow({
                        content: marker.getTitle() + "\r\n 【平均評価:" + String(marker.stars) + "/5.0】",
                        ariaLabel: marker.getTitle(),
                    });*/
              
                    const place_info =
                        '<div id="content" style="width: 200px; height: 200px;">' +
                        '<h3 id="firstHeading" class="firstHeading">'+
                        marker.getTitle() + 
                        "</h3>" + 
                        '<div id ="info" display: flex;>' +
                        "<img src=" + 
                        marker.picture +
                        ' width="100" height="100">' +
                        '<p>評価：' + 
                        marker.stars +
                        " /5<br>" + 
                        marker.total_reviews +  
                        "人のレビュー<br>" + 
                        marker.address +
                        "</p></div>" +
                        "</div>";
              
                    // 1つのマーカをクリックしたときに場所の情報をウィンドウ表示する処理
                    marker.addListener("click", () => {
                        infoWindow.close();
                        /*infoWindow.setContent(marker.getTitle() + "\r\n 【平均評価：" + String(marker.stars) + "/5.0】" +
                        "評価人数"  + marker.picture);*/
                        infoWindow.setContent(place_info);
                        infoWindow.open(marker.getMap(), marker);
                    });
                
                    // 各マーカを1つに格納
                    markers.push(marker);
        
                    if (place.geometry.viewport) {
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });
                map.fitBounds(bounds);
            });
        }
        window.initAutocmplete = initAutocomplete;
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBX-T2KgCdV5nEYyUZmcFLflMmW76c7gHs&callback=initAutocomplete&libraries=places" defer>
    </script>
</html>
@endsection