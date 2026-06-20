<?php
/**
 * Laravel，Tinker，配置
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Console Commands	控制台命令
    |--------------------------------------------------------------------------
    |
    | This option allows you to add additional Artisan commands that should
    | be available within the Tinker environment. Once the command is in
    | this array you may execute the command in Tinker using its name.
	| 此选项允许您添加一些额外的“艺术家”命令，这些命令应在“ tink器”环境中可用。
    |
    */

    'commands' => [
        // App\Console\Commands\ExampleCommand::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Alias Blacklist	别名黑名单
    |--------------------------------------------------------------------------
    |
    | Typically, Tinker automatically aliases classes as you require them in
    | Tinker. However, you may wish to never alias certain classes, which
    | you may accomplish by listing the classes in the following array.
	| 通常情况下，在 Tinker 中，您只需指定需求，Tinker 就会自动为相关类设置别名。
    |
    */

    'dont_alias' => [
        'App\Nova',
    ],

];
