<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Application\Service;

use Tulia\Cms\Options\Domain\WriteModel\Model\Option;
use Tulia\Cms\Options\Infrastructure\Persistence\WriteModel\OptionsRepository\OptionsRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class OptionsCreator
{
    private RegisteredOptionsCollector $collector;
    private OptionsRepositoryInterface $repository;

    public function __construct(RegisteredOptionsCollector $collector, OptionsRepositoryInterface $repository)
    {
        $this->collector = $collector;
        $this->repository = $repository;
    }

    public function create(Option $option): void
    {
        $this->repository->create($option);
    }

    /**
     * @param Option[] $options
     */
    public function bulkCreate(array $options): void
    {
        $this->repository->bulkCreate($options);
    }

    /**
     * Creates all registered options for given website ID.
     * Website must exists to create options for this website.
     *
     * @param string $websiteId
     */
    public function createForWebsite(string $websiteId): void
    {
        $source = $this->collector->collectRegisteredOptions();
        $options = [];

        foreach ($source as $name => $option) {
            $options[] = new Option(
                $websiteId,
                $name,
                $option['value'],
                null,
                $option['multilingual'],
                $option['autoload']
            );
        }

        $this->bulkCreate($options);
    }
}
