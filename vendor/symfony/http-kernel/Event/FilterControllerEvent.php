<?php
/**
 * Symfony，组件，Http内核，事件，过滤器控制器事件
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
 * @deprecated since Symfony 4.3, use ControllerEvent instead
 */
class FilterControllerEvent extends KernelEvent
{
    private $controller;

    public function __construct(HttpKernelInterface $kernel, callable $controller, Request $request, ?int $requestType)
    {
        parent::__construct($kernel, $request, $requestType);

        $this->setController($controller);
    }

    /**
     * Returns the current controller.
     *
     * @return callable
     */
    public function getController()
    {
        return $this->controller;
    }

    public function setController(callable $controller)
    {
        $this->controller = $controller;
    }
}
