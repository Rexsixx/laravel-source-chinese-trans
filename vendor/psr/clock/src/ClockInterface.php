<?php
/**
 * Psr，时钟，时钟接口
 */

namespace Psr\Clock;

use DateTimeImmutable;

interface ClockInterface
{
    /**
     * Returns the current time as a DateTimeImmutable Object
	 * 返回当前时间作为datetimei不变对象
     */
    public function now(): DateTimeImmutable;
}
