<?php
/**
 * Illuminate，基础，提供者，基础服务提供商
 */

namespace Illuminate\Foundation\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\AggregateServiceProvider;

class FoundationServiceProvider extends AggregateServiceProvider
{
    /**
     * The provider class names.
	 * 提供程序类名
     *
     * @var array
     */
    protected $providers = [
        FormRequestServiceProvider::class,
    ];

    /**
     * Register the service provider.
	 * 注册服务提供者
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->registerRequestValidate();
    }

    /**
     * Register the "validate" macro on the request.
	 * 在请求上注册“validate”宏
     *
     * @return void
     */
    public function registerRequestValidate()
    {
        Request::macro('validate', function (array $rules, ...$params) {
            validator()->validate($this->all(), $rules, ...$params);

            return $this->only(collect($rules)->keys()->map(function ($rule) {
                return str_contains($rule, '.') ? explode('.', $rule)[0] : $rule;
            })->unique()->toArray());
        });
    }
}
