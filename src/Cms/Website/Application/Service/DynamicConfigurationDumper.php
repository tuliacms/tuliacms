<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Application\Service;

use Tulia\Cms\Website\Infrastructure\Persistence\Domain\DynamicConfigurationStorage;
use Tulia\Cms\Website\Domain\ReadModel\Enum\ScopeEnum;
use Tulia\Cms\Website\Domain\ReadModel\FinderFactoryInterface;
use Tulia\Cms\Website\Domain\ReadModel\Model\Collection;

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
        $result = $this->factoryFactory->find(['active' => '1'], ScopeEnum::INTERNAL);

        $flattened = $this->flatten($result);
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
