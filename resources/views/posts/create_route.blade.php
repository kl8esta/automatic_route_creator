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
        <div id="map" style="width: 500px; height: 500px;">
            
        </div>
    </body>
    <script>
        function initAutocomplete() {
        const map = new google.maps.Map(
            document.getElementById("map"),
            {
              center: { lat: 35.6810, lng: 139.7673 },
              zoom: 13,
              mapTypeId: "roadmap",
            }
          );
        
          // Create the search box and link it to the UI element.
          const input = document.getElementById("pac-input");
          const searchBox = new google.maps.places.SearchBox(input);
        
          map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
        
          // Bias the SearchBox results towards current map's viewport.
          map.addListener("bounds_changed", () => {
            searchBox.setBounds(map.getBounds());
          });
        
          let markers = [];
        
          // Listen for the event fired when the user selects a prediction and retrieve
          // more details for that place.
          searchBox.addListener("places_changed", () => {
            const places = searchBox.getPlaces();
            
            // Create an info window to share between markers.
            const infoWindow = new google.maps.InfoWindow();
        
            if (places.length == 0) {
              return;
            }
        
            // Clear out the old markers.
            markers.forEach((marker) => {
              marker.setMap(null);
            });
            markers = [];
        
            // For each place, get the icon, name and location.
            const bounds = new google.maps.LatLngBounds();
        
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
        
              // Create a marker for each place.
              /*markers.push(
                new google.maps.Marker({
                  map,
                  title: place.name,
                  position: place.geometry.location,
                })
              );*/
              const marker = new google.maps.Marker({
                  map,
                  title: place.name,
                  stars: place.rating,
                  position: place.geometry.location,
              });
              
              // Add a click listener for each marker, and set up the info window.
              marker.addListener("click", () => {
                infoWindow.close();
                infoWindow.setContent(marker.getTitle());
                infoWindow.open(marker.getMap(), marker);
              });
              
              markers.push(marker);
        
              if (place.geometry.viewport) {
                // Only geocodes have viewport.
                bounds.union(place.geometry.viewport);
              } else {
                bounds.extend(place.geometry.location);
              }
            });
            map.fitBounds(bounds);
          });
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBX-T2KgCdV5nEYyUZmcFLflMmW76c7gHs&callback=initAutocomplete&libraries=places" defer></script>
</html>
@endsection