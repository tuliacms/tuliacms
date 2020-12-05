<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Framework\Routing\Strategy;

use Tulia\Cms\Taxonomy\Infrastructure\Persistence\Routing\Strategy\TermStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FullPathStrategy implements StrategyInterface
{
    public const NAME = 'full-path';

    /**
     * @var TermStorageInterface
     */
    private $storage;

    /**
     * @param TermStorageInterface $storage
     */
    public function __construct(TermStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(string $id, string $locale): string
    {
        $path = '';
        $term = $this->getTerm($id, $locale);

        while ($term !== null) {
            $path = "/{$term['slug']}" . $path;

            if ($term['parent_id']) {
                $term = $this->getTerm($term['parent_id'], $locale);
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

    private function getTerm(string $id, string $locale): ?array
    {
        return $this->storage->find($id, $locale) ?? null;
    }
}
