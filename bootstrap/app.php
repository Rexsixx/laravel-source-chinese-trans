<?php
/**
 * 启动，app
 */

/*
|--------------------------------------------------------------------------
| Create The Application	创建应用程序
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
| 我们首先要做的就是创建一个新的 Laravel 应用实例，
| 这个实例将作为 Laravel 所有组件的“连接器”，并且是系统中用于绑定各种部分的依赖注入容器。
|
*/

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces		绑定重要接口
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
| 接下来，我们需要将一些重要的接口绑定到容器中，以便在需要时能够调用它们。
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application	返回应用
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
| 此脚本会返回应用程序实例。
| 该实例会传递给调用脚本，这样我们就能将实例的构建与应用程序的实际运行以及响应的发送分离开来。
|
*/

return $app;
