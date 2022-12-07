<!DOCTYPE HTML>
@extends('layouts.app')

@section('content')
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    </head>
    <body style="padding: 20px">
        <h1>ルートガイド</h1>
        <input id="pac-input" class="controls" type="text" placeholder="Search Box"/>
        <div style="display: flex;">
            <div class="view_left" id="map" style="width: 700px; height: 500px;"></div>
            <!--button id="btn" style="from-green-400 to-blue-500">CLICK</button><-->
            <div class="view_right" style="padding: 10px">
                <h4>追加した観光地(回る順番は自動で決まります)</h4>
                <h6>最大8か所追加できます(最初に出発地を入力してください)</h6>
                <ul id="disp_list">
                </ul>
                <!--button id="delete_btn" style="from-green-400 to-blue-500">削除</button><-->
                <!--button id="route_direction" style="from-green-400 to-blue-500">観光ルートを表示する</button-->
                <!--button id="list_value" type="button" style="from-green-400 to-blue-500">試験用</button-->
                <form action="/pre_posts" method="POST">
                    @csrf
                    <div class="json">
                        <input type="hidden" name="list_json" id="list_to_route"/>
                        <p class="title__error" style="color:red">{{ $errors->first('differ') }}</p>
                    </div>
                    <button type="submit" id="direct_route">ルートを作成</button>
                </form>
                <!--p><a href="/posts/post_route">APIができるまではこのページをクリック</a></p-->
                <div class="back">
                    <a href="/">マイページに戻る</a>
                </div>
            </div>
        </div>
    </body>
    <!-- ↓Googleマップに関するJavaScript記述 -->
    <script>
        // 行きたいところ(観光地リスト)に追加した観光地名の格納用
        let place_list = [];
        let point_list = [];
        //const list_value = document.getElementById('list_value');
        const direct_route = document.getElementById('direct_route')
        direct_route.addEventListener('click', () => {
            var wayPoints = new Array();
            for (let i = 1; i < point_list.length-1; i++)
            {
                wayPoints.push({location: point_list[i]});
            }
            request = {
                origin: point_list[0],  // 出発地
                destination: point_list[point_list.length-1],  // 到着地
                avoidHighways: true, // 高速は利用しない
                travelMode: google.maps.TravelMode.DRIVING, // 車モード
                optimizeWaypoints: true, // 最適化を有効
                waypoints: wayPoints // 経由地
            }
            let json = JSON.stringify(request);
            const list_to_route = document.getElementById('list_to_route');
            list_to_route.value = json;
            //console.log(json);
            ////list_to_route.value = request;
            ////console.log(request);
        });
        
        /*function jsonSet()
        {
            console.log("on");
            request = {
                origin: place_list[0],  // 出発地
                destination: place_list[place_list.length-1],  // 到着地
                avoidHighways: true, // 高速は利用しない
                travelMode: google.maps.TravelMode.DRIVING, // 車モード
                optimizeWaypoints: true, // 最適化を有効
                waypoints: wayPoints // 経由地
            }
            
            const list_to_route = document.getElementById('list_to_route');
            list_to_route.value = request;
        }*/
        
        /*const route_btn = document.getElementById('route_direction');
        const route_map = new google.maps.Map(
            document.getElementById("route_map"),
            {
                center: { lat: 35.6810, lng: 139.7673 },
                zoom: 13,
                mapTypeId: "roadmap",
            }
        );*/
        //route_btn.addEventListener('click', () => {
            /*var wayPoints = new Array();
            //place_list.forEach((spot) => {
            //    wayPoints.push({location: spot});
            //});
            for (let i = 1; i < place_list.length-1; i++)
            {
                wayPoints.push({location: place_list[i]});
            }
            // DirectionsService生成
            var directionsService = new google.maps.DirectionsService();
            
            // DirectionｓRenderer生成
            var directionsRenderer = new google.maps.DirectionsRenderer();
            directionsRenderer.setPanel(document.getElementById('route_panel'));
            directionsRenderer.setMap(route_map);
            // ルート検索実行
            directionsService.route({
                origin: place_list[0],  // 出発地
                destination: place_list[place_list.length-1],  // 到着地
                avoidHighways: true, // 高速は利用しない
                travelMode: google.maps.TravelMode.DRIVING, // 車モード
                optimizeWaypoints: true, // 最適化を有効
                waypoints: wayPoints // 経由地
                }, function(response, status) {
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
        });*/
        
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
            
            //現在の追加済み観光地数
            let num_places = 0;
            // マーカー用の変数
            let markers = [];
            let add_btn;
            
            // 観光地{pタグ}リストの表示場所
            const disp_list = document.getElementById('disp_list');
            
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
                let preInfoWindow = infoWindow;
                
                // 各場所に対する処理
                places.forEach((place) => {
                    //console.log(place);
                    if (!place.geometry || !place.geometry.location) {
                        console.log("Returned place contains no geometry");
                        return;
                    }
                    
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
                        optimized: false,
                    });
                
                    // 各マーカを1つに格納
                    markers.push(marker);
        
                    if (place.geometry.viewport) {
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });
                
                // マーカーウィンドウ内のボタン用
                let add_btns = Array(markers.length);
                // マーカーウィンドウの情報格納用
                let place_info = Array(markers.length);
                //let place_info = "";
                
                // 前のマーカーで開いていたウィンドウの削除関数
                function closeOtherMarkerWindow() {
                    if (preInfoWindow) {
                        preInfoWindow.close();
                    }
                }
                
                //domready時に前クリックしたマーカーウィンドウのボタン要素が残り続けることへの対策
                //let click_counts = 1;
                //let count_list =  Array(markers.length);
                //let out_clicks = 0;
                //let in_clicks = Array(markers.length);
                
                //各マーカークリック時の処理
                markers.forEach((marker, i) => {
                    //count_list[i] = 0;
                    //in_clicks[i] = 0; 
                    marker.addListener("click", () => {
                        //マーカをクリックした際に表示するウィンドウのHTML記述
                        place_info[i] =
                            '<div id="window_contents" style="width: 250px; height: 200px;">' +
                            '<h3 id="firstHeading" class="firstHeading">'+
                            marker.getTitle() + 
                            "</h3>" + 
                            '<div id ="info" style="display: flex;">' +
                            '<div style="margin: 5px">' +
                            "<img src=" + 
                            marker.picture +
                            ' width="100" height="100">' +
                            '<p>評価：' + 
                            marker.stars +
                            " /5　　" + 
                            marker.total_reviews +  
                            "人のレビュー" + 
                            "</p></div>" +
                            '<div style="margin: 5px;">'+
                            '<button id ="add_btn' + 
                            String(i) +
                            '" type="button" style="padding: 5px; margin: 5px">目的地に追加</button><p>'+
                            marker.address +
                            "</p></div></div>";  
                        //console.log(marker);
                        //out_clicks += 1;
                        //in_clicks[i] = out_clicks;
                        //console.log("外" + String(click_counts) + "+内" + String(i) + "番目" + String(count_list[i]));
                        
                        // クリック直後に以前のマーカーウィンドウを閉じる
                        //infoWindow.close();
                        closeOtherMarkerWindow();
                        // ウィンドウ用のHTMLをセットして開く
                        infoWindow.setContent(place_info[i]);
                        infoWindow.open(marker.getMap(), marker);
                        
                        //if (in_clicks[i] == out_clicks)
                        //{
                        //infoWindow.addListener('domready', () => {
                        // ウィンドウ内のタグ要素をアクセス可能にする
                        google.maps.event.addListenerOnce(infoWindow,'domready', function() {
                            //ウィンドウ内の観光地追加ボタンが押されたときの処理
                            add_btns[i] = document.getElementById('add_btn'+String(i));
                            //add_btn = document.getElementById('add_btn');
                            add_btns[i].addEventListener('click', () => {
                                if(place_list.length < 8)
                                {
                                    //count_list[i] = 1;
                                    //console.log(String(i) + "番目" + String(count_list[i]));
                                    //if (count_list[i] == click_counts)
                                    
                                    // 同じ観光地を追加しないための判定
                                    if (place_list.indexOf(marker.getTitle())==-1)
                                    {
                                        // 観光地リストに追加
                                        place_list.push(marker.getTitle()); 
                                        point_list.push(marker.getPosition());
                                    }
                                    
                                    // 一度pタグをすべて削除
                                    while(disp_list.firstChild){
                                        disp_list.removeChild(disp_list.firstChild);
                                    }
                                    // pタグに観光地リストの要素を記述して列挙
                                    for (let i = 0; i < place_list.length; i++) 
                                    {
                                        // pタグの生成
                                        const pTag = document.createElement('p');
                                        // pタグの情報
                                        //todoList.id = 'place'+ String(disp_list.childElementCount);
                                        pTag.id = 'place'+ String(i);
                                        //todoList.textContent = marker.getTitle();
                                        pTag.textContent = place_list[i];
                                        // pタグ内に各観光地の削除ボタンを実装
                                        const btn = document.createElement('button')
                                        btn.id = 'btn' + String(i);
                                        btn.type = "button";
                                        btn.textContent = "削除";
                                        // 各pタグの子要素に追加
                                        pTag.appendChild(btn);
                                        // 各pタグを親要素に格納
                                        disp_list.appendChild(pTag);
                                    }
                　                   //count_list[i] = 0;
                　                   }
                　               // 8個以上追加できないようにする
                　               else {
                　                   window.confirm('目的地は(出発地を含めて)8個までです')
                　               }
                　               // 追加した観光地をリストから削除するボタンの有効化
                                enableDelButtons();           　               
                            }) 
                            //place_list = [];
                        });
                        
                        preInfoWindow = infoWindow;
                        //count_list[i] = 0;                                    
                        //console.log("外" + String(click_counts) + "+内" + String(i) + "番目" + String(count_list[i]));
                    });
                });                    
                
                map.fitBounds(bounds);
            });
            
            // 観光地をリストから削除する関数
            function enableDelButtons() {
                // 各観光地のpタグidと削除ボタンを取得
                let place_ids = [];
                let delete_btns = [];
                
                for(let i = 0; i < disp_list.childElementCount; i++)
                {
                    //pタグと削除ボタンタグを取得
                    place_ids.push(document.getElementById('place'+ String(i)));
                    del_listen = document.getElementById('btn'+ String(i));
                    // リストから対象要素を削除詰めするクリックイベントの追加
                    del_listen.addEventListener('click', function(){
                        disp_list.removeChild(place_ids[i]);
                        delete place_list[i];
                        place_list = place_list.filter(Boolean);
                    })
                    delete_btns.push(del_listen);
                }
                //for(let i = 0; i < disp_list.childElementCount; i++)
                //{
                //    delete_btns[i].addEventListener('click', function(){
                //        disp_list.removeChild(place_ids[i]);
                //        delete place_list[i];
                //        place_list = place_list.filter(Boolean);
                //    })
                //}
            }
        }
        window.initAutocmplete = initAutocomplete;
        
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $gapi }}&callback=initAutocomplete&libraries=places,geometry" defer>
    </script>
</html>
@endsection