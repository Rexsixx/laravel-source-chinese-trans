<?php
/**
 * 路由，控制台
 */

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes	控制台路由
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
| 此文件中您可以定义所有基于 Closure 的控制台命令。
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');
