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
| 这里是您可以为应用程序注册API路由的地方。
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
