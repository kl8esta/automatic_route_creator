<!DOCTYPE HTML>
@extends('layouts.app')

@section('content')
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    </head>
    <body style="padding: 0 20px;">
        <h1>ルートガイド</h1>
        <input id="pac-input" class="controls" type="text" placeholder="Search Box" 
        style="margin: 10px; width: 300px; border-radius: 2px; font-size: 15px;"/>
        <div style="display: flex;">
            <div class="view_left" id="map" style="width: 700px; height: 500px;"></div>
            <!--button id="btn" style="from-green-400 to-blue-500">CLICK</button><-->
            <div class="view_middle" style="margin: 0 10px">
                <h4>観光地リスト</h4>
                <h4>(最大8ヵ所追加できます)</h4>
                <!--button id="delete_btn" style="from-green-400 to-blue-500">削除</button><-->
                <!--button id="route_direction" style="from-green-400 to-blue-500">観光ルートを表示する</button-->
                <!--button id="list_value" type="button" style="from-green-400 to-blue-500">試験用</button-->
                <form action="/pre_posts" method="POST">
                    @csrf
                    <ul id="disp_list">
                    </ul>
                    <div class="json">
                        <input type="hidden" name="list_json" id="list_to_route"/>
                        <input type="hidden" name="list_name" id="list_to_name"/>
                        <p class="title__error" style="color:red">{{ $errors->first('differ') }}</p>
                    </div>
                    <button type="submit" id="direct_route">ルートを作成</button>
                </form>
                <!--p><a href="/posts/post_route">APIができるまではこのページをクリック</a></p-->
                <div class="back" style="margin: 10px 0 0 0; font-size: 15px;">
                    <a href="/">マイページに戻る</a>
                </div>
            </div>
            <div class="view_right" style="margin: 0 10px">
                <h4>使い方</h4>
                <ul>
                    <li>マップの「検索ボックス」で観光地を検索します</li>
                    <li>行きたい場所を「観光地リスト」に追加します</li>
                    <li>「ルートを作成」で観光ルートが表示されます</li>
                </ul>
                <h4>補足</h4>
                <ul>
                    <li>観光ルートは自動的に所要時間が最短のルートになります</li>
                    <li>観光地リストに追加できるのは8ヵ所までです</li>
                    <li>初めに出発地を入れれば、最適な巡る順番がおすすめされます</li>
                </ul>
            </div>
        </div>
    </body>
    <!-- ↓Googleマップに関するJavaScript記述 -->
    <script>
        //// 観光地リストに関する処理
        // 「目的地に追加」で追加した"観光地名"を格納する配列
        let place_list = [];
        
        // 「目的地に追加」で追加した"観光地の座標"を格納する配列
        let point_list = [];

        //const list_value = document.getElementById('list_value');
        
        // "ルートを作成" ボタンの実装 + クリックイベントの実装
        const direct_route = document.getElementById('direct_route')
        direct_route.addEventListener('click', () => {
            // 経由地の座標(緯度・経度)を格納
            var wayPoints = new Array();
            for (let i = 1; i < point_list.length-1; i++) {
                wayPoints.push({location: point_list[i]});
            }
            
            // Direction API へのリクエスト作成
            // →観光地リストの経由地情報とその他設定を書いたリクエスト
            // →画面遷移したときにそのレスポンスがマップで表示される
            request = {
                origin: point_list[0],  // 出発地
                destination: point_list[point_list.length-1],  // 到着地
                avoidHighways: true, // 高速道路を使わないオプション
                travelMode: google.maps.TravelMode.DRIVING, // 車モード
                optimizeWaypoints: true, // 最適化オプション
                waypoints: wayPoints // 経由地
            }
            
            // フォームタグ内にリクエストを埋め込む
            // Direction APIリクエストをJSON化
            let json = JSON.stringify(request);
            const list_to_route = document.getElementById('list_to_route');
            list_to_route.value = json;
            
            // 観光地リスト(地名タイトル)もJSON化して埋め込む
            let spot_names = JSON.stringify(place_list);
            const list_to_name = document.getElementById('list_to_name');
            list_to_name.value = spot_names;
            //list_to_name.value = place_list;
            //-console.log(json);
            //-list_to_route.value = request;
            //-console.log(request);
        });
        
        //// Googleマップ上の処理を常時行うための関数
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
            
            // 現在の追加済み観光地数
            let num_places = 0;
            
            // マーカー用の変数
            let markers = [];
            let add_btn;
            
            // 観光地リストの要素(ulタグ)の取得
            const disp_list = document.getElementById('disp_list');
            
            //// 地名を検索したときの処理
            // →入力キーワードが変わると，検索結果も変化する
            searchBox.addListener("places_changed", () => {
                // 検索結果の複数の場所を格納
                const places = searchBox.getPlaces();
                
                // 未入力検索ではなにもしない
                if (places.length == 0) {
                    return;
                }
        
                // 表示されていたマーカーの消去
                markers.forEach((marker) => {
                    marker.setMap(null);
                });
                markers = [];
                
                const bounds = new google.maps.LatLngBounds();
                
                //// 検索でヒットした観光地をマーカーで表示させるための処理
                // 検索された各観光地にマーカーを付与していく
                places.forEach((place) => {
                    //console.log(place);
                    if (!place.geometry || !place.geometry.location) {
                        console.log("Returned place contains no geometry");
                        return;
                    }
                    
                    //// 各マーカーに付与する場所の情報(場所名, 平均評価, 総レビュー数，... 
                    //// ...場所の写真, 住所, その他未実装)
                    // マーカーの生成 + 観光地情報の追加
                    const marker = new google.maps.Marker({
                        map,
                        title: place.name, // 観光地名
                        stars: place.rating, // 平均レビュー
                        total_reviews: place.user_ratings_total, // レビュー数
                        picture: place.photos[0].getUrl(), // ユーザー投稿写真
                        address: place.formatted_address, // 住所
                        attributions: place.html_attributions[0],
                        url: place.url,
                        position: place.geometry.location,
                        optimized: false,
                    });
                    
                    // 各マーカーを配列要素として格納
                    markers.push(marker);
                    
                    if (place.geometry.viewport) {
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });
                
                // マーカークリック時のウィンドウ表示を実装する変数
                const infoWindow = new google.maps.InfoWindow();
                
                // 過去のウィンドウ情報を保存する変数
                let preInfoWindow = infoWindow;
                
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
                    // domready時に前クリックしたマーカーウィンドウのボタン要素が残り続けることへの対策
                    //let click_counts = 1;
                    //let count_list =  Array(markers.length);
                    //let out_clicks = 0;
                    //let in_clicks = Array(markers.length);
                    
                //// 各マーカークリック時のイベント処理
                markers.forEach((marker, i) => {
                    //count_list[i] = 0;
                    //in_clicks[i] = 0; 
                    marker.addListener("click", () => {
                        //// マーカーウィンドウの表示
                        // マーカーのクリックで表示されるウィンドウのHTML文
                        place_info[i] =
                            '<div id="window_contents" style="width: 260px; height: 200px;">' +
                            '<h3 id="firstHeading" class="firstHeading">'+
                            marker.getTitle() + 
                            "</h3>" + 
                            '<div id ="info" style="display: flex;">' +
                            '<div style="margin: 5px 0 0 5px">' +
                            "<img src=" + 
                            marker.picture +
                            ' width="100" height="100">' +
                            '<p style="margin: 5px 0 0 0">評価：' + 
                            marker.stars +
                            " /5<br>" + 
                            marker.total_reviews +  
                            "人のレビュー" + 
                            "</p></div>" +
                            '<div style="margin: 5px 0 0 5px;">'+
                            '<button id ="add_btn' + 
                            String(i) +
                            '" type="button" style="padding: 5px; margin: 5px">観光地リストに追加</button><p>'+
                            marker.address +
                            "</p></div></div>";  
                            //console.log(marker);
                            //out_clicks += 1;
                            //in_clicks[i] = out_clicks;
                            //console.log("外" + String(click_counts) + "+内" + String(i) + "番目" + String(count_list[i]));
                        
                        // クリック直後に以前のマーカーウィンドウを閉じる
                            //infoWindow.close();
                        closeOtherMarkerWindow();
                        
                        // マーカーウィンドウをHTMLで開く
                        infoWindow.setContent(place_info[i]);
                        infoWindow.open(marker.getMap(), marker);
                        
                            //if (in_clicks[i] == out_clicks)
                            //{
                            //infoWindow.addListener('domready', () => {
                        
                        //// マーカーウィンドウ内のイベント処理
                        // ウィンドウ内のタグ要素をアクセス可能にする
                        google.maps.event.addListenerOnce(infoWindow,'domready', function() {
                            // ウィンドウ内の「目的地に追加」ボタンが押されたときの処理
                            add_btns[i] = document.getElementById('add_btn'+String(i));
                                //add_btn = document.getElementById('add_btn');
                            add_btns[i].addEventListener('click', () => {
                                // 観光地リストに入る観光地は8個まで(API有料なら23個まで)
                                if (place_list.length < 8) {
                                        //count_list[i] = 1;
                                        //console.log(String(i) + "番目" + String(count_list[i]));
                                        //if (count_list[i] == click_counts)
                                    
                                    // 同じ観光地が追加されないための判定
                                    if (place_list.indexOf(marker.getTitle())==-1) {
                                        // 観光地名を観光地リスト追加
                                        place_list.push(marker.getTitle()); 
                                        // 観光地座標
                                        point_list.push(marker.getPosition());
                                    }
                                    
                                    // 一度pタグをすべて削除
                                    while (disp_list.firstChild) {
                                        disp_list.removeChild(disp_list.firstChild);
                                    }
                                    // pタグに観光地リストの要素を記述して列挙
                                    for (let i = 0; i < place_list.length; i++) {
                                        // pタグの生成(id, テキスト)
                                        const pTag = document.createElement('p');
                                            //todoList.id = 'place'+ String(disp_list.childElementCount);
                                        pTag.id = 'place'+ String(i);
                                            //pTag.name = "place_names[]";
                                            //todoList.textContent = marker.getTitle();
                                        pTag.textContent = place_list[i];
                                        
                                        // pタグ内に各観光地の削除ボタン(id, type, テキスト)を実装
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
                　               // 9個以上追加できないようにする
                　               else {
                　                   window.confirm('目的地は(出発地を含めて)8個までです')
                　               }
                　               
                　               // 追加した観光地をリストから削除するボタンの有効化
                                enableDelButtons();           　               
                            }) 
                            //place_list = [];
                        });
                        
                        // 現在のウィンドウ情報を保存
                        preInfoWindow = infoWindow;
                        //count_list[i] = 0;                                    
                        //console.log("外" + String(click_counts) + "+内" + String(i) + "番目" + String(count_list[i]));
                    });
                });                    
                
                map.fitBounds(bounds);
            });
            
            //// 観光地を観光地リストから削除する関数
            function enableDelButtons() {
                // 各観光地のpタグidを格納する配列
                let place_ids = [];
                
                // 各観光地の削除ボタンタグを格納する配列
                let delete_btns = [];
                
                // 観光地リストの各要素に対する処理
                for (let i = 0; i < disp_list.childElementCount; i++) {
                    // 各観光地のpタグidを取得 → 配列に格納
                    place_ids.push(document.getElementById('place'+ String(i)));
                    
                    //// リストから対象要素を削除詰めするクリックイベントの追加
                    // 各観光地の削除ボタン取得
                    // クリックして
                    del_listen = document.getElementById('btn'+ String(i));
                    del_listen.addEventListener('click', function(){
                        // 要素の削除
                        disp_list.removeChild(place_ids[i]);
                        
                        // 元の観光地名と座標が入った配列の中身も削除・整理
                        delete place_list[i];
                        place_list = place_list.filter(Boolean);
                        
                        delete point_list[i];
                        point_list = place_list.filter(Boolean);
                    })
                    
                    // 削除ボタンを配列に格納
                    delete_btns.push(del_listen);
                }
                /*for(let i = 0; i < disp_list.childElementCount; i++){
                    delete_btns[i].addEventListener('click', function(){
                        disp_list.removeChild(place_ids[i]);
                        delete place_list[i];
                        place_list = place_list.filter(Boolean);
                    })
                }*/
            }
        }
        window.initAutocmplete = initAutocomplete;
        
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $gapi }}&callback=initAutocomplete&libraries=places,geometry" defer>
    </script>
</html>
@endsection