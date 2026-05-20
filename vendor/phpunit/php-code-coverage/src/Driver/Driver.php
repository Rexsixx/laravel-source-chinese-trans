<?php
/**
 * SebastianBergmann，CodeCoverage，驱动程序，Driver
 */

/*
 * This file is part of the php-code-coverage package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Driver;

/**
 * Interface for code coverage drivers.
 * 代码覆盖驱动程序的接口
 */
interface Driver
{
    /**
     * @var int
     *
     * @see http://xdebug.org/docs/code_coverage
     */
    public const LINE_EXECUTED = 1;

    /**
     * @var int
     *
     * @see http://xdebug.org/docs/code_coverage
     */
    public const LINE_NOT_EXECUTED = -1;

    /**
     * @var int
     *
     * @see http://xdebug.org/docs/code_coverage
     */
    public const LINE_NOT_EXECUTABLE = -2;

    /**
     * Start collection of code coverage information.
	 * 开始收集代码覆盖率信息
     */
    public function start(bool $determineUnusedAndDead = true): void;

    /**
     * Stop collection of code coverage information.
     */
    public function stop(): array;
}
