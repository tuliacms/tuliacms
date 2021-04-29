<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Domain\ReadModel\Finder;

use Psr\EventDispatcher\EventDispatcherInterface;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Event\QueryFilterEvent;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Event\QueryPrepareEvent;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Plugin\PluginInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\QueryInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractFinder
{
    protected EventDispatcherInterface $eventDispatcher;
    protected array $plugins = [];

    abstract public function getAlias(): string;
    abstract public function createQuery(): QueryInterface;

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(array $criteria, string $scope = 'default', array $parameters = []): Collection
    {
        [$criteria, $scope, $parameters] = $this->prepareFetch($criteria, $scope, $parameters);
        $query = $this->createQuery();
        $query->setPlugins($this->plugins);

        $result = $query->query($criteria, $parameters);

        return $this->afterQuery($result, $criteria, $scope, $parameters);
    }

    public function addPlugin(PluginInterface $plugin): void
    {
        $this->plugins[] = $plugin;
    }

    protected function prepareFetch(array $criteria, string $scope, array $parameters): array
    {
        $event = new QueryPrepareEvent($criteria, $scope, $parameters);
        $this->eventDispatcher->dispatch($event);

        return [$event->getCriteria(), $event->getScope(), $event->getParameters()];
    }

    protected function afterQuery(Collection $collection, array $criteria, string $scope, array $parameters): Collection
    {
        $event = new QueryFilterEvent($collection, $criteria, $scope, $parameters);
        $this->eventDispatcher->dispatch($event);

        return $event->getCollection();
    }
}
