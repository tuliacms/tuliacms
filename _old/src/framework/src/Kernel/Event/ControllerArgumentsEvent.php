<?php

declare(strict_types=1);

namespace Tulia\Framework\Kernel\Event;

use Tulia\Framework\Http\Request;
use Tulia\Framework\Kernel\HttpKernelInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ControllerArgumentsEvent extends KernelEvent
{
    /**
     * @var callable
     */
    protected $controller;

    /**
     * @var array
     */
    protected $arguments;

    /**
     * @param HttpKernelInterface $kernel
     * @param Request             $request
     * @param callable            $controller
     * @param array               $arguments
     */
    public function __construct(HttpKernelInterface $kernel, Request $request, callable $controller, array $arguments)
    {
        parent::__construct($kernel, $request);

        $this->controller = $controller;
        $this->arguments  = $arguments;
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

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @param array $arguments
     */
    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
    }
}
