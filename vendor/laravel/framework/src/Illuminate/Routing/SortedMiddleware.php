<?php
/**
 * Illuminate，路由，排序的中间件
 */

namespace Illuminate\Routing;

use Illuminate\Support\Collection;

class SortedMiddleware extends Collection
{
    /**
     * Create a new Sorted Middleware container.
	 * 创建一个新的Sorted Middleware容器
     *
     * @param  array  $priorityMap
     * @param  array|\Illuminate\Support\Collection  $middlewares
     * @return void
     */
    public function __construct(array $priorityMap, $middlewares)
    {
        if ($middlewares instanceof Collection) {
            $middlewares = $middlewares->all();
        }

        $this->items = $this->sortMiddleware($priorityMap, $middlewares);
    }

    /**
     * Sort the middlewares by the given priority map.
	 * 根据给定的优先级映射对中间件进行排序。
     *
     * Each call to this method makes one discrete middleware movement if necessary.
     *
     * @param  array  $priorityMap
     * @param  array  $middlewares
     * @return array
     */
    protected function sortMiddleware($priorityMap, $middlewares)
    {
        $lastIndex = 0;

        foreach ($middlewares as $index => $middleware) {
            if (! is_string($middleware)) {
                continue;
            }

            $stripped = head(explode(':', $middleware));

            if (in_array($stripped, $priorityMap)) {
                $priorityIndex = array_search($stripped, $priorityMap);

                // This middleware is in the priority map. If we have encountered another middleware
                // that was also in the priority map and was at a lower priority than the current
                // middleware, we will move this middleware to be above the previous encounter.
				// 此中间件位于优先级列表中。如果我们在后续过程中又遇到了另一个同样在优先级列表中的中间件，
				// 且其优先级低于当前的这个中间件，那么我们将把当前这个中间件移到比之前遇到的那个位置更高的位置。
                if (isset($lastPriorityIndex) && $priorityIndex < $lastPriorityIndex) {
                    return $this->sortMiddleware(
                        $priorityMap, array_values($this->moveMiddleware($middlewares, $index, $lastIndex))
                    );
                }

                // This middleware is in the priority map; but, this is the first middleware we have
                // encountered from the map thus far. We'll save its current index plus its index
                // from the priority map so we can compare against them on the next iterations.
				// 此中间件已在优先级列表中；但这是我们在目前所查看的列表中遇到的第一个此类中间件。
                $lastIndex = $index;
                $lastPriorityIndex = $priorityIndex;
            }
        }

        return array_values(array_unique($middlewares, SORT_REGULAR));
    }

    /**
     * Splice a middleware into a new position and remove the old entry.
	 * 将中间件拼接到新位置并删除旧条目
     *
     * @param  array  $middlewares
     * @param  int  $from
     * @param  int  $to
     * @return array
     */
    protected function moveMiddleware($middlewares, $from, $to)
    {
        array_splice($middlewares, $to, 0, $middlewares[$from]);

        unset($middlewares[$from + 1]);

        return $middlewares;
    }
}
