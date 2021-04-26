<?php

declare(strict_types=1);

namespace Tulia\Framework\Kernel\Event;

use Psr\EventDispatcher\StoppableEventInterface;
use Tulia\Framework\Http\Request;
use Tulia\Framework\Kernel\HttpKernelInterface;

/**
 * @author Adam Banaszkiewicz
 */
class KernelEvent implements StoppableEventInterface
{
    /**
     * @var bool
     */
    private $propagationStopped = false;

    /**
     * @var HttpKernelInterface
     */
    protected $kernel;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param HttpKernelInterface $kernel
     * @param Request             $request
     */
    public function __construct(HttpKernelInterface $kernel, Request $request)
    {
        $this->kernel  = $kernel;
        $this->request = $request;
    }

    /**
     * @return HttpKernelInterface
     */
    public function getKernel(): HttpKernelInterface
    {
        return $this->kernel;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * {@inheritdoc}
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }
}
