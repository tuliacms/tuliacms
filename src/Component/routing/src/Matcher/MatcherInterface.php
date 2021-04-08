<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Matcher;

use Tulia\Component\Routing\Exception\RoutingException;
use Tulia\Component\Routing\Request\RequestContextInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface MatcherInterface
{
    /**
     * @param string $pathinfo
     * @param RequestContextInterface $context
     *
     * @return array
     *
     * @throws RoutingException
     */
    public function match(string $pathinfo, RequestContextInterface $context): array;
}
