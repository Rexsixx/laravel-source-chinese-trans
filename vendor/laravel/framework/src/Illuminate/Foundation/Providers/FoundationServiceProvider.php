<?php
/**
 * Illuminate，基础，供应商，基础服务提供商
 */

namespace Illuminate\Foundation\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
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

        $this->registerRequestValidation();
        $this->registerRequestSignatureValidation();
    }

    /**
     * Register the "validate" macro on the request.
	 * 在请求上注册“validate”宏
     *
     * @return void
     */
    public function registerRequestValidation()
    {
        Request::macro('validate', function (array $rules, ...$params) {
            return validator()->validate($this->all(), $rules, ...$params);
        });
    }

    /**
     * Register the "hasValidSignature" macro on the request.
	 * 在请求上注册“hasValidSignature”宏
     *
     * @return void
     */
    public function registerRequestSignatureValidation()
    {
        Request::macro('hasValidSignature', function () {
            return URL::hasValidSignature($this);
        });
    }
}
