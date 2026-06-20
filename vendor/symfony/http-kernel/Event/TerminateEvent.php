<?php
/**
 * Symfony，组件，Http内核，事件，终止事件
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Event;

/**
 * Allows to execute logic after a response was sent.
 * 允许在发送响应后执行逻辑。
 *
 * Since it's only triggered on master requests, the `getRequestType()` method
 * will always return the value of `HttpKernelInterface::MASTER_REQUEST`.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 *
 * @final since Symfony 4.4
 */
class TerminateEvent extends PostResponseEvent
{
}
