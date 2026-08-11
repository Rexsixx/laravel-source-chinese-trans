<?php
/**
 * api路由
 */

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes	API的路由
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
| 在此处可以为您的应用程序注册 API 路由。
| 这些路由由具有“api”中间件组的路由处理器加载。尽情构建您的API吧！
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
