<?php
/**
 * Symfony，契约，HttpClient，异常，重定向异常接口
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
 * When a 3xx response is returned and the "max_redirects" option has been reached.
 * 当返回3xx响应并且达到“max_redirects”选项时。
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
interface RedirectionExceptionInterface extends HttpExceptionInterface
{
}
