<?php

declare(strict_types=1);

namespace Tulia\Framework\Templating;

use Symfony\Component\HttpFoundation\RequestStack;
use Tulia\Framework\Http\Request;

/**
 * @author Adam Banaszkiewicz
 */
class App
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->requestStack->getCurrentRequest();
    }
}
