<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\Routing\Strategy\Core;

use Tulia\Cms\Taxonomy\Domain\Routing\Strategy\TaxonomyRoutingStrategyInterface;
use Tulia\Cms\Taxonomy\Ports\Infrastructure\Persistence\Domain\WriteModel\TermWriteStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SimpleRoutingStrategy implements TaxonomyRoutingStrategyInterface
{
    public const NAME = 'simple';

    private TermWriteStorageInterface $storage;

    public function __construct(TermWriteStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(string $id, string $locale, string $defaultLocale): string
    {
        $term = $this->storage->find($id, $locale, $defaultLocale);

        if ($term !== null) {
            return "/{$term['slug']}";
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return self::NAME;
    }
}
