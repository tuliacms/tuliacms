<?php

declare(strict_types=1);

namespace Tulia\Framework\Kernel\Event;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Adam Banaszkiewicz
 */
class RequestEvent extends KernelEvent
{
    /**
     * @var Response
     */
    protected $response;

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
