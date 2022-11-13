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
        <input id="pac-input" class="controls" type="text" placeholder="Search Box"/>
        <div id="map" style="width: 700px; height: 500px;"></div>
        <button id="btn" style="from-green-400 to-blue-500">CLICK</button>
        <h4>追加した観光地(回る順番は自動で決まります)</h4>
        <!--table>
            <tr id="disp_list" style="border-collapse: collapse; text-align: center;">
                <td>スコア</td><td>スコア</td>
            </tr>
        </table>
        <-->
        <h6>最大8か所追加できます(最初に出発地を入力してください)</h6>
        <ul id="disp_list">
        </ul>
        <button id="delete_btn" style="from-green-400 to-blue-500">削除</button>
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
            <br>
            <div id="route-condition" class="form-group">
                <div>
                    <div style="float:right">
                        <button id="route-button" class="btn btn-primary" type="button">検索</button>
                        <button id="clear-button" class="btn btn-default" type="button">クリア</button>
                    </div>
                    <div style="clear:both"></div>
                </div>                    
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
            
            // 行きたいところ(観光地リスト)に追加した観光地名の格納用
            let place_list = [];
            //現在の追加済み観光地数
            let num_places = 0;
            // マーカー用の変数
            let markers = [];
            let add_btn;
            
            // 観光地リストの表示場所
            disp_list = document.getElementById('disp_list');
            
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
                let lastOpenedInfoWindow = infoWindow;
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
              
                    /*const infoWindow = new google.maps.InfoWindow({
                        content: marker.getTitle() + "\r\n 【平均評価:" + String(marker.stars) + "/5.0】",
                        ariaLabel: marker.getTitle(),
                    });*/
                    
              
                    //onclick="addPlace()"
                    // 1つのマーカをクリックしたときに場所の情報をウィンドウ表示する処理
                
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
                let place_info = Array(markers.length);
                //let place_info = "";
                function closeLastOpenedInfoWindow() {
                    if (lastOpenedInfoWindow) {
                        lastOpenedInfoWindow.close();
                    }
                }
                
                let click_counts = 1;
                let count_list =  Array(markers.length);
                
                let out_clicks = 0;
                let in_clicks = Array(markers.length);
                
                
                markers.forEach((marker, i) => {
                    count_list[i] = 0;
                    in_clicks[i] = 0; 
                    //domready時に前クリックしたマーカーウィンドウのボタン要素が残り続けることへの対策
                    //addable_flag = 0;
                    marker.addListener("click", () => {
                    //マーカをクリックしたときの場所情報のウィンドウ表示
                        place_info[i] =
                            '<div id="window_contents" style="width: 200px; height: 200px;">' +
                            '<h3 id="firstHeading" class="firstHeading">'+
                            marker.getTitle() + 
                            "</h3>" + 
                            '<div id ="info" display: flex;>' +
                            "<img src=" + 
                            marker.picture +
                            ' width="100" height="100">' +
                            '<p>評価：' + 
                            marker.stars +
                            " /5　　" + 
                            marker.total_reviews +  
                            "人のレビュー" + 
                            "</p></div>" +
                            '<button id ="add_btn' + 
                            String(i) +
                            '" type="button">目的地に追加</button><p>'+
                            marker.address +
                            "</p></div>";                    
                            
                        //infoWindow.close();
                        console.log(marker);
                        out_clicks += 1;
                        in_clicks[i] = out_clicks;
                        
                        console.log("外" + String(click_counts) + "+内" + String(i) + "番目" + String(count_list[i]));
                        closeLastOpenedInfoWindow();
                        infoWindow.setContent(place_info[i]);
                        infoWindow.open(marker.getMap(), marker);
                        //if (in_clicks[i] == out_clicks)
                        //{
                        //infoWindow.addListener('domready', () => {
                        google.maps.event.addListenerOnce(infoWindow,'domready', function() {
                            //console.log("infoWIndow is created");
                            //const upper = document.getElementById('window_contents');
                            console.log(add_btns[i]);
                            add_btns[i] = document.getElementById('add_btn'+String(i));
                            console.log(add_btns[i]);
                            //add_btn = document.getElementById('add_btn');
                            add_btns[i].addEventListener('click', () => {
                                if(place_list.length < 8)
                                {
                                    count_list[i] = 1;
                                    console.log(String(i) + "番目" + String(count_list[i]));
                                    //addable_flag = 1;
                                    //console.log(place);
                                    //console.log("infoWIndow is created");
                                    //place_list.push(marker.getTitle());
                                    //if (addable_flag == 1)
                                    console.log(place_list.length);
                                    //if (count_list[i] == click_counts)
                                    //{
                                    if (place_list.indexOf(marker.getTitle())==-1)
                                    {
                                        place_list.push(marker.getTitle());                                
                                    }
                                    console.log(place_list.length);
                                    
                                    // 一度pタグをすべて削除
                                    while(disp_list.firstChild){
                                        disp_list.removeChild(disp_list.firstChild);
                                    }
                                    // pタグ内に追加した観光地を列挙
                                    for (let i = 0; i < place_list.length; i++) 
                                    { 
                                        const todoList = document.createElement('p');
                                        // pタグの情報
                                        //todoList.id = 'place'+ String(disp_list.childElementCount);
                                        todoList.id = 'place'+ String(i);
                                        //todoList.textContent = marker.getTitle();
                                        todoList.textContent = place_list[i];
                                        // pタグ内に各観光地の削除ボタンを実装
                                        const btn = document.createElement('button')
                                        //btn.id = 'btn' + String(i);
                                        btn.id = 'btn' + String(i);
                                        btn.type = "button";
                                        btn.textContent = "削除";
                                        todoList.appendChild(btn);
                                        disp_list.appendChild(todoList);
                                    }
                　                   count_list[i] = 0;
                　                   }
                　               else
                　               {
                　               window.confirm('目的地は(出発地を含めて)8個までです')
                　               }
                　               
                                enableDelButtons();           　               
                                //addable_flag = 0;
                                //}
                                //console.log(todoList);
                                /*var disp_list_fc = disp_list.firstChild;
                                for(var i=0; i<disp_list_fc; i++){
                                    disp_list.removeChild(disp_list.getElementById('place'+ String(i)));
                                }*/
                                //disp_list.appendChild(todoList);
                                /*infoWindow.close();
                                infoWindow.setContent("added");
                                infoWindow.open(marker.getMap(), marker);*/
                                //console.log(disp_list);
                                //infoWindow.close();
                                /*while(disp_list.firstChild){
                                    disp_list.removeChild(disp_list.firstChild);
                                }*/
                                /*for (let i = 0; i < place_list.length; i++) 
                                { 
                                　  const todoList = document.createElement('p');
                                　  // pタグの情報
                                　  todoList.id = 'place'+ String(i);
    　                               todoList.textContent = place_list[i]; 
                                　  // pタグ内に各観光地の削除ボタンを実装
                                　  const btn = document.createElement('button')
                                　  //btn.id = 'btn' + String(i);
                                　  btn.id = 'btn';
                                　  btn.type = "button";
                                　  btn.textContent = "削除";
                                　  todoList.appendChild(btn);
    　                               disp_list.appendChild(todoList); 
                                }*/
                                /*var todoList = document.createElement('li'); 
                　               todoList.textContent = place_list[place_list.length-1]; 
                　               //todoList.textContent = marker.getTitle();
            　                   disp_list.appendChild(todoList);*/
                            }) 
                            //place_list = [];
                        });
                        //}
                        
                        lastOpenedInfoWindow = infoWindow;
                        //count_list[i] = 0;                                    
                        console.log("外" + String(click_counts) + "+内" + String(i) + "番目" + String(count_list[i]));
                        /*infoWindow.addListener('closeclick', ()=>{
                            for (let i = 0; i < place_list.length; i++) 
                                { 
                                    console.log(place_list[i]);
                                }
                        });*/
    
                        /*if ( place_list.length > num_places) 
                        {
                            //console.log(place_list.length);
                            //let pre_status = place_list.length
                        　  var todoList = document.createElement('li'); 
                    　       console.log(places.length);
            　               todoList.textContent = place_list[place_list.length-1]; 
            　               document.getElementById('disp_list').appendChild(todoList);
            　               num_places = place_list.length;
            　               // console.log(place_list.length);
                        }*/
                        
                        
                    });
                });                    
                
                map.fitBounds(bounds);
            });
            /*for (let i = 0; i < place_list.length; i++) 
            { 
            　  var todoList = document.createElement('li'); 
            　  todoList.textContent = place_list[i]; 
            　  document.getElementById('disp_list').appendChild(todoList); 
            }*/
            /*btn.addEventListener('click', function(){
                //disp_list.removeChild(disp_list.firstChild);
                for (let i = 0; i < place_list.length; i++) 
                { 
                    console.log(place_list[i]);
                }
            })*/
            //console.log(place_ids[0]);
            //const delete_btn = document.getElementById('delete_btn');            
            function enableDelButtons() {
                // 各観光地のidと削除ボタンを取得
                let place_ids = [];
                let delete_btns = [];
                for(let i = 0; i < disp_list.childElementCount; i++)
                {
                    console.log(i);
                    place_ids.push(document.getElementById('place'+ String(i)));
                    delete_btns.push(document.getElementById('btn'+ String(i)));
                }
                for(let i = 0; i < disp_list.childElementCount; i++)
                {
                    console.log(i);
                    delete_btns[i].addEventListener('click', function(){
                        disp_list.removeChild(place_ids[i]);
                        delete place_list[i];
                        place_list = place_list.filter(Boolean);
                    })
                    
                }
            }
        }
        window.initAutocmplete = initAutocomplete;
        /*const btn = document.getElementById('btn');
        btn.addEventListener('click', function(){
            //disp_list.removeChild(disp_list.firstChild);
        })*/
        
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $gapi }}&callback=initAutocomplete&libraries=places" defer>
    </script>
</html>
@endsection