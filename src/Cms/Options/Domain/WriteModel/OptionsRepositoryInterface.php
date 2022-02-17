<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Domain\WriteModel;

use Tulia\Cms\Options\Domain\WriteModel\Exception\OptionNotFoundException;
use Tulia\Cms\Options\Domain\WriteModel\Model\Option;

/**
 * @author Adam Banaszkiewicz
 */
interface OptionsRepositoryInterface
{
    /**
     * @throws OptionNotFoundException
     */
    public function find(string $name): Option;

    /**
     * @return Option[]
     */
    public function findAllForWebsite(string $websiteId): array;

    public function save(Option $option): void;

    /**
     * @param Option[] $options
     */
    public function saveBulk(array $options): void;

    public function update(Option $option): void;

    /**
     * @param Option[] $options
     */
    public function updateBulk(array $options): void;

    public function delete(Option $option): void;

    /**
     * @param Option[] $options
     */
    public function deleteBulk(array $options): void;
}
