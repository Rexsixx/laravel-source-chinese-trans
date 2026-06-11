<?php
/**
 * Illuminate，契约，支持，Responsable
 */

namespace Illuminate\Contracts\Support;

interface Responsable
{
    /**
     * Create an HTTP response that represents the object.
	 * 创建一个表示对象的HTTP响应
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request);
}
