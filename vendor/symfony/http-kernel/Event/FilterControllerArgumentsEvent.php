<?php
/**
 * Symfony，组件，Http内核，事件，过滤器控制器参数事件
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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @deprecated since Symfony 4.3, use ControllerArgumentsEvent instead
 */
class FilterControllerArgumentsEvent extends FilterControllerEvent
{
    private $arguments;

    public function __construct(HttpKernelInterface $kernel, callable $controller, array $arguments, Request $request, ?int $requestType)
    {
        parent::__construct($kernel, $controller, $request, $requestType);

        $this->arguments = $arguments;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }
}
