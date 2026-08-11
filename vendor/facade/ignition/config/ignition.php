<?php
/**
 * Facade，Ignition，配置，Ignition
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Editor	编辑
    |--------------------------------------------------------------------------
    |
    | Choose your preferred editor to use when clicking any edit button.
	| 单击任何编辑按钮时，选择要使用的首选编辑器。
    |
    | Supported: "phpstorm", "vscode", "vscode-insiders", "textmate", "emacs",
    |            "sublime", "atom", "nova", "macvim", "idea", "netbeans",
    |            "xdebug"
    |
    */

    'editor' => env('IGNITION_EDITOR', 'phpstorm'),

    /*
    |--------------------------------------------------------------------------
    | Theme	主题
    |--------------------------------------------------------------------------
    |
    | Here you may specify which theme Ignition should use.
	| 在这里，您可以指定应该使用哪个主题。
    |
    | Supported: "light", "dark", "auto"
    |
    */

    'theme' => env('IGNITION_THEME', 'light'),

    /*
    |--------------------------------------------------------------------------
    | Sharing	共享
    |--------------------------------------------------------------------------
    |
    | You can share local errors with colleagues or others around the world.
    | Sharing is completely free and doesn't require an account on Flare.
	| 你可以与同事或世界各地的其他人共享本地错误信息。分享完全免费，且无需在 Flare 上注册账户。
    |
    | If necessary, you can completely disable sharing below.
	| 如有必要，您可以完全禁用下面的共享。
    |
    */

    'enable_share_button' => env('IGNITION_SHARING_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Register Ignition commands	注册点火命令
    |--------------------------------------------------------------------------
    |
    | Ignition comes with an additional make command that lets you create
    | new solution classes more easily. To keep your default Laravel
    | installation clean, this command is not registered by default.
	| Ignition 配备了一个额外的 make 命令，可让您更轻松地创建新的解决方案类。
	| 为了保持默认的 Laravel 安装干净，此命令默认未注册。
    |
    | You can enable the command registration below.
	| 您可以启用下面的命令注册。
    |
    */
    'register_commands' => env('REGISTER_IGNITION_COMMANDS', false),

    /*
    |--------------------------------------------------------------------------
    | Ignored Solution Providers	被忽略的解决方案提供商
    |--------------------------------------------------------------------------
    |
    | You may specify a list of solution providers (as fully qualified class
    | names) that shouldn't be loaded. Ignition will ignore these classes
    | and possible solutions provided by them will never be displayed.
	| 您可以指定一组不应被加载的解决方案提供程序（以完全限定的类名形式）。
	| Ignition 将忽略这些类，且它们提供的可能解决方案将不会显示。
    |
    */

    'ignored_solution_providers' => [
        Facade\Ignition\SolutionProviders\MissingPackageSolutionProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Runnable Solutions	可运行解决方案
    |--------------------------------------------------------------------------
    |
    | Some solutions that Ignition displays are runnable and can perform
    | various tasks. Runnable solutions are enabled when your app has
    | debug mode enabled. You may also fully disable this feature.
	| Ignition 显示的某些解决方案可以运行并执行各种任务。
	| 当您的应用启用了调试模式时，可运行的解决方案将被启用。您也可以完全禁用此功能。
    |
    */

    'enable_runnable_solutions' => env('IGNITION_ENABLE_RUNNABLE_SOLUTIONS', null),

    /*
    |--------------------------------------------------------------------------
    | Remote Path Mapping	远端路径映射
    |--------------------------------------------------------------------------
    |
    | If you are using a remote dev server, like Laravel Homestead, Docker, or
    | even a remote VPS, it will be necessary to specify your path mapping.
	| 如果你使用的是远程开发服务器，例如 Laravel Homestead、Docker 或甚至远程 VPS，则需要指定路径映射。
    |
    | Leaving one, or both of these, empty or null will not trigger the remote
    | URL changes and Ignition will treat your editor links as local files.
	| 如果留空或设置为 null，其中一项或两项将不会触发远程 URL 的更改，Ignition 会将您的编辑器链接视为本地文件。
    |
    | "remote_sites_path" is an absolute base path for your sites or projects
    | in Homestead, Vagrant, Docker, or another remote development server.
    |
    | Example value: "/home/vagrant/Code"
    |
    | "local_sites_path" is an absolute base path for your sites or projects
    | on your local computer where your IDE or code editor is running on.
    |
    | Example values: "/Users/<name>/Code", "C:\Users\<name>\Documents\Code"
    |
    */

    'remote_sites_path' => env('IGNITION_REMOTE_SITES_PATH', ''),
    'local_sites_path' => env('IGNITION_LOCAL_SITES_PATH', ''),

    /*
    |--------------------------------------------------------------------------
    | Housekeeping Endpoint Prefix	Housekeeping端点前缀
    |--------------------------------------------------------------------------
    |
    | Ignition registers a couple of routes when it is enabled. Below you may
    | specify a route prefix that will be used to host all internal links.
	| 当它被启用时，会注册一对路由。
	| 您可以在下方指定一个路由前缀，用于托管所有内部链接。
    |
    */
    'housekeeping_endpoint_prefix' => '_ignition',

];
