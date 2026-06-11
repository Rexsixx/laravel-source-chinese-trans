<?php
/**
 * GuzzleHttp，许诺，协议接口
 */

namespace GuzzleHttp\Promise;

/**
 * Interface used with classes that return a promise.
 * 使用返回一个承诺的类的接口。
 */
interface PromisorInterface
{
    /**
     * Returns a promise.
	 * 返回一个承诺
     *
     * @return PromiseInterface
     */
    public function promise();
}
