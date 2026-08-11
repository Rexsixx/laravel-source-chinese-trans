<?php
/**
 * web路由
 */

/*
|--------------------------------------------------------------------------
| Web Routes	web路由
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
| 在这里可以为您的应用程序注册网络路由。
| 这些路由由包含“web”中间件组的路由处理器加载。现在开始创造一些精彩的东西吧！
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/index', 'IndexController@index');

//自定义添加
Route::get('/test', 'TestController@test');
