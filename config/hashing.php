<?php
/**
 * 配置，hashing
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Hash Driver	默认哈希驱动程序
    |--------------------------------------------------------------------------
    |
    | This option controls the default hash driver that will be used to hash
    | passwords for your application. By default, the bcrypt algorithm is
    | used; however, you remain free to modify this option if you wish.
	| 此选项用于控制将用于对您的应用程序的密码进行哈希处理的默认哈希驱动程序。
	| 默认使用 bcrypt 算法，但如果您愿意，仍可自由修改此选项。
    |
    | Supported: "bcrypt", "argon"
    |
    */

    'driver' => 'bcrypt',

    /*
    |--------------------------------------------------------------------------
    | Bcrypt Options	Bcrypt选项
    |--------------------------------------------------------------------------
    |
    | Here you may specify the configuration options that should be used when
    | passwords are hashed using the Bcrypt algorithm. This will allow you
    | to control the amount of time it takes to hash the given password.
	| 在此您可以指定在使用 Bcrypt 算法对密码进行哈希处理时应采用的配置选项。
	| 这将允许你控制给定密码哈希所需的时间。
    |
    */

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Argon Options		Argon选项
    |--------------------------------------------------------------------------
    |
    | Here you may specify the configuration options that should be used when
    | passwords are hashed using the Argon algorithm. These will allow you
    | to control the amount of time it takes to hash the given password.
	| 在此您可以指定在使用阿贡算法对密码进行哈希处理时应采用的配置选项。
	| 这些选项将允许您控制给定密码哈希所需的时间。
    |
    */

    'argon' => [
        'memory' => 1024,
        'threads' => 2,
        'time' => 2,
    ],

];
