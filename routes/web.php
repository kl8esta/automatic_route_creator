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
    Route::get('/posts/create_route', 'RoutePostController@create_route');
    Route::get('/posts/post_route', 'RoutePostController@post_route');
    Route::post('/posts', 'RoutePostController@save_form');
});

Auth::routes();