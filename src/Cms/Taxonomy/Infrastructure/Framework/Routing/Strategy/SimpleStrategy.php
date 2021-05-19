<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Framework\Routing\Strategy;

use Tulia\Cms\Taxonomy\Infrastructure\Persistence\Routing\Strategy\TermStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SimpleStrategy implements StrategyInterface
{
    public const NAME = 'simple';

    private TermStorageInterface $storage;

    public function __construct(TermStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(string $id, string $locale): string
    {
        $term = $this->storage->find($id, $locale);

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
