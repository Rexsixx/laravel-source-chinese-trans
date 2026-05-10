<?php
/**
 * Mockery，计数验证器，计数验证器抽象
 */

/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/blob/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mockery
 * @package    Mockery
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

namespace Mockery\CountValidator;

abstract class CountValidatorAbstract
{
    /**
     * Expectation for which this validator is assigned
	 * 分配此验证器的期望
     *
     * @var \Mockery\Expectation
     */
    protected $_expectation = null;

    /**
     * Call count limit
	 * 呼叫计数限制
     *
     * @var int
     */
    protected $_limit = null;

    /**
     * Set Expectation object and upper call limit
	 * 设置期望对象和呼出上限
     *
     * @param \Mockery\Expectation $expectation
     * @param int $limit
     */
    public function __construct(\Mockery\Expectation $expectation, $limit)
    {
        $this->_expectation = $expectation;
        $this->_limit = $limit;
    }

    /**
     * Checks if the validator can accept an additional nth call
	 * 检查验证器是否可以接受额外的第n个调用
     *
     * @param int $n
     * @return bool
     */
    public function isEligible($n)
    {
        return ($n < $this->_limit);
    }

    /**
     * Validate the call count against this validator
	 * 根据此验证器验证调用计数
     *
     * @param int $n
     * @return bool
     */
    abstract public function validate($n);
}
