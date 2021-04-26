<?php

declare(strict_types=1);

namespace Tulia\Framework\Kernel\EventListener;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Tulia\Framework\Kernel\Event\ExceptionEvent;
use Tulia\Framework\Kernel\Event\ResponseEvent;
use Tulia\Framework\Kernel\Event\TerminateEvent;

/**
 * @author Adam Banaszkiewicz
 */
class ProfilerListener
{
    protected $profiler;
    protected $exception;
    protected $profiles;
    protected $requestStack;
    protected $parents;

    public function __construct(Profiler $profiler, RequestStack $requestStack)
    {
        $this->profiler = $profiler;
        $this->profiles = new \SplObjectStorage();
        $this->parents = new \SplObjectStorage();
        $this->requestStack = $requestStack;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $this->exception = $event->getThrowable();
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $route = $event->getRequest()->attributes->get('_route');

        // Skip for profiler.
        // @todo Change checking to more professional way instead of checking route ;)
        if ($route && strncmp($route, 'profiler', 8) === 0) {
            return;
        }

        $request = $event->getRequest();
        $exception = $this->exception;
        $this->exception = null;

        if (!$profile = $this->profiler->collect($request, $event->getResponse(), $exception)) {
            return;
        }

        $this->profiles[$request] = $profile;

        $this->parents[$request] = $this->requestStack->getParentRequest();
    }

    public function onKernelTerminate(TerminateEvent $event): void
    {
        // attach children to parents
        foreach ($this->profiles as $request) {
            if (null !== $parentRequest = $this->parents[$request]) {
                if (isset($this->profiles[$parentRequest])) {
                    $this->profiles[$parentRequest]->addChild($this->profiles[$request]);
                }
            }
        }

        // save profiles
        foreach ($this->profiles as $request) {
            $this->profiler->saveProfile($this->profiles[$request]);
        }

        $this->profiles = new \SplObjectStorage();
        $this->parents = new \SplObjectStorage();
    }
}
