<?php
/**
 * Symfony，契约，Http客户端，异常，传输异常接口
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Contracts\HttpClient\Exception;

/**
 * When any error happens at the transport level.
 * 当任何错误发生在传输级别时。
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
interface TransportExceptionInterface extends ExceptionInterface
{
}
