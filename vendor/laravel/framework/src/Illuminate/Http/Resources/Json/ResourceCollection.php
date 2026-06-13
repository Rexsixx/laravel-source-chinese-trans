<?php
/**
 * Illuminate，Http，资源，Json，资源收集
 */

namespace Illuminate\Http\Resources\Json;

use Countable;
use IteratorAggregate;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Http\Resources\CollectsResources;

class ResourceCollection extends JsonResource implements Countable, IteratorAggregate
{
    use CollectsResources;

    /**
     * The resource that this resource collects.
	 * 此资源收集的资源
     *
     * @var string
     */
    public $collects;

    /**
     * The mapped collection instance.
	 * 映射的集合实例
     *
     * @var \Illuminate\Support\Collection
     */
    public $collection;

    /**
     * Create a new resource instance.
	 * 创建一个新的资源实例
     *
     * @param  mixed  $resource
     * @return void
     */
    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->resource = $this->collectResource($resource);
    }

    /**
     * Return the count of items in the resource collection.
	 * 返回资源集合中项目的计数
     *
     * @return int
     */
    public function count()
    {
        return $this->collection->count();
    }

    /**
     * Transform the resource into a JSON array.
	 * 将资源转换为JSON数组
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map->toArray($request)->all();
    }

    /**
     * Create an HTTP response that represents the object.
	 * 创建一个表示对象的HTTP响应
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        return $this->resource instanceof AbstractPaginator
                    ? (new PaginatedResourceResponse($this))->toResponse($request)
                    : parent::toResponse($request);
    }
}
