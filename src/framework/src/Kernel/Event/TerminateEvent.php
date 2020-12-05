<?php

declare(strict_types=1);

namespace Tulia\Framework\Kernel\Event;

use Symfony\Component\HttpFoundation\Response;
use Tulia\Framework\Http\Request;
use Tulia\Framework\Kernel\HttpKernelInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TerminateEvent extends KernelEvent
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @param HttpKernelInterface $kernel
     * @param Request             $request
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
}
