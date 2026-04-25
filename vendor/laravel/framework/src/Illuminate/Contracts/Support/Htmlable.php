<?php
/**
 * Illuminate，契约，支持，可转为HTML的
 */

namespace Illuminate\Contracts\Support;

interface Htmlable
{
    /**
     * Get content as a string of HTML.
	 * 获取HTML字符串形式的内容
     *
     * @return string
     */
    public function toHtml();
}
