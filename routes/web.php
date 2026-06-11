<?php
/**
 * 路由，web
 */

/*
|--------------------------------------------------------------------------
| Web Routes	Web 路由
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
| 这里是您可以为应用程序注册web路由的地方。
| 这些路由在包含“web”中间件组的组内由RouteServiceProvider加载。现在创造伟大的东西!
|
*/

Route::get('/', function () {
    return view('welcome');
});
