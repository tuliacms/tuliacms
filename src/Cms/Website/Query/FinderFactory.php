<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Query;

use Psr\EventDispatcher\EventDispatcherInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
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
        $this->connection      = $connection;
        $this->eventDispatcher = $eventDispatcher;
        $this->storage         = $storage;
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
