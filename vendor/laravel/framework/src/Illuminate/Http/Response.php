<?php
/**
 * Illuminate，Http，响应
 */

namespace Illuminate\Http;

use ArrayObject;
use JsonSerializable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class Response extends BaseResponse
{
    use ResponseTrait, Macroable {
        Macroable::__call as macroCall;
    }

    /**
     * Set the content on the response.
	 * 设置响应的内容
     *
     * @param  mixed  $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->original = $content;

        // If the content is "JSONable" we will set the appropriate header and convert
        // the content to JSON. This is useful when returning something like models
        // from routes that will be automatically transformed to their JSON form.
		// 如果内容是“可转换为 JSON 格式”的，我们将设置相应的标头，并将内容转换为 JSON 格式。
		// 当从路由中返回诸如模型之类的数据，并且这些数据会自动转换为 JSON 格式时，这种方法就很有用。
        if ($this->shouldBeJson($content)) {
            $this->header('Content-Type', 'application/json');

            $content = $this->morphToJson($content);
        }

        // If this content implements the "Renderable" interface then we will call the
        // render method on the object so we will avoid any "__toString" exceptions
        // that might be thrown and have their errors obscured by PHP's handling.
		// 如果此内容实现了“可渲染”接口，那么我们将调用该对象的“渲染”方法，
		// 这样就能避免可能出现的“__toString”异常，并且这些错误也能通过 PHP 的处理方式得以掩盖。
        elseif ($content instanceof Renderable) {
            $content = $content->render();
        }

        parent::setContent($content);

        return $this;
    }

    /**
     * Determine if the given content should be turned into JSON.
	 * 确定是否应该将给定的内容转换为JSON
     *
     * @param  mixed  $content
     * @return bool
     */
    protected function shouldBeJson($content)
    {
        return $content instanceof Arrayable ||
               $content instanceof Jsonable ||
               $content instanceof ArrayObject ||
               $content instanceof JsonSerializable ||
               is_array($content);
    }

    /**
     * Morph the given content into JSON.
	 * 将给定的内容转换为JSON
     *
     * @param  mixed   $content
     * @return string
     */
    protected function morphToJson($content)
    {
        if ($content instanceof Jsonable) {
            return $content->toJson();
        } elseif ($content instanceof Arrayable) {
            return json_encode($content->toArray());
        }

        return json_encode($content);
    }
}
