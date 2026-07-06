<?php
/**
 * 引导，应用
 */

/*
|--------------------------------------------------------------------------
| Create The Application	创建应用程序
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
| 我们要做的第一件事就是创建一个新的Laravel应用实例,
| 它作为Laravel的所有组件的“glue”,是系统绑定所有各个部分的IoC容器。
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
| 接下来,我们需要将一些重要的接口绑定到容器中,这样我们就能在需要时解决它们。
| 内核从web和CLI中服务于该应用程序的传入请求。
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
| Return The Application	返回应用程序
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
| 这个脚本返回应用程序实例。
| 该实例被赋予调用脚本,因此我们可以将实例的构建与应用程序的实际运行分开,并发送响应。
|
*/

return $app;
