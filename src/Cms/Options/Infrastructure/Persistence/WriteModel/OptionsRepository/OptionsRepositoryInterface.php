<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Infrastructure\Persistence\WriteModel\OptionsRepository;

use Tulia\Cms\Options\Domain\WriteModel\Model\Option;

/**
 * @author Adam Banaszkiewicz
 */
interface OptionsRepositoryInterface
{
    public function find(string $websiteId, string $name): ?Option;

    public function create(Option $option): void;

    /**
     * @param string $name
     * @param mixed $value
     * @param string $locale
     * @param string $websiteId
     * @param string $defaultLocale
     */
    public function updateValue(string $name, $value, string $locale, string $websiteId, string $defaultLocale): void;

    /**
     * @param Option[] $options
     */
    public function bulkCreate(array $options): void;

    public function delete(string $name, string $websiteId): void;
}
