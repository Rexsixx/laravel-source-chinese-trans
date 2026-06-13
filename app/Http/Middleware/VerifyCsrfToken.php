<?php
/**
 * App，Http，中间件，验证 Csrf令牌
 */

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
	 * 指示是否应该在响应上设置xsrf令牌cookie
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
	 * 该uri应该被排除在CSRF验证之外
     *
     * @var array
     */
    protected $except = [
        //
    ];
}
