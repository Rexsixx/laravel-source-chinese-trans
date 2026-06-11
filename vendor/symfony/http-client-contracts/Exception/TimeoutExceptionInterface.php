<?php
/**
 * Symfony，契约，HTTP客户端，异常，超时异常接口
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
 * When an idle timeout occurs.
 * 当空闲超时发生时。
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
interface TimeoutExceptionInterface extends TransportExceptionInterface
{
}
