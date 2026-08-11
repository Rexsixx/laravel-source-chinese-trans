<?php
/**
 * Dotenv，Regex，结果
 */

namespace Dotenv\Regex;

abstract class Result
{
    /**
     * Get the success option value.
	 * 获得成功的选项值
     *
     * @return \PhpOption\Option
     */
    abstract public function success();

    /**
     * Get the error value, if possible.
	 * 如果可能，获取错误值。
     *
     * @return string|int
     */
    public function getSuccess()
    {
        return $this->success()->get();
    }

    /**
     * Map over the success value.
	 * 映射成功值
     *
     * @param callable $f
     *
     * @return \Dotenv\Regex\Result
     */
    abstract public function mapSuccess(callable $f);

    /**
     * Get the error option value.
	 * 获取错误选项值
     *
     * @return \PhpOption\Option
     */
    abstract public function error();

    /**
     * Get the error value, if possible.
	 * 如果可能的话,获取错误值。
     *
     * @return string
     */
    public function getError()
    {
        return $this->error()->get();
    }

    /**
     * Map over the error value.
	 * 映射到错误值
     *
     * @param callable $f
     *
     * @return \Dotenv\Regex\Result
     */
    abstract public function mapError(callable $f);
}
