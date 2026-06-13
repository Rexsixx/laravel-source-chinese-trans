<?php
/**
 * 配置，view
 */

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths	查看存储路径
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
	| 大多数模板系统都是从磁盘加载模板的。在这里，您可以指定一系列需要检查的路径，以确定您的视图文件所在的位置。
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
	| 这个选项决定了所有编译后的刀片模板将在哪里存储到您的应用程序中。
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),

];
