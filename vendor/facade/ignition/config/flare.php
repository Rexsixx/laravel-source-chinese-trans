<?php
/**
 * Facade，Ignition，配置，flare
 */

return [
    /*
    |
    |--------------------------------------------------------------------------
    | Flare API key		Flare API密钥
    |--------------------------------------------------------------------------
    |
    | Specify Flare's API key below to enable error reporting to the service.
	| 在下面指定Flare的API密钥，以启用向服务报告错误。
    |
    | More info: https://flareapp.io/docs/general/projects
    |
    */

    'key' => env('FLARE_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Reporting Options		报告选项
    |--------------------------------------------------------------------------
    |
    | These options determine which information will be transmitted to Flare.
	| 这些选项决定了哪些信息将被传输到Flare。
    |
    */

    'reporting' => [
        'anonymize_ips' => true,
        'collect_git_information' => true,
        'report_queries' => true,
        'maximum_number_of_collected_queries' => 200,
        'report_query_bindings' => true,
        'report_view_data' => true,
        'grouping_type' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Reporting Log statements	日志报表
    |--------------------------------------------------------------------------
    |
    | If this setting is `false` log statements won't be send as events to Flare,
    | no matter which error level you specified in the Flare log channel.
	| 如果此设置为“false”，无论您在Flare日志通道中指定何种错误级别，日志消息都不会作为事件发送到Flare。
    |
    */

    'send_logs_as_events' => true,
];
