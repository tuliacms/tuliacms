<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Ports\Infrastructure\Persistence\Domain\WriteModel;

/**
 * @author Adam Banaszkiewicz
 */
interface OptionsStorageInterface
{
    public function find(string $name, string $websiteId, string $locale): ?array;

    public function findAllForWebsite(string $websiteId, string $locale): array;

    public function insert(array $option, string $defaultLocale): void;

    public function update(array $option, string $defaultLocale): void;

    public function delete(array $option): void;
}
