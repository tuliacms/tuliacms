<?php

declare(strict_types=1);

namespace Tulia\Cms\Security\Framework\Security\RequestMatcher;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FrontendRequestMatcher implements RequestMatcherInterface
{
    public function matches(Request $request): bool
    {
        return ((bool) $request->attributes->get('_is_backend', false)) === false;
    }
}
