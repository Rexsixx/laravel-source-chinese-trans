<?php
/**
 * Illuminate，契约，广播，工厂
 */

namespace Illuminate\Contracts\Broadcasting;

interface Factory
{
    /**
     * Get a broadcaster implementation by name.
	 * 按名称获取广播器实现
     *
     * @param  string  $name
     * @return void
     */
    public function connection($name = null);
}
