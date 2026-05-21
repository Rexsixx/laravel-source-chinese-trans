<?php
/**
 * 配置，queue
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Queue Connection Name		默认队列连接名
    |--------------------------------------------------------------------------
    |
    | Laravel's queue API supports an assortment of back-ends via a single
    | API, giving you convenient access to each back-end using the same
    | syntax for every one. Here you may define a default connection.
	| Laravel 的队列 API 通过单一 API 支持多种后端，让您能够使用相同的语法便捷地访问每一个后端。
    |
    */

    'default' => env('QUEUE_DRIVER', 'sync'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections		队列连接
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each server that
    | is used by your application. A default configuration has been added
    | for each back-end shipped with Laravel. You are free to add more.
	| 在这里，您可以为您的应用程序所使用的每台服务器配置连接信息。
    |
    | Drivers: "sync", "database", "beanstalkd", "sqs", "redis", "null"
    |
    */

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 90,
        ],

        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host' => 'localhost',
            'queue' => 'default',
            'retry_after' => 90,
        ],

        'sqs' => [
            'driver' => 'sqs',
            'key' => env('SQS_KEY', 'your-public-key'),
            'secret' => env('SQS_SECRET', 'your-secret-key'),
            'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
            'queue' => env('SQS_QUEUE', 'your-queue-name'),
            'region' => env('SQS_REGION', 'us-east-1'),
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'default',
            'retry_after' => 90,
            'block_for' => null,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs		失败的队列工作
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control which database and table are used to store the jobs that
    | have failed. You may change them to any database / table you wish.
	| 这些选项用于配置失败队列作业日志的处理方式，使您能够控制使用哪些数据库和表来存储已失败的作业。
    |
    */

    'failed' => [
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'failed_jobs',
    ],

];
