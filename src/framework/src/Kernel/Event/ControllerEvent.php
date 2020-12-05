<?php

declare(strict_types=1);

namespace Tulia\Framework\Kernel\Event;

use Tulia\Framework\Http\Request;
use Tulia\Framework\Kernel\HttpKernelInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ControllerEvent extends KernelEvent
{
    /**
     * @var callable
     */
    protected $controller;

    /**
     * @param HttpKernelInterface $kernel
     * @param Request             $request
     * @param callable            $controller
     */
    public function __construct(HttpKernelInterface $kernel, Request $request, callable $controller)
    {
        parent::__construct($kernel, $request);

        $this->controller = $controller;
    }

    /**
     * @return callable
     */
    public function getController(): callable
    {
        return $this->controller;
    }

    /**
     * @param callable $controller
     */
    public function setController(callable $controller): void
    {
        $this->controller = $controller;
    }
}
