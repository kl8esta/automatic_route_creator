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
    Route::get('/', 'UserController@index');
    Route::post('/posts', 'RoutePostController@save_form');
    Route::get('/posts/create_route', 'RoutePostController@create_route');
    Route::get('/posts/post_route', 'RoutePostController@post_route');
    Route::get('/posts/public_list/{route_post}', 'RoutePostController@public_list_one');
    Route::get('/posts/private_list/{route_post}', 'RoutePostController@private_list_one');
    Route::get('/posts/public_list', 'RoutePostController@show_public_list');
    Route::get('/posts/private_list', 'UserController@show_private_list');
});

Auth::routes();