<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Infrastructure\Persistence\ReadModel\Options;

/**
 * @author Adam Banaszkiewicz
 */
interface OptionsFinderInterface
{
    public function findByName(string $name, string $locale, string $website);

    public function findBulkByName(array $names, string $locale, string $website): array;
}
