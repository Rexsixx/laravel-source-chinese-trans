<?php
/**
 * Illuminate，数据库，查询，Json 表达式
 */

namespace Illuminate\Database\Query;

use InvalidArgumentException;

class JsonExpression extends Expression
{
    /**
     * Create a new raw query expression.
	 * 创建一个新的原始查询表达式
     *
     * @param  mixed  $value
     * @return void
     */
    public function __construct($value)
    {
        parent::__construct(
            $this->getJsonBindingParameter($value)
        );
    }

    /**
     * Translate the given value into the appropriate JSON binding parameter.
	 * 将给定的值转换为适当的JSON绑定参数
     *
     * @param  mixed  $value
     * @return string
     */
    protected function getJsonBindingParameter($value)
    {
        switch ($type = gettype($value)) {
            case 'boolean':
                return $value ? 'true' : 'false';
            case 'integer':
            case 'double':
                return $value;
            case 'string':
                return '?';
            case 'object':
            case 'array':
                return '?';
        }

        throw new InvalidArgumentException("JSON value is of illegal type: {$type}");
    }
}
