<?php
/**
 * Faker，唯一生成器
 */

namespace Faker;

/**
 * Proxy for other generators, to return only unique values. Works with
 * Faker\Generator\Base->unique()
 * 其他生成器的代理，只返回唯一的值。
 */
class UniqueGenerator
{
    protected $generator;
    protected $maxRetries;
    protected $uniques = array();

    /**
     * @param Generator $generator
     * @param integer $maxRetries
     */
    public function __construct(Generator $generator, $maxRetries = 10000)
    {
        $this->generator = $generator;
        $this->maxRetries = $maxRetries;
    }

    /**
     * Catch and proxy all generator calls but return only unique values
	 * 捕获并代理所有生成器调用，但只返回唯一的值。
     * @param string $attribute
     * @return mixed
     */
    public function __get($attribute)
    {
        return $this->__call($attribute, array());
    }

    /**
     * Catch and proxy all generator calls with arguments but return only unique values
	 * 捕获并代理所有带参数的生成器调用，但只返回唯一的值。
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (!isset($this->uniques[$name])) {
            $this->uniques[$name] = array();
        }
        $i = 0;
        do {
            $res = call_user_func_array(array($this->generator, $name), $arguments);
            $i++;
            if ($i > $this->maxRetries) {
                throw new \OverflowException(sprintf('Maximum retries of %d reached without finding a unique value', $this->maxRetries));
            }
        } while (array_key_exists(serialize($res), $this->uniques[$name]));
        $this->uniques[$name][serialize($res)]= null;

        return $res;
    }
}
