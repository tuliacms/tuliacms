<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\ReadModel;

use Psr\EventDispatcher\EventDispatcherInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\Website\Domain\ReadModel\Enum\ScopeEnum;
use Tulia\Cms\Website\Domain\ReadModel\Model\Collection;
use Tulia\Component\Routing\Website\Locale\Storage\StorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FinderFactory implements FinderFactoryInterface
{
    protected ConnectionInterface $connection;
    protected EventDispatcherInterface $eventDispatcher;
    protected StorageInterface $storage;

    public function __construct(
        ConnectionInterface $connection,
        EventDispatcherInterface $eventDispatcher,
        StorageInterface $storage
    ) {
        $this->connection = $connection;
        $this->eventDispatcher = $eventDispatcher;
        $this->storage = $storage;
    }

    public function find(array $criteria, string $scope = ScopeEnum::BACKEND_LISTING, array $parameters = []): Collection
    {
        $finder = $this->getInstance($scope, $parameters);
        $finder->setCriteria();
        $finder->fetch();

        return $finder->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getInstance(string $scope, array $params = []): FinderInterface
    {
        $finder = new Finder($this->connection, $this->storage, array_merge([
            'scope' => $scope,
        ], $params));

        $finder->setEventDispatcher($this->eventDispatcher);

        return $finder;
    }
}
