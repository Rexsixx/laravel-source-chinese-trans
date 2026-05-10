<?php
/**
 * Illuminate，基础，测试，问题，发出Http请求
 */

namespace Illuminate\Foundation\Testing\Concerns;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

trait MakesHttpRequests
{
    /**
     * Additional headers for the request.
	 * 请求的附加标头
     *
     * @var array
     */
    protected $defaultHeaders = [];

    /**
     * Additional server variables for the request.
	 * 请求的其他服务器变量
     *
     * @var array
     */
    protected $serverVariables = [];

    /**
     * Indicates whether redirects should be followed.
	 * 指示是否应该遵循重定向
     *
     * @var bool
     */
    protected $followRedirects = false;

    /**
     * Define additional headers to be sent with the request.
	 * 定义要随请求一起发送的附加标头
     *
     * @param  array $headers
     * @return $this
     */
    public function withHeaders(array $headers)
    {
        $this->defaultHeaders = array_merge($this->defaultHeaders, $headers);

        return $this;
    }

    /**
     * Add a header to be sent with the request.
	 * 添加与请求一起发送的标头
     *
     * @param  string $name
     * @param  string $value
     * @return $this
     */
    public function withHeader(string $name, string $value)
    {
        $this->defaultHeaders[$name] = $value;

        return $this;
    }

    /**
     * Flush all the configured headers.
	 * 刷新所有配置的标头
     *
     * @return $this
     */
    public function flushHeaders()
    {
        $this->defaultHeaders = [];

        return $this;
    }

    /**
     * Define a set of server variables to be sent with the requests.
	 * 定义一组要随请求一起发送的服务器变量
     *
     * @param  array  $server
     * @return $this
     */
    public function withServerVariables(array $server)
    {
        $this->serverVariables = $server;

        return $this;
    }

    /**
     * Disable middleware for the test.
	 * 禁用测试的中间件
     *
     * @param  string|array  $middleware
     * @return $this
     */
    public function withoutMiddleware($middleware = null)
    {
        if (is_null($middleware)) {
            $this->app->instance('middleware.disable', true);

            return $this;
        }

        foreach ((array) $middleware as $abstract) {
            $this->app->instance($abstract, new class {
                public function handle($request, $next)
                {
                    return $next($request);
                }
            });
        }

        return $this;
    }

    /**
     * Enable the given middleware for the test.
	 * 为测试启用给定的中间件
     *
     * @param  string|array  $middleware
     * @return $this
     */
    public function withMiddleware($middleware = null)
    {
        if (is_null($middleware)) {
            unset($this->app['middleware.disable']);

            return $this;
        }

        foreach ((array) $middleware as $abstract) {
            unset($this->app[$abstract]);
        }

        return $this;
    }

    /**
     * Automatically follow any redirects returned from the response.
	 * 自动遵循从响应返回的任何重定向
     *
     * @return $this
     */
    public function followingRedirects()
    {
        $this->followRedirects = true;

        return $this;
    }

    /**
     * Set the referer header to simulate a previous request.
	 * 设置引用头以模拟先前的请求
     *
     * @param  string  $url
     * @return $this
     */
    public function from(string $url)
    {
        return $this->withHeader('referer', $url);
    }

    /**
     * Visit the given URI with a GET request.
	 * 使用GET请求访问给定的URI
     *
     * @param  string  $uri
     * @param  array  $headers
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function get($uri, array $headers = [])
    {
        $server = $this->transformHeadersToServerVars($headers);

        return $this->call('GET', $uri, [], [], [], $server);
    }

    /**
     * Visit the given URI with a GET request, expecting a JSON response.
	 * 使用GET请求访问给定的URI，期望得到JSON响应。
     *
     * @param  string  $uri
     * @param  array  $headers
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function getJson($uri, array $headers = [])
    {
        return $this->json('GET', $uri, [], $headers);
    }

    /**
     * Visit the given URI with a POST request.
	 * 使用POST请求访问给定的URI
     *
     * @param  string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function post($uri, array $data = [], array $headers = [])
    {
        $server = $this->transformHeadersToServerVars($headers);

        return $this->call('POST', $uri, $data, [], [], $server);
    }

    /**
     * Visit the given URI with a POST request, expecting a JSON response.
	 * 使用POST请求访问给定的URI，期望得到JSON响应。
     *
     * @param  string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function postJson($uri, array $data = [], array $headers = [])
    {
        return $this->json('POST', $uri, $data, $headers);
    }

    /**
     * Visit the given URI with a PUT request.
	 * 使用PUT请求访问给定的URI
     *
     * @param  string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function put($uri, array $data = [], array $headers = [])
    {
        $server = $this->transformHeadersToServerVars($headers);

        return $this->call('PUT', $uri, $data, [], [], $server);
    }

    /**
     * Visit the given URI with a PUT request, expecting a JSON response.
	 * 使用一个PUT请求访问给定的URI，期望得到一个JSON响应。
     *
     * @param  string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function putJson($uri, array $data = [], array $headers = [])
    {
        return $this->json('PUT', $uri, $data, $headers);
    }

    /**
     * Visit the given URI with a PATCH request.
	 * 使用PATCH请求访问给定的URI
     *
     * @param  string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function patch($uri, array $data = [], array $headers = [])
    {
        $server = $this->transformHeadersToServerVars($headers);

        return $this->call('PATCH', $uri, $data, [], [], $server);
    }

    /**
     * Visit the given URI with a PATCH request, expecting a JSON response.
	 * 使用PATCH请求访问给定的URI，期望得到JSON响应。
     *
     * @param  string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function patchJson($uri, array $data = [], array $headers = [])
    {
        return $this->json('PATCH', $uri, $data, $headers);
    }

    /**
     * Visit the given URI with a DELETE request.
	 * 使用DELETE请求访问给定的URI
     *
     * @param  string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function delete($uri, array $data = [], array $headers = [])
    {
        $server = $this->transformHeadersToServerVars($headers);

        return $this->call('DELETE', $uri, $data, [], [], $server);
    }

    /**
     * Visit the given URI with a DELETE request, expecting a JSON response.
	 * 使用DELETE请求访问给定的URI，期望得到JSON响应。
     *
     * @param  string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function deleteJson($uri, array $data = [], array $headers = [])
    {
        return $this->json('DELETE', $uri, $data, $headers);
    }

    /**
     * Call the given URI with a JSON request.
	 * 用JSON请求调用给定的URI
     *
     * @param  string  $method
     * @param  string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function json($method, $uri, array $data = [], array $headers = [])
    {
        $files = $this->extractFilesFromDataArray($data);

        $content = json_encode($data);

        $headers = array_merge([
            'CONTENT_LENGTH' => mb_strlen($content, '8bit'),
            'CONTENT_TYPE' => 'application/json',
            'Accept' => 'application/json',
        ], $headers);

        return $this->call(
            $method, $uri, [], [], $files, $this->transformHeadersToServerVars($headers), $content
        );
    }

    /**
     * Call the given URI and return the Response.
	 * 调用给定的URI并返回Response
     *
     * @param  string  $method
     * @param  string  $uri
     * @param  array  $parameters
     * @param  array  $cookies
     * @param  array  $files
     * @param  array  $server
     * @param  string  $content
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        $kernel = $this->app->make(HttpKernel::class);

        $files = array_merge($files, $this->extractFilesFromDataArray($parameters));

        $symfonyRequest = SymfonyRequest::create(
            $this->prepareUrlForRequest($uri), $method, $parameters,
            $cookies, $files, array_replace($this->serverVariables, $server), $content
        );

        $response = $kernel->handle(
            $request = Request::createFromBase($symfonyRequest)
        );

        if ($this->followRedirects) {
            $response = $this->followRedirects($response);
        }

        $kernel->terminate($request, $response);

        return $this->createTestResponse($response);
    }

    /**
     * Turn the given URI into a fully qualified URL.
	 * 将给定的URI转换为完全限定的URL
     *
     * @param  string  $uri
     * @return string
     */
    protected function prepareUrlForRequest($uri)
    {
        if (Str::startsWith($uri, '/')) {
            $uri = substr($uri, 1);
        }

        if (! Str::startsWith($uri, 'http')) {
            $uri = config('app.url').'/'.$uri;
        }

        return trim($uri, '/');
    }

    /**
     * Transform headers array to array of $_SERVER vars with HTTP_* format.
	 * 将headers数组转换为HTTP_*格式的$_SERVER变量数组
     *
     * @param  array  $headers
     * @return array
     */
    protected function transformHeadersToServerVars(array $headers)
    {
        return collect(array_merge($this->defaultHeaders, $headers))->mapWithKeys(function ($value, $name) {
            $name = strtr(strtoupper($name), '-', '_');

            return [$this->formatServerHeaderKey($name) => $value];
        })->all();
    }

    /**
     * Format the header name for the server array.
	 * 格式化服务器数组的标头名称
     *
     * @param  string  $name
     * @return string
     */
    protected function formatServerHeaderKey($name)
    {
        if (! Str::startsWith($name, 'HTTP_') && $name !== 'CONTENT_TYPE' && $name !== 'REMOTE_ADDR') {
            return 'HTTP_'.$name;
        }

        return $name;
    }

    /**
     * Extract the file uploads from the given data array.
	 * 从给定的数据数组中提取文件上传
     *
     * @param  array  $data
     * @return array
     */
    protected function extractFilesFromDataArray(&$data)
    {
        $files = [];

        foreach ($data as $key => $value) {
            if ($value instanceof SymfonyUploadedFile) {
                $files[$key] = $value;

                unset($data[$key]);
            }

            if (is_array($value)) {
                $files[$key] = $this->extractFilesFromDataArray($value);

                $data[$key] = $value;
            }
        }

        return $files;
    }

    /**
     * Follow a redirect chain until a non-redirect is received.
	 * 遵循重定向链，直到接收到非重定向。
     *
     * @param  \Illuminate\Http\Response  $response
     * @return \Illuminate\Http\Response
     */
    protected function followRedirects($response)
    {
        while ($response->isRedirect()) {
            $response = $this->get($response->headers->get('Location'));
        }

        $this->followRedirects = false;

        return $response;
    }

    /**
     * Create the test response instance from the given response.
	 * 根据给定的响应创建测试响应实例
     *
     * @param  \Illuminate\Http\Response  $response
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    protected function createTestResponse($response)
    {
        return TestResponse::fromBaseResponse($response);
    }
}
