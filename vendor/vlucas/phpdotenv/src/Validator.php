<?php
/**
 * Dotenv，验证程序
 */

namespace Dotenv;

use Dotenv\Exception\InvalidCallbackException;
use Dotenv\Exception\ValidationException;

/**
 * This is the validator class.
 * 这是验证器类
 *
 * It's responsible for applying validations against a number of variables.
 * 它负责对一些变量进行有效的验证。
 */
class Validator
{
    /**
     * The variables to validate.
	 * 验证的变量
     *
     * @var array
     */
    protected $variables;

    /**
     * The loader instance.
	 * 加载实例
     *
     * @var \Dotenv\Loader
     */
    protected $loader;

    /**
     * Create a new validator instance.
	 * 创建一个新的验证器实例
     *
     * @param array          $variables
     * @param \Dotenv\Loader $loader
     *
     * @return void
     */
    public function __construct(array $variables, Loader $loader)
    {
        $this->variables = $variables;
        $this->loader = $loader;

        $this->assertCallback(
            function ($value) {
                return $value !== null;
            },
            'is missing'
        );
    }

    /**
     * Assert that each variable is not empty.
	 * 断言每个变量都不是空的
     *
     * @return \Dotenv\Validator
     */
    public function notEmpty()
    {
        return $this->assertCallback(
            function ($value) {
                return strlen(trim($value)) > 0;
            },
            'is empty'
        );
    }

    /**
     * Assert that each specified variable is an integer.
	 * 断言每个指定变量是一个整数
     *
     * @return \Dotenv\Validator
     */
    public function isInteger()
    {
        return $this->assertCallback(
            function ($value) {
                return ctype_digit($value);
            },
            'is not an integer'
        );
    }

    /**
     * Assert that each specified variable is a boolean.
	 * 断言每个指定变量是一个布尔值
     *
     * @return \Dotenv\Validator
     */
    public function isBoolean()
    {
        return $this->assertCallback(
            function ($value) {
                if ($value === '') {
                    return false;
                }

                return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null;
            },
            'is not a boolean'
        );
    }

    /**
     * Assert that each variable is amongst the given choices.
	 * 断言每个变量都在给定的选项中
     *
     * @param string[] $choices
     *
     * @return \Dotenv\Validator
     */
    public function allowedValues(array $choices)
    {
        return $this->assertCallback(
            function ($value) use ($choices) {
                return in_array($value, $choices);
            },
            'is not an allowed value'
        );
    }

    /**
     * Assert that the callback returns true for each variable.
	 * 断言回调对每个变量都是正确的
     *
     * @param callable $callback
     * @param string   $message
     *
     * @throws \Dotenv\Exception\InvalidCallbackException|\Dotenv\Exception\ValidationException
     *
     * @return \Dotenv\Validator
     */
    protected function assertCallback($callback, $message = 'failed callback assertion')
    {
        if (!is_callable($callback)) {
            throw new InvalidCallbackException('The provided callback must be callable.');
        }

        $variablesFailingAssertion = array();
        foreach ($this->variables as $variableName) {
            $variableValue = $this->loader->getEnvironmentVariable($variableName);
            if (call_user_func($callback, $variableValue) === false) {
                $variablesFailingAssertion[] = $variableName." $message";
            }
        }

        if (count($variablesFailingAssertion) > 0) {
            throw new ValidationException(sprintf(
                'One or more environment variables failed assertions: %s.',
                implode(', ', $variablesFailingAssertion)
            ));
        }

        return $this;
    }
}
