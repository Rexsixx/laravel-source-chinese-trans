<?php
/**
 * Symfony，组件，Http内核，异常，Http异常接口
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Exception;

/**
 * Interface for HTTP error exceptions.
 * HTTP错误异常的接口。
 *
 * @author Kris Wallsmith <kris@symfony.com>
 */
interface HttpExceptionInterface extends \Throwable
{
    /**
     * Returns the status code.
     *
     * @return int An HTTP response status code
     */
    public function getStatusCode();

    /**
     * Returns response headers.
     *
     * @return array Response headers
     */
    public function getHeaders();
}
