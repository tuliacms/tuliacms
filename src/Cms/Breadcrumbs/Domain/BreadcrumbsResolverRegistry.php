<?php

declare(strict_types=1);

namespace Tulia\Cms\Breadcrumbs\Domain;

/**
 * @author Adam Banaszkiewicz
 */
class BreadcrumbsResolverRegistry implements BreadcrumbsResolverRegistryInterface
{
    /**
     * @var BreadcrumbsResolverInterface[]
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
