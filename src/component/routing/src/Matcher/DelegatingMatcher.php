<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Matcher;

use Tulia\Component\Routing\Exception\RequestNoMatchedException;
use Tulia\Component\Routing\Exception\RoutingException;
use Tulia\Component\Routing\Request\RequestContextInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Framework\Http\Request;

/**
 * @author Adam Banaszkiewicz
 */
class DelegatingMatcher implements MatcherInterface
{
    /**
     * @var MatcherInterface[]
     */
    private $matchers = [];

    /**
     * @param iterable $matchers
     */
    public function __construct(iterable $matchers)
    {
        $this->matchers = $matchers;
    }

    /**
     * {@inheritdoc}
     */
    public function match(string $pathinfo, RequestContextInterface $context): array
    {
        foreach ($this->matchers as $matcher) {
            try {
                $result = $matcher->match($pathinfo, $context);
            } catch (RoutingException $exception) {
                continue;
            }

            if ($result !== []) {
                return $result;
            }
        }

        throw new RequestNoMatchedException(sprintf('Request %s "%s" not matched by Routing.', $context->getMethod(), $pathinfo));
    }
}
