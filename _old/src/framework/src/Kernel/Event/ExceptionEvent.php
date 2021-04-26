<?php

declare(strict_types=1);

namespace Tulia\Framework\Kernel\Event;

use Symfony\Component\HttpFoundation\Response;
use Tulia\Framework\Http\Request;
use Tulia\Framework\Kernel\HttpKernelInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ExceptionEvent extends KernelEvent
{
    /**
     * @var Response
     */
    protected $response;
    protected $throwable;

    /**
     * RequestEvent constructor.
     *
     * @param HttpKernelInterface $kernel
     * @param Request             $request
     * @param \Throwable          $throwable
     */
    public function __construct(HttpKernelInterface $kernel, Request $request, \Throwable $throwable)
    {
        parent::__construct($kernel, $request);

        $this->throwable = $throwable;
    }

    /**
     * @return \Throwable
     */
    public function getThrowable(): \Throwable
    {
        return $this->throwable;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @return bool
     */
    public function hasResponse(): bool
    {
        return (bool) $this->response;
    }
}
