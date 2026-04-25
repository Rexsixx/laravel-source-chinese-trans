<?php
/**
 * Illuminate，Http，资源，合并值
 */

namespace Illuminate\Http\Resources;

use Illuminate\Support\Collection;

class MergeValue
{
    /**
     * The data to be merged.
	 * 待合并的数据
     *
     * @var array
     */
    public $data;

    /**
     * Create new merge value instance.
	 * 创建新的合并值实例
     *
     * @param  \Illuminate\Support\Collection|array  $data
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data instanceof Collection ? $data->all() : $data;
    }
}
