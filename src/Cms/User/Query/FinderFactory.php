<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Query;

use Psr\EventDispatcher\EventDispatcherInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\User\Query\Model\Collection;

/**
 * @author Adam Banaszkiewicz
 */
class FinderFactory implements FinderFactoryInterface
{
    protected ConnectionInterface $connection;
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(
        ConnectionInterface $connection,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->connection = $connection;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstance(string $scope, array $params = []): FinderInterface
    {
        $finder = new Finder($this->connection, array_merge([
            'scope' => $scope,
        ], $params));
        $finder->setEventDispatcher($this->eventDispatcher);

        return $finder;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(string $scope, array $criteria): Collection
    {
        $finder = $this->getInstance($scope);
        $finder->setCriteria($criteria);
        $finder->fetch();
        return $finder->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchRaw(string $scope, array $criteria): Collection
    {
        $finder = $this->getInstance($scope);
        $finder->setCriteria($criteria);
        $finder->fetchRaw();
        return $finder->getResult();
    }
}
