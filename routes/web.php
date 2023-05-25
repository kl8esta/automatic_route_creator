<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['auth']], function(){
    // マイページ画面
    Route::get('/', 'UserController@index');
    
    // 観光地検索・リスト作成ページへの遷移
    Route::get('/posts/create_route', 'RoutePostController@create_route');
    
    // 観光地リスト作成画面でのform送信で起動
    Route::post('/pre_posts', 'RoutePostController@save_route');
    
    // 観光地ルート出力結果＆ルート投稿作成画面への遷移
    Route::get('/posts/post_route', 'RoutePostController@post_route');
    
    // ルート投稿画面でのform送信で起動
    Route::post('/posts', 'RoutePostController@save_form');
    
    // マイ(非公開)ルートの一覧ページ＆詳細ページ
    Route::get('/posts/public_list', 'RoutePostController@show_public_list');
    Route::get('/posts/public_list/{route_post}', 'RoutePostController@public_list_one');
    
    // 公開ルートの一覧ページ＆詳細ページ
    Route::get('/posts/private_list', 'UserController@show_private_list');
    Route::get('/posts/private_list/{route_post}', 'RoutePostController@private_list_one');
    Route::post('/ajaxfav', 'FavoriteController@ajaxfav');
    
    // いいねルートの一覧ページ
    Route::get('/posts/favorite_list', 'UserController@show_favorite_list');
    
    // マイルートの編集・更新form送信・削除のルーティング
    Route::get('/posts/{route_post}/edit', 'RoutePostController@editPost');
    Route::put('/posts/{route_post}', 'RoutePostController@updatePost');
    Route::delete('/posts/{route_post}', 'RoutePostController@delete');
});

Auth::routes();