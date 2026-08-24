<?php
/**
 * Facade，ignition，flare
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
	| 这些选项决定了哪些信息将被传输到Flare
    |
    */

    'reporting' => [
        'anonymize_ips' => true,
        'collect_git_information' => false,
        'report_queries' => true,
        'maximum_number_of_collected_queries' => 200,
        'report_query_bindings' => true,
        'report_view_data' => true,
        'grouping_type' => null,
        'report_logs' => true,
        'maximum_number_of_collected_logs' => 200,
        'censor_request_body_fields' => ['password'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Reporting Log statements
    |--------------------------------------------------------------------------
    |
    | If this setting is `false` log statements won't be sent as events to Flare,
    | no matter which error level you specified in the Flare log channel.
    |
    */

    'send_logs_as_events' => true,

    /*
    |--------------------------------------------------------------------------
    | Censor request body fields
    |--------------------------------------------------------------------------
    |
    | These fields will be censored from your request when sent to Flare.
    |
    */

    'censor_request_body_fields' => ['password'],
];
