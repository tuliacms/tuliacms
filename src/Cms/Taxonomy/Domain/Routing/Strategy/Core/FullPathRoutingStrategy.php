<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\Routing\Strategy\Core;

use Tulia\Cms\Taxonomy\Domain\Routing\Strategy\TaxonomyRoutingStrategyInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Taxonomy;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Term;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\ValueObject\TermId;
use Tulia\Cms\Taxonomy\Ports\Infrastructure\Persistence\Domain\WriteModel\TermWriteStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FullPathRoutingStrategy implements TaxonomyRoutingStrategyInterface
{
    public const NAME = 'full_path';

    private TermWriteStorageInterface $storage;

    public function __construct(TermWriteStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function generateFromTaxonomy(Taxonomy $taxonomy, string $id): string
    {
        $path = '';
        $term = $taxonomy->getTerm(new TermId($id));

        while ($term !== null) {
            $path = "/{$term->getSlug()}" . $path;

            if ($term->getParentId() && $term->getParentId()->getId() !== Term::ROOT_ID) {
                $term = $taxonomy->getTerm($term->getParentId());
            } else {
                break;
            }
        }

        return $path;
    }

    public function generate(string $id, string $locale, string $defaultLocale): string
    {
        $path = '';
        $term = $this->storage->find($id, $locale, $defaultLocale);

        while ($term !== null) {
            $path = "/{$term['slug']}" . $path;

            if ($term['parent_id'] && $term['parent_id'] !== Term::ROOT_ID) {
                $term = $this->storage->find($term['parent_id'], $locale, $defaultLocale);
            } else {
                break;
            }
        }

        return $path;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
