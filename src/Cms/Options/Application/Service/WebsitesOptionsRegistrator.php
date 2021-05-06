<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Application\Service;

use Tulia\Cms\Options\Domain\WriteModel\Model\Option;
use Tulia\Cms\Options\Ports\Infrastructure\Persistence\Domain\WriteModel\OptionsRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WebsitesOptionsRegistrator
{
    private RegisteredOptionsRegistry $collector;
    private OptionsRepositoryInterface $repository;

    public function __construct(RegisteredOptionsRegistry $collector, OptionsRepositoryInterface $repository)
    {
        $this->collector = $collector;
        $this->repository = $repository;
    }

    /**
     * Creates all registered options for given website ID.
     * Website must exists to create options for this website.
     *
     * @param string $websiteId
     */
    public function registerMissingOptionsForWebsite(string $websiteId): void
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

        $this->repository->saveBulk($options);
    }

    public function removeOptionsForWebsite(string $websiteId): void
    {
        $options = $this->repository->findAllForWebsite($websiteId);
        $this->repository->deleteBulk($options);
    }
}
