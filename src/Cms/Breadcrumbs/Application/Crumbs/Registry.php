<?php

declare(strict_types=1);

namespace Tulia\Cms\Breadcrumbs\Application\Crumbs;

/**
 * @author Adam Banaszkiewicz
 */
class Registry implements RegistryInterface
{
    /**
     * @var ResolverInterface[]
     */
    protected iterable $resolvers;

    public function __construct(iterable $resolvers)
    {
        $this->resolvers = $resolvers;
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return iterator_to_array($this->resolvers);
    }
}
