<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\Routing\Strategy\Core;

use Tulia\Cms\Taxonomy\Domain\Routing\Strategy\TaxonomyRoutingStrategyInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Taxonomy;
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

    public function generateFromTaxonomy(Taxonomy $taxonomy, string $id): string
    {
        $term = $taxonomy->getTerm($id);

        if ($term !== null) {
            return "/{$term->getSlug()}";
        }

        return '';
    }

    public function generate(string $id, string $locale, string $defaultLocale): string
    {
        $term = $this->storage->find($id, $locale, $defaultLocale);

        if ($term !== null) {
            return "/{$term['slug']}";
        }

        return '';
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
