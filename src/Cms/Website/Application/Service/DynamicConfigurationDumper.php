<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Application\Service;

use Tulia\Cms\Website\Infrastructure\Persistence\Domain\DynamicConfigurationStorage;
use Tulia\Cms\Website\Query\Enum\ScopeEnum;
use Tulia\Cms\Website\Query\FinderFactoryInterface;
use Tulia\Cms\Website\Query\Model\Collection;

/**
 * @author Adam Banaszkiewicz
 */
class DynamicConfigurationDumper
{
    private FinderFactoryInterface $factoryFactory;
    private DynamicConfigurationStorage $storage;

    public function __construct(FinderFactoryInterface $factoryFactory, DynamicConfigurationStorage $storage)
    {
        $this->factoryFactory = $factoryFactory;
        $this->storage = $storage;
    }

    public function dump(): void
    {
        $finder = $this->factoryFactory->getInstance(ScopeEnum::INTERNAL);
        $finder->setCriteria([]);
        $finder->fetchRaw();

        $flattened = $this->flatten($finder->getResult());
        $this->storage->save($flattened);
    }

    private function flatten(Collection $collection): array
    {
        $flat = [];

        foreach ($collection->all() as $website) {
            foreach ($website->getLocales() as $locale) {
                $flat[] = [
                    'id' => $website->getId(),
                    'backend_prefix' => $website->getBackendPrefix(),
                    'name' => $website->getName(),
                    'code' => $locale->getCode(),
                    'domain' => $locale->getDomain(),
                    'locale_prefix' => $locale->getLocalePrefix(),
                    'path_prefix' => $locale->getPathPrefix(),
                    'ssl_mode' => $locale->getSslMode(),
                    'default' => $locale->isDefault(),
                ];
            }
        }

        return $flat;
    }
}
