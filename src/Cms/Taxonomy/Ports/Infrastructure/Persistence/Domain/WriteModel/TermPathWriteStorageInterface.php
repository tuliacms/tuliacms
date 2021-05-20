<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Ports\Infrastructure\Persistence\Domain\WriteModel;

/**
 * @author Adam Banaszkiewicz
 */
interface TermPathWriteStorageInterface
{
    public function remove(string $termId, string $locale): void;

    public function save(string $termId, string $locale, string $path): void;
}
