<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::any('/wx','WxController@index');
//点击微博登录
Route::any('weibo','WxController@weibo');
Route::any('denglu','WxController@denglu');


 Route::any('center','UserController@center');
 Route::any('login','UserController@login');
 Route::any('logout','UserController@logout');

//商城路由
Route::get('/','GoodsController@index');
Route::get('insert','GoodsController@insert');
Route::get('goods/{gid}','GoodsController@goods');
//买
Route::any('buy/{gid}','GoodsController@buy');
//查看购物车
Route::get('cart','GoodsController@cart');
//清空
Route::any('cart_clear','GoodsController@cart_clear');

//订单表
Route::post('done','GoodsController@done');
//商品快照表
Route::post('pay','GoodsController@pay');