<?php

declare(strict_types=1);

namespace Tulia\Framework\Kernel\Event;

use Symfony\Component\HttpFoundation\Response;
use Tulia\Framework\Http\Request;
use Tulia\Framework\Kernel\HttpKernelInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ViewEvent extends KernelEvent
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @var mixed
     */
    protected $controllerResult;

    /**
     * @param HttpKernelInterface $kernel
     * @param Request             $request
     * @param mixed               $controllerResult
     */
    public function __construct(HttpKernelInterface $kernel, Request $request, $controllerResult)
    {
        parent::__construct($kernel, $request);

        $this->controllerResult = $controllerResult;
    }

    /**
     * @return mixed|Response
     */
    public function getControllerResult()
    {
        return $this->controllerResult;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    /**
     * @return bool
     */
    public function hasResponse(): bool
    {
        return $this->response instanceof Response;
    }
}
