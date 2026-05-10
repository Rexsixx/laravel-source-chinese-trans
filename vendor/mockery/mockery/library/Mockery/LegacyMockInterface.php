<?php
/**
 * Mockery，Legacy 模拟接口
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

namespace Mockery;

interface LegacyMockInterface
{
    /**
     * Alternative setup method to constructor
	 * 构造函数的可选设置方法
     *
     * @param \Mockery\Container $container
     * @param object $partialObject
     * @return void
     */
    public function mockery_init(\Mockery\Container $container = null, $partialObject = null);

    /**
     * Set expected method calls
	 * 设置预期的方法调用
     *
     * @param string|array ...$methodNames one or many methods that are expected to be called in this mock
     *
     * @return \Mockery\ExpectationInterface|\Mockery\Expectation|\Mockery\HigherOrderMessage
     */
    public function shouldReceive(...$methodNames);

    /**
     * Shortcut method for setting an expectation that a method should not be called.
	 * 用于设置不应调用方法的期望的快捷方法。
     *
     * @param string|array ...$methodNames one or many methods that are expected not to be called in this mock
     * @return \Mockery\ExpectationInterface|\Mockery\Expectation|\Mockery\HigherOrderMessage
     */
    public function shouldNotReceive(...$methodNames);

    /**
     * Allows additional methods to be mocked that do not explicitly exist on mocked class
	 * 允许其他的方法被嘲笑,在被嘲笑的类上没有显式地存在。
     * @param String $method name of the method to be mocked
     */
    public function shouldAllowMockingMethod($method);

    /**
     * Set mock to ignore unexpected methods and return Undefined class
     * @param mixed $returnValue the default return value for calls to missing functions on this mock
     * @return Mock
     */
    public function shouldIgnoreMissing($returnValue = null);

    /**
     * @return Mock
     */
    public function shouldAllowMockingProtectedMethods();

    /**
     * Set mock to defer unexpected methods to its parent if possible
	 * 如果可能的话,设置模拟将意想不到的方法推迟到它的父母。
     *
     * @deprecated 2.0.0 Please use makePartial() instead
     *
     * @return Mock
     */
    public function shouldDeferMissing();

    /**
     * Set mock to defer unexpected methods to its parent if possible
	 * 如果可能的话，将mock设置为将意外的方法推迟给它的父方法。
     *
     * @return Mock
     */
    public function makePartial();

    /**
     * @param null|string $method
     * @param null|array|Closure $args
     * @return mixed
     */
    public function shouldHaveReceived($method, $args = null);

    /**
     * @return mixed
     */
    public function shouldHaveBeenCalled();

    /**
     * @param null|string $method
     * @param null|array|Closure $args
     * @return mixed
     */
    public function shouldNotHaveReceived($method, $args = null);

    /**
     * @param array $args (optional)
     * @return mixed
     */
    public function shouldNotHaveBeenCalled(array $args = null);

    /**
     * In the event shouldReceive() accepting an array of methods/returns
     * this method will switch them from normal expectations to default
     * expectations
     *
     * @return self
     */
    public function byDefault();

    /**
     * Iterate across all expectation directors and validate each
     *
     * @throws \Mockery\CountValidator\Exception
     * @return void
     */
    public function mockery_verify();

    /**
     * Tear down tasks for this mock
     *
     * @return void
     */
    public function mockery_teardown();

    /**
     * Fetch the next available allocation order number
     *
     * @return int
     */
    public function mockery_allocateOrder();

    /**
     * Set ordering for a group
     *
     * @param mixed $group
     * @param int $order
     */
    public function mockery_setGroup($group, $order);

    /**
     * Fetch array of ordered groups
     *
     * @return array
     */
    public function mockery_getGroups();

    /**
     * Set current ordered number
     *
     * @param int $order
     */
    public function mockery_setCurrentOrder($order);

    /**
     * Get current ordered number
     *
     * @return int
     */
    public function mockery_getCurrentOrder();

    /**
     * Validate the current mock's ordering
     *
     * @param string $method
     * @param int $order
     * @throws \Mockery\Exception
     * @return void
     */
    public function mockery_validateOrder($method, $order);

    /**
     * Gets the count of expectations for this mock
     *
     * @return int
     */
    public function mockery_getExpectationCount();

    /**
     * Return the expectations director for the given method
     *
     * @var string $method
     * @return \Mockery\ExpectationDirector|null
     */
    public function mockery_setExpectationsFor($method, \Mockery\ExpectationDirector $director);

    /**
     * Return the expectations director for the given method
     *
     * @var string $method
     * @return \Mockery\ExpectationDirector|null
     */
    public function mockery_getExpectationsFor($method);

    /**
     * Find an expectation matching the given method and arguments
     *
     * @var string $method
     * @var array $args
     * @return \Mockery\Expectation|null
     */
    public function mockery_findExpectation($method, array $args);

    /**
     * Return the container for this mock
     *
     * @return \Mockery\Container
     */
    public function mockery_getContainer();

    /**
     * Return the name for this mock
     *
     * @return string
     */
    public function mockery_getName();

    /**
     * @return array
     */
    public function mockery_getMockableProperties();

    /**
     * @return string[]
     */
    public function mockery_getMockableMethods();

    /**
     * @return bool
     */
    public function mockery_isAnonymous();
}
