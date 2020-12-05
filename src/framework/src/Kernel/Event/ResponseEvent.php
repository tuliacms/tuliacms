<?php

declare(strict_types=1);

namespace Tulia\Framework\Kernel\Event;

use Symfony\Component\HttpFoundation\Response;
use Tulia\Framework\Http\Request;
use Tulia\Framework\Kernel\HttpKernelInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ResponseEvent extends KernelEvent
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @param HttpKernelInterface $kernel
     * @param Request             $request
     * @param Response            $response
     */
    public function __construct(HttpKernelInterface $kernel, Request $request, Response $response)
    {
        parent::__construct($kernel, $request);

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
     * @var Response $response
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }
}
