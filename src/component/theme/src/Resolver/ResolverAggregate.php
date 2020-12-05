<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Resolver;

use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ResolverAggregate implements ResolverAggregateInterface
{
    /**
     * @var iterable
     */
    protected $resolvers = [];

    /**
     * @param iterable $resolvers
     */
    public function __construct(iterable $resolvers)
    {
        $this->resolvers = $resolvers;
    }

    /**
     * {@inheritdoc}
     */
    public function addResolver(ResolverInterface $resolver): void
    {
        $this->resolvers[] = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(ThemeInterface $theme): void
    {
        foreach ($this->resolvers as $resolver) {
            $resolver->resolve($theme);
        }
    }
}
