<?php
/**
 * 路由，api
 */

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes	API路由
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
| 这里就是您为您的应用程序注册 API 路由的地方。
| 这些路由由 RouteServiceProvider 在一个被分配了“api”中间件组的组中加载。尽情构建您的 API 吧！
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
