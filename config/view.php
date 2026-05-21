<?php
/**
 * 配置，视图
 */

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths	视图存储路径
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
	| 大多数模板系统从磁盘加载模板。
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path	编译视图路径
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. Typically, this is within the storage
    | directory. However, as usual, you are free to change this value.
	| 此选项决定了所有编译后的 Blade 模板将在您的应用程序中存储的位置。
    |
    */

    'compiled' => realpath(storage_path('framework/views')),

];
