<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\Routing\Strategy\Core;

use Tulia\Cms\Taxonomy\Domain\Routing\Strategy\TaxonomyRoutingStrategyInterface;
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

    /**
     * {@inheritdoc}
     */
    public function generate(string $id, string $locale, string $defaultLocale): string
    {
        $path = '';
        $term = $this->storage->find($id, $locale, $defaultLocale);

        while ($term !== null) {
            $path = "/{$term['slug']}" . $path;

            if ($term['parent_id']) {
                $term = $this->storage->find($term['parent_id'], $locale, $defaultLocale);
            } else {
                break;
            }
        }

        return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return self::NAME;
    }
}
