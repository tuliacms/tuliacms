<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Resolver;

use IteratorAggregate;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ResolverAggregate implements ResolverAggregateInterface
{
    /**
     * @var ResolverInterface[]
     */
    protected $resolvers;

    public function __construct(iterable $resolvers)
    {
        $this->resolvers = $resolvers;
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
