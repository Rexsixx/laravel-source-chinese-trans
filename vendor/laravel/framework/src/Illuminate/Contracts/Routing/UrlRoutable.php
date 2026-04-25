<?php
/**
 * Illuminate，契约，路由，Url 可路由的
 */

namespace Illuminate\Contracts\Routing;

interface UrlRoutable
{
    /**
     * Get the value of the model's route key.
	 * 获取模型的路由键值
     *
     * @return mixed
     */
    public function getRouteKey();

    /**
     * Get the route key for the model.
	 * 获取模型的路由键
     *
     * @return string
     */
    public function getRouteKeyName();

    /**
     * Retrieve the model for a bound value.
	 * 检索绑定值的模型
     *
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value);
}
