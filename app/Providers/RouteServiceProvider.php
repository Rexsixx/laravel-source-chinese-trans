<?php
/**
 * App，供应商，路由服务提供商
 */

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
	 * 此命名空间应用于控制器路由。
     *
     * In addition, it is set as the URL generator's root namespace.
	 * 此外，它被设置为URL生成器的根名称空间。
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
	 * 定义你的路由模型绑定、模式过滤器等。
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
	 * 为应用程序定义路由
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
	 * 为应用程序定义“web”路由。
     *
     * These routes all receive session state, CSRF protection, etc.
	 * 这些路由都接收会话状态,CSRF保护等。
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
	 * 定义应用程序的“api”路由。
     *
     * These routes are typically stateless.
	 * 这些路线通常是无状态的。
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}
